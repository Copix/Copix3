<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Sylvain VUIDART
 */

/**
 * Actions d'administration sur les liens
 * 
 * @package cms
 * @subpackage heading
 */
class ActionGroupAdminLink extends ActionGroupAbstractAdminHeadingElement {

	/**
	 * Formulaire de modification de l'élément
	 */
	public function processEdit (){
		$ppo = _ppo ();
		$ppo->editedElement = $this->_getEditedElement ();
		$ppo->editId = _request ('editId');
		
		$ppo->TITLE_PAGE = $ppo->editedElement->public_id_hei ? 'Modification d\'un lien' : 'Création d\'un lien';
		return _arPpo ($ppo, 'link.form.php');
	}
	
	/**
	 * Sauvegarde de l'élément
	 */
	public function processValid (){
		$element = $this->_getEditedElement ();
		$oldStatus = $element->status_hei;
		//mise à jour de l'enregistrement en cours de modification
		_ppo (CopixRequest::asArray ('caption_hei', 'description_hei'))->saveIn ($element);
			
		$element->href_link = _request('type_link') == 0 ? _request("href_link") : null;
		$element->module_link = _request('type_link') == 2 ? _request("module_link") : null;
		$element->not_rewritten_link = (_request('not_rewritten_link', 'no') == 'no') ? 0 : 1;
		
        if(_request('type_link') != 1){		
			$element->linked_public_id_hei = null;
			$element->url_link = null;
			$element->caption_link = null;
		} else {
			$extra = explode('#', _request("link"));
			$element->linked_public_id_hei = $extra[0];
			$element->not_rewritten_link = null;
			$element->extra_link = array_key_exists('1', $extra) ? "anchor:".$extra[1] : '';
			$element->caption_link = _request ('caption_link');
			$element->url_link = _request ('url_link');
		}

		$toPlan = _ioClass ('HeadingElementInformationServices')->checkPlanningDates(_request('published_date', false), _request('end_published_date', false));
	   
		//On crée un nouvel élément si  
		// id_lien === null (enregistrement jamais crée)
		if ($element->id_link === null){
			_class ('linkservices')->insert ($element);
		}elseif ($element->status_hei == HeadingElementStatus::DRAFT
				|| ($element->status_hei == HeadingElementStatus::PLANNED && $toPlan)){
			//On modifie directement l'objet si 
			//le statut de l'élément est brouillon
			_class ('linkservices')->update ($element);
		}else{
			//Dans tous les autres cas
			//on crée une nouvelle version de l'élément 
			_class ('linkservices')->version ($element);
		}

		$aParam = array (
			'editId'=>_request ('editId'),
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
	 * Préparation des données à éditer
	 * @TODO rétablir la surcharge (coupée pour simplement modifier l'url d'édition)
	 */
	public function processPrepareEdit (){
		//récupération de l'identifiant de modification
		$editId = _request ('editId');
		
		//Récupération de la classe de service a utiliser
		$type = CopixSession::get ('type_hei', $editId);
		$headingElementType = new HeadingElementType ();
		$typeInformations = $headingElementType->getInformations ($type);
		

		//on regarde le type d'action que l'on souhaite effectuer (création ou modification)
		if (CopixSession::exists ('id_helt', $editId)){
			$toEdit = _class ($typeInformations['classid'])->getById (CopixSession::get ('id_helt', $editId));
		} else {
			$toEdit = _ppo ();
			$toEdit->parent_heading_public_id_hei = CopixSession::get ('heading', $editId);
		}

		//on met l'information à modifier en session
		CopixSession::set ($type.'|edit|record', $toEdit, $editId);

		//redirection vers l'écran de modification
		return _arRedirect (_url ('adminlink|edit', array ('editId'=>$editId)));
	}	
}