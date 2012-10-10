<?php
/**
 * @package     cms
 * @subpackage  document
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Sylvain Vuidart
 */

/**
 * Actions d'administration des éléments rubriques (pas de leurs contenus, gérés dans "element")
 * 
 * @package cms
 * @subpackage document
 */
class ActionGroupAdmin extends ActionGroupAbstractAdminHeadingElement {
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
		$ppo->chooseHeading = false;
		$ppo->popup = false;
		if (CopixSession::get ('then', $ppo->editId, false)){
			$ppo->chooseHeading = true;
			$ppo->popup = true;
			CopixConfig::instance()->mainTemplate = "default|popup.php";
		}

		$ppo->TITLE_PAGE = $ppo->editedElement->public_id_hei && !_request('classic', false) ? 'Modification de document' : 'Création de document';
		return _arPpo ($ppo, $ppo->editedElement->public_id_hei || _request('classic', false) ? 'document.edit.php' : 'document.form.php');
	}

	/**
	 * Sauvegarde de la page
	 *
	 */
	public function processValid (){
		$element = $this->_getEditedElement ();
		$oldStatus = $element->status_hei;
		
		//mise à jour de l'enregistrement en cours de modification
		_ppo (CopixRequest::asArray ('caption_hei', 'description_hei'))->saveIn ($element);
			
	 	if (($fichier = CopixUploadedFile::get ('file_document')) !== false){
			$fileName = uniqid ().$fichier -> getName(); 	
			$element->file_document = $fileName;
			$element->size_document = $fichier->getSize ();
	  	}
	  	
		if (_request("parent_heading_public_id_hei", false)){
	  		$element->parent_heading_public_id_hei = _request("parent_heading_public_id_hei");
	  	}
	  	
	  	$toPlan = _ioClass ('HeadingElementInformationServices')->checkPlanningDates(_request('published_date', false), _request('end_published_date', false));

		//On crée un nouvel élément si  
		// id_document === null (enregistrement jamais crée)
		if ($element->id_document === null){
			if ($element->file_document == ""){
				$ppo = _ppo ();
				$ppo->editedElement = $this->_getEditedElement ();
				$ppo->editId = _request ('editId');
				$ppo->errors = 'Vous devez choisir un fichier';
				$ppo->TITLE_PAGE = 'Création de document';
				return _arPpo ($ppo, 'document.edit.php');
			}
			_class ('DocumentServices')->insert ($element);
		}elseif ($element->status_hei == HeadingElementStatus::DRAFT
			 || ($element->status_hei == HeadingElementStatus::PLANNED && $toPlan)){
			//On modifie directement l'objet si 
			//le statut de l'élément est brouillon
			_class ('DocumentServices')->update ($element);
		}else{
			//Dans tous les autres cas
			//on crée une nouvelle version de l'élément 
			_class ('DocumentServices')->version ($element);
		}

		//si aucune exception n'a été jeté, on upload
		if ($fichier){	
			$fichier->move (COPIX_VAR_PATH.DocumentServices::DOCUMENT_PATH, $element->file_document);
	  	}

		$aParam = array (
			'editId' => _request ('editId'),
			'result'=>'saved',
			'selected'=>array($element->id_helt . '|' . $element->type_hei)
		);

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
			// Possibilité de notifier par email qu'un contenu a été publié
			$headingElementType = new HeadingElementType ();
			$typeInformations = $headingElementType->getInformations ($element->type_hei);
			if (CopixUserPreferences::get($typeInformations['module'].'|'.$element->type_hei.'Notification') == '1') {
                // Previous Action
                $aParam['prevaction'] = 'publish';
            }
		}

		//retour sur l'écran d'admin générale
		return _arRedirect (_url ('heading|element|finalizeEdit', $aParam));
	}
	
	/**
	 * Affiche le fichier d'un document
	 *
	 */
	public function processDownload(){
		$element = $this->_getEditedElement ();
		$path_parts = pathinfo ($element->file_document);
		$extension = (array_key_exists('extension', $path_parts))?$path_parts['extension']:'';		
		return _arFile (COPIX_VAR_PATH.DocumentServices::DOCUMENT_PATH.$element->file_document, array ('filename' => CopixUrl::escapeSpecialChars($element -> caption_hei) . "." . $extension));
	}
	
}