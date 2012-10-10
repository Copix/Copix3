<?php
/**
 * @package     cms
 * @subpackage  articles
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Sylvain Vuidart
 */

/**
 * Actions d'administration des articles
 * 
 * @package cms
 * @subpackage articles
 */
class ActionGroupAdmin extends ActionGroupAbstractAdminHeadingElement {
	/**
	 * Formulaire de modification de l'élément
	 */
	public function processEdit (){
		$ppo = _ppo ();
		$ppo->editedElement = $this->_getEditedElement ();
		$ppo->editId = _request ('editId');

		if ($ppo->editedElement->public_id_hei) {
			$ppo->TITLE_PAGE = 'Modification d\'article';
		} else {
			$ppo->TITLE_PAGE = 'Création d\'article';
			$ppo->editedElement->editor_article = CopixUserPreferences::get ('articles|editor', CmsEditorServices::WYSIWYG_EDITOR);
			
		}
		$ppo->theme = _class('heading|headingelementinformationservices')->getTheme ($ppo->editedElement->public_id_hei ? $ppo->editedElement->public_id_hei : $ppo->editedElement->parent_heading_public_id_hei, $fooParameterIn);
		
		$ppo->chooseHeading = false;
		$ppo->popup = false;
		if (CopixSession::get ('then', $ppo->editId, false)){
			$ppo->popup = true;
			$ppo->chooseHeading = true;
			CopixConfig::instance()->mainTemplate = "default|popup.php";
		}
		return _arPpo ($ppo, 'article.form.php');
	}
	
	/**
	 * Sauvegarde de la page
	 */
	public function processValid (){
		$element = $this->_getEditedElement ();
		$oldStatus = $element->status_hei;
		//mise à jour de l'enregistrement en cours de modification
		_ppo (CopixRequest::asArray ('caption_hei', 'description_hei', 'summary_article', 'content_article', 'editor_article'))->saveIn ($element);
		
		if (empty ($element->content_article)){
			$ppo = _ppo ();
			$ppo->editedElement = $element;
			$ppo->editId = _request ('editId');
			$ppo->error = "Vous devez renseigner un contenu pour l'article";
			$ppo->TITLE_PAGE = $ppo->editedElement->public_id_hei ? 'Modification d\'article' : 'Création d\'article';
			$ppo->chooseHeading = false;
			$ppo->popup = false;
			if (CopixSession::get ('then', $ppo->editId, false)){
				$ppo->popup = true;
				$ppo->chooseHeading = true;
				CopixConfig::instance()->mainTemplate = "default|popup.php";
			}
			return _arPpo ($ppo, 'article.form.php');
		}
		
		if (_request("parent_heading_public_id_hei", false)){
	  		$element->parent_heading_public_id_hei = _request("parent_heading_public_id_hei");
	  	}
	  	
	  	$toPlan = _ioClass ('HeadingElementInformationServices')->checkPlanningDates(_request('published_date', false), _request('end_published_date', false));
	   
		//On crée un nouvel élément si  
		// id_article === null (enregistrement jamais crée)
		if ($element->id_article === null){
			_class ('ArticleServices')->insert ($element);
		}elseif ($element->status_hei == HeadingElementStatus::DRAFT
				 || ($element->status_hei == HeadingElementStatus::PLANNED && $toPlan)){
			//On modifie directement l'objet si 
			//le statut de l'élément est brouillon
			_class ('ArticleServices')->update ($element);
		}else{
			//Dans tous les autres cas
			//on crée une nouvelle version de l'élément 
			_class ('ArticleServices')->version ($element);
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

	  	//retour sur le module heading|admin
	  	return _arRedirect (_url ('heading|element|finalizeEdit', $aParam));
	}
	
	public function processChangeEditor (){
		$element = $this->_getEditedElement ();
		_ppo (CopixRequest::asArray ('caption_hei', 'description_hei', 'summary_article', 'content_article', 'editor_article'))->saveIn ($element);
		CopixUserPreferences::set ('articles|editor', $element->editor_article);
		return $this->processEdit ();
	}
}