<?php
/**
 * @package     cms
 * @subpackage  cms_rss
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Sylvain Vuidart
 */

/**
 * Actions d'administration des flux rss
 * 
 * @package cms
 * @subpackage cms_rss
 */
class ActionGroupAdmin extends ActionGroupAbstractAdminHeadingElement {
	/**
	 * Formulaire de modification de l'élément
	 */
	public function processEdit (){
		$ppo = _ppo ();
		$ppo->editedElement = $this->_getEditedElement ();
		$ppo->availableTypeRss = array();
		$elType = new HeadingElementType();
		$listElementType = $elType->getList(); 
		foreach ($listElementType as $key => $infos){
			if($infos['canrss'] == true){
				$ppo->availableTypeRss[$key] = $infos['caption'];
			}
		}
		
		$ppo->editId = _request ('editId');

		if ($ppo->editedElement->public_id_hei) {
			$ppo->TITLE_PAGE = 'Modification du flux RSS';
		} else {
			$ppo->TITLE_PAGE = 'Création d\'un flux RSS';			
		}
		return _arPpo ($ppo, 'rss.form.php');
	}
	
	/**
	 * Sauvegarde de la page
	 */
	public function processValid (){
		$element = $this->_getEditedElement ();
		$oldStatus = $element->status_hei;
		$tosave = CopixRequest::asArray ('caption_hei', 'description_hei', 'heading_public_id_rss', 'order_rss');
		$tosave['recursive_flag'] = _request('recursive_flag', 0);
		$tosave['element_types_rss'] = join(',', _request('element_types', array()));
		
		//mise à jour de l'enregistrement en cours de modification
		_ppo ($tosave)->saveIn ($element);

		$ppo = _ppo ();
		if ($element->heading_public_id_rss == ""){
			$ppo->errors[] = 'Vous devez choisir une rubrique d\'articles';
		}
		if ($element->description_hei == ""){
			$ppo->errors[] = 'Vous devez renseigner une description';
		}
		if ($element->element_types_rss == ""){
			$ppo->errors[] = 'Vous devez renseigner un type d\'élément';
		}
		if (isset($ppo->errors)){
			$ppo->editedElement = $element;
			$ppo->editId = _request ('editId');	
			
			if ($ppo->editedElement->public_id_hei) {
				$ppo->TITLE_PAGE = 'Modification du flux RSS';
			} else {
				$ppo->TITLE_PAGE = 'Création d\'un flux RSS';			
			}
			$elType = new HeadingElementType();
			$listElementType = $elType->getList(); 
			foreach ($listElementType as $key => $infos){
				if($infos['canrss'] == true){
					$ppo->availableTypeRss[$key] = $infos['caption'];
				}
			}
			return _arPpo ($ppo, 'rss.form.php');
		}
			
		$toPlan = _ioClass ('HeadingElementInformationServices')->checkPlanningDates(_request('published_date', false), _request('end_published_date', false));
	   
		//On crée un nouvel élément si  
		// id_rss === null (enregistrement jamais crée)
		if ($element->id_rss === null){
			_class ('rssServices')->insert ($element);
		}elseif ($element->status_hei == HeadingElementStatus::DRAFT
			 || ($element->status_hei == HeadingElementStatus::PLANNED && $toPlan)){
			//On modifie directement l'objet si 
			//le statut de l'élément est brouillon
			_class ('rssServices')->update ($element);
		}else{
			//Dans tous les autres cas
			//on crée une nouvelle version de l'élément 
			_class ('rssServices')->version ($element);
		}

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
			if (CopixUserPreferences::get ($typeInformations['module'] . '|' . $element->type_hei . 'Notification') == '1') {
                $aParam['prevaction'] = 'publish';
            }
		}

	  	//retour sur le module heading|admin
	  	return _arRedirect (_url ('heading|element|finalizeEdit', array ('editId' => _request ('editId'), 'result'=>'saved', 'selected'=>array($element->id_helt . '|' . $element->type_hei))));
	}
}