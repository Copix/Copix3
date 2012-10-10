<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Gérald Croës
 */

/**
 * Actions d'administration des éléments rubriques (pas de leurs contenus, gérés dans "element")
 * 
 * @package cms
 * @subpackage heading
 */
class ActionGroupAdmin extends ActionGroupAbstractAdminHeadingElement {

	/**
	 * Formulaire de modification de l'élément
	 */
	public function processEdit (){
		$ppo = _ppo ();
		$ppo->editedElement = $this->_getEditedElement ();
		$ppo->editId = _request ('editId');

		$ppo->TITLE_PAGE = $ppo->editedElement->public_id_hei ? 'Modification de rubrique' : 'Création de rubrique';
		return _arPpo ($ppo, 'heading.form.tpl');
	}

	/**
	 * Validation des modifications
	 */
	public function processValid (){
		$element = $this->_getEditedElement ();

		//mise à jour de l'enregistrement en cours de modification
		_ppo (CopixRequest::asArray ('caption_hei', 'title_hei', 'description_hei', 'home_heading'))->saveIn ($element);

		//Sauveagrde d'un nouvel élément ou modification de l'élément existant
		if ($element->id_heading === null){
			_class ('headingservices')->insert ($element);
		}else{
			_class ('headingservices')->update ($element);
		}

		//supression de l'enregistrement en session
		//CopixSession::delete ('heading|edit|record', _request ('editId'));

		//retour sur l'écran d'admin générale
		$public_id = (CopixUserPreferences::get ('heading|redirectCreateHeading') == 1) ? $element->public_id_hei : $element->parent_heading_public_id_hei;
		return _arRedirect (_url ('element|', array ('heading' => $public_id, 'selected' => array ($element->id_helt . '|heading'))));
	}
}