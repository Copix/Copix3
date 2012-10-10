<?php
/**
 * @package		tutorials
 * @subpackage	tags_demo
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */


/**
 * Pages par défaut pour le module 
 * @package		tutorials
 * @subpackage	tags_demo
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
	 * Liste des tags ou il existe une démonstration
	 */
	public function processDefault (){
		$arTags = array ('copixpicture', 'calendar', 'checkbox', 'popupinformation', 'i18n', 'inputtext', 'radiobutton', 'select','multipleselect', 'ulli', 'htmleditor', 'selectslider', 'betweenselectslider');
		return _arPpo (new CopixPPO (array ('TITLE_PAGE'=>'Liste des démonstrations', 'arTags'=>$arTags)), 'tags.list.tpl');
	}
	
	/**
	 * Démo sur copix picture
	 */
	public function processCopixPicture (){
		return _arPpo (new CopixPPO (array ('TITLE_PAGE'=>'Utilisation de copix picture')), 'copixpicture.tpl');
	}
	
	/**
	 * Démo sur calendar
	 */
	public function processCalendar (){
		return _arPpo (new CopixPPO (array ('TITLE_PAGE'=>'Utilisation de calendar')), 'calendar.tpl');
	}
	
	/**
	 * Démo avec ULLI
	 */
	public function processULLI (){
		$arULLI = array ('1', array ('21', '22', '23'), '3');
		return _arPpo (new CopixPPO (array ('TITLE_PAGE'=>'Utilisation de ulli', 'arULLI'=>$arULLI)), 'ulli.tpl');
	}
	
	/**
	 * Démo avec popupinformations
	 */
	public function processPopupInformation (){
		return _arPpo (new CopixPPO (array ('TITLE_PAGE'=>'Utilisation de PopupInformation')), 'popupinformation.tpl');
	}
	
	/**
	 * Demo avec le tag select
	 */
	public function processSelect (){
		$arObjects = array ();

		$obj = new StdClass ();
		$obj->id = '1';
		$obj->caption = 'libellé 1';
		$arObjects[] = $obj;

		$obj = new StdClass ();
		$obj->id = '2';
		$obj->caption = 'libellé 2';
		$arObjects[] = $obj;

		return _arPpo (new CopixPPO (array ('TITLE_PAGE'=>'Utilisation de select', 'arObjects'=>$arObjects, 'iteratorObjects'=>new ArrayIterator ($arObjects))), 'select.tpl');
	}
	/**
	 * Demo avec le tag multipleselect
	 */
	public function processMultipleSelect (){
		$arObjects = array ();

		$obj = new StdClass ();
		$obj->id = '1';
		$obj->caption = 'libellé 1';
		$arObjects[] = $obj;

		$obj = new StdClass ();
		$obj->id = '2';
		$obj->caption = 'libellé 2';
		$arObjects[] = $obj;

		return _arPpo (new CopixPPO (array ('TITLE_PAGE'=>'Utilisation de multipleselect', 'arObjects'=>$arObjects, 'iteratorObjects'=>new ArrayIterator ($arObjects))), 'multipleselect.tpl');
	}
	
	/**
	 * Démo sur radiobutton
	 */
	public function processRadioButton (){
		$arObjects = array ();

		$obj = new StdClass ();
		$obj->id = '1';
		$obj->caption = 'libellé 1';
		$arObjects[] = $obj;

		$obj = new StdClass ();
		$obj->id = '2';
		$obj->caption = 'libellé 2';
		$arObjects[] = $obj;
		
		return _arPpo (new CopixPPO (array ('TITLE_PAGE'=>'Utilisation de radio boutton', 'arObjects'=>$arObjects, 'iteratorObjects'=>new ArrayIterator ($arObjects))), 'radiobutton.tpl');
	}
	
	/**
	 * Demo avec le tag checkbox
	 */
	public function processCheckbox (){
		$arObjects = array ();

		$obj = new StdClass ();
		$obj->id = '1';
		$obj->caption = 'libellé 1';
		$arObjects[] = $obj;

		$obj = new StdClass ();
		$obj->id = '2';
		$obj->caption = 'libellé 2';
		$arObjects[] = $obj;

		return _arPpo (new CopixPPO (array ('TITLE_PAGE'=>'Utilisation d\'un checkbox', 'arObjects'=>$arObjects, 'iteratorObjects'=>new ArrayIterator ($arObjects))), 'checkbox.tpl');
	}
	
	/**
	 * Démo avec inputtext
	 */
	public function processInputText (){
		return _arPpo (new CopixPPO (array ('TITLE_PAGE'=>'Utilisation de InputText')), 'inputtext.tpl');
	}
	
	/**
	 * Démo avec htmleditor
	 */
	public function processHtmlEditor (){
		return _arPpo (new CopixPPO (array ('TITLE_PAGE'=>'Utilisation de HTMLEditor')), 'htmleditor.tpl');
	}
	
	/**
	 * Démo avec i18n
	 */
	public function processI18n (){
		return _arPpo (new CopixPPO (array ('TITLE_PAGE'=>'Utilisation de l\'i18n')), 'i18n.tpl');
	}
	
	public function processSelectSlider (){
		return _arPpo (_ppo ('TITLE_PAGE=>Utilisation du plugin Mootools SelectSlider'), 'selectslider.tpl');
	}
	
	public function processBetweenSelectSlider (){
		return _arPpo (_ppo ('TITLE_PAGE=>Utilisation du plugin Mootools BeetweenSelectSlider'), 'selectbetweenslider.tpl');
	}
}
?>