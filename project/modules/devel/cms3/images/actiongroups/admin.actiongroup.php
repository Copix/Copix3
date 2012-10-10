<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Sylvain Vuidart
 */
 
/**
 * Actions d'administration générales des images
 * 
 * @package cms
 * @subpackage images
 */
class ActionGroupAdmin extends ActionGroupAbstractAdminHeadingElement  {
	protected function _beforeAction ($pAction) {
		parent::_beforeAction ($pAction);
		_notify ('breadcrumb', array ('path' => array ('#' => 'Ajouter une image')));
	}

	/**
	 * Prépare l'édition d'un document
	 *
	 * @return CopixActionReturn
	 */
	public function processPrepareEdit () {
		parent::processPrepareEdit ();
		$form = CopixUserPreferences::get ('document|uploadForm');
		return _arRedirect (_url ('admin|edit', array ('editId' => _request ('editId'), 'classic' => ($form == 'classic'))));
	}

	/**
	 * Formulaire de modification de l'élément
	 */
	public function processEdit (){
		$ppo = _ppo ();
		$ppo->editedElement = $this->_getEditedElement ();
		$ppo->editId = _request ('editId');
		
		$ppo->TITLE_PAGE = ($ppo->editedElement->public_id_hei) ? 'Modification d\'image' : 'Création d\'image';
		$ppo->chooseHeading = false;
		$ppo->popup = false;
		if (CopixSession::get ('then', $ppo->editId, false)){
			$ppo->popup = true;
			$ppo->chooseHeading = true;
			CopixConfig::instance()->mainTemplate = "default|popup.php";
		}
		return _arPpo ($ppo, $ppo->editedElement->public_id_hei || _request('classic', false) ? 'images.edit.php' : 'images.form.php');
	}

	/**
	 * Sauvegarde de la page
	 *
	 * @return unknown
	 */
	public function processValid (){
		//aperçu de l'image lors de la saisie simple
		if (_request ('preview')) {
			return $this->processApercuImage ();
		}
		
		$element = $this->_getEditedElement ();
		$oldStatus = $element->status_hei;
		//mise à jour de l'enregistrement en cours de modification
		_ppo (CopixRequest::asArray ('caption_hei', 'description_hei'))->saveIn ($element);
	  	
	  	if (_request("parent_heading_public_id_hei", false)){
	  		$element->parent_heading_public_id_hei = _request("parent_heading_public_id_hei");
	  	}
	  	
	  	$fileNameBeforeNewVersion = $element -> file_image;
	  	
	  	$toPlan = _ioClass ('HeadingElementInformationServices')->checkPlanningDates(_request('published_date', false), _request('end_published_date', false));
	   
		//On crée un nouvel élément si  
		// id_image === null (enregistrement jamais crée)
		if ($element->id_image === null){
			if (!$element->file_image){
				return _arRedirect (_url ('images|admin|edit', array ('editId' => _request ('editId'), 'classic' => _request ('classic'))));
			}
			_class ('ImageServices')->insert ($element);
		}elseif ($element->status_hei == HeadingElementStatus::DRAFT
				 || ($element->status_hei == HeadingElementStatus::PLANNED && $toPlan)){
			//On modifie directement l'objet si 
			//le statut de l'élément est brouillon
			_class ('ImageServices')->update ($element);
		}else{
			//Dans tous les autres cas
			//on crée une nouvelle version de l'élément 
			_class ('ImageServices')->version ($element);
		}

  		//on supprime le cache sur cette image si il y en a
  		CopixFile::removeDir(COPIX_CACHE_PATH.ImageServices::IMAGE_PATH.$element->id_helt.'/');
  		CopixFile::createDir(COPIX_VAR_PATH.ImageServices::IMAGE_PATH);
  		rename(COPIX_CACHE_PATH.ImageServices::IMAGE_PATH._request('editId')."/".$fileNameBeforeNewVersion, COPIX_VAR_PATH.ImageServices::IMAGE_PATH.$element->file_image);

		//planification
		if($toPlan){
			$published_date_hei = _request('published_date', false) ? CopixDateTime::DateTimeToyyyymmddhhiiss(_request('published_date')) : null;
			$end_published_date_hei = _request('end_published_date', false) ? CopixDateTime::DateTimeToyyyymmddhhiiss(_request('end_published_date')) : null;
			
			//si on part d'une version publiée sur laquelle on veut juste ajouter une date d'archivage, on publie d'abord la nouvelle version
			if($oldStatus == HeadingElementStatus::PUBLISHED && !$published_date_hei && $end_published_date_hei){
				_ioClass ('HeadingElementInformationServices')->publishById ($element->id_helt, $element->type_hei);
			}
			//on planifie
			_ioClass ('HeadingElementInformationServices')->planById ($element->id_helt, $element->type_hei, $published_date_hei, $end_published_date_hei);
		}
		//publication
		else if (CopixRequest::getBoolean ('publish')) {
			_ioClass ('HeadingElementInformationServices')->publishById ($element->id_helt, $element->type_hei);
		}
	  	
	  	//retour sur le module heading|admin
	  	return _arRedirect (_url ('heading|element|finalizeEdit', array ('editId' => _request ('editId'), 'result'=>'saved', 'selected'=>array($element->id_helt . '|' . $element->type_hei))));
	}
	
	/**
	 * Affiche le fichier d'un document
	 *
	 */
	public function processDownload(){
		$element = $this->_getEditedElement ();
		$path_parts = pathinfo($element -> file_image);
		$extension = $path_parts['extension'];	
		return _arFile (COPIX_VAR_PATH.ImageServices::IMAGE_PATH.$element -> file_image, array ('filename' => CopixUrl::escapeSpecialChars($element -> caption_hei) . "." . $extension));
	}	
	
	public function processApercuImage(){
		$ppo = new CopixPPO();
        $iSize = null;
        $element = $this->_getEditedElement ();
		if (($fichier = CopixUploadedFile::get ('file_image')) !== false){
			$fichier -> move (COPIX_CACHE_PATH.ImageServices::IMAGE_PATH._request('editId')."/");
			$element = $this->_getEditedElement (); 
			$fileName = $fichier -> getName ();
			$element->size_image = $fichier->getSize ();
			$element->file_image = $fichier -> getName ();
        	$editId = _request ('editId');
	  	}
	  	$element->caption_hei = _request("caption_hei");
	  	$element->description_hei = _request("description_hei");
		return _arRedirect (_url ('admin|edit', array ('editId' => _request ('editId'), 'classic' => 1)));
	}
	
	/**
	 * Affichage de l'image en cache pour l'élément en cours de modification
	 */
	public function processShowCacheImageFile(){
		$editedElement = $this->_getEditedElement (); 
		return _arFile (COPIX_CACHE_PATH.ImageServices::IMAGE_PATH._request('editId')."/".$editedElement->file_image);
	}
	
	public function processEditTempImage(){
		$editedElement = $this->_getEditedElement (); 
		if(!file_exists(COPIX_CACHE_PATH.ImageServices::IMAGE_PATH._request('editId')."/".$editedElement->file_image)){
			CopixFile::createDir(COPIX_CACHE_PATH.ImageServices::IMAGE_PATH._request('editId')."/");
			copy(COPIX_VAR_PATH.ImageServices::IMAGE_PATH.$editedElement -> file_image, COPIX_CACHE_PATH.ImageServices::IMAGE_PATH."/"._request('editId')."/".$editedElement->file_image);
		}
		if (_request('submitImageEditor')){
			$image = CopixImage::load(COPIX_CACHE_PATH.ImageServices::IMAGE_PATH._request('editId')."/".$editedElement->file_image);
			if(_request('moveleft')){
				$image->rotate(90);
			} else if(_request('moveright')){
				$image->rotate(270);
			} 
			else if(_request('doblackwhite')){
				$image->blackAndWhite();
			} else {
				if (_request('x') || _request('y')){
					$image->crop(_request('width'), _request('height'), _request('x'), _request('y'));
				} else {
					$image->resize(_request('width'), _request('height'));
				}
			}
			$image->save();
		}
		
		$ppo = _ppo();
		$ppo->path = COPIX_CACHE_PATH.ImageServices::IMAGE_PATH._request('editId')."/";
		$ppo->src = _url('images|admin|showCacheimagefile', array('editId'=>_request('editId'), 'v'=>uniqid()));
		$ppo->editedElement = $editedElement;
		return _arPPO($ppo, array('template'=>'imageeditor.php', 'mainTemplate'=>'default|popup.php'));
	}
	
	
	public function processConvertDiapoToImage(){
		CopixRequest::assert ('editId', 'portal_id');
		$isPage = true;
		
		$portlet = CopixSession::get('portal|'._request ('portal_id'), _request('editId'));
		if ($portlet == null){
			$portlet = $this->_getEditedElement ();
			$isPage = false;
		}
		
		$portlet = $portlet->cloneToImage();
		$portlet->setEtat (Portlet::UPDATED);
		if($isPage){
			$page = CopixSession::get('page|edit|record', _request('editId'));
			// met à jout la page
			$page->refreshPortletType($portlet);
			// met à jour la session
			$portlet = $page->getPortlet($portlet->getRandomId());
			CopixSession::set('portal|'._request ('portal_id'),$portlet, _request('editId'));
			return _arRedirect (_url ('portal|admin|edit', array ('editId'=>_request ('editId'), 'mode'=>'edit'))."#".$portlet->getRandomId ());
		}else{
			$editId = _request ('editId');
			$type = CopixSession::get ('type_hei', $editId);
			CopixSession::set ($type.'|edit|record', $portlet, $editId);
			return _arRedirect (_url ('portal|adminportlet|edit', array ('editId'=>_request ('editId'), 'mode'=>'edit'))."#".$portlet->getRandomId ());
		}	
	}
	
	
	public function processConvertImageToDiapo(){
		CopixRequest::assert ('editId', 'portal_id');
		$isPage = true;
		
		$portlet = CopixSession::get('portal|'._request ('portal_id'), _request('editId'));
		if ($portlet == null){
			$portlet = $this->_getEditedElement ();
			$isPage = false;
		}
		$portlet = $portlet->cloneToDiaporama();
		$portlet->setEtat (Portlet::UPDATED);
		if($isPage){
			// met à jout la page
			$page = CopixSession::get('page|edit|record', _request('editId'));
			$page->refreshPortletType($portlet);
			// met à jour la session			
			$portlet = $page->getPortlet($portlet->getRandomId());
			CopixSession::set('portal|'._request ('portal_id'),$portlet, _request('editId'));
			return _arRedirect (_url ('portal|admin|edit', array ('editId'=>_request ('editId'), 'mode'=>'edit'))."#".$portlet->getRandomId ());
		}else{
			$editId = _request ('editId');
			$type = CopixSession::get ('type_hei', $editId);
			CopixSession::set ($type.'|edit|record', $portlet, $editId);
			return _arRedirect (_url ('portal|adminportlet|edit', array ('editId'=>_request ('editId'), 'mode'=>'edit'))."#".$portlet->getRandomId ());
		}	
	}
	
}