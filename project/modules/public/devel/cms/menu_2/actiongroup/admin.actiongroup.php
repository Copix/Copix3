<?php
/**
* @package	cms
* @subpackage menu_2
* @author	Sylvain DACLIN
* @copyright 2001-2006 CopixTeam
* @link		http://www.copix.org
* @license  http://www.gnu.org/licenses/lgpl.htmlGNU Leser General Public Licence, see LICENCE file
*/

/**
 * @package	cms
 * @subpackage menu_2
 *  * Admin ActionGroup for menu.
 *
 * Handle menu administration and configuration, with cut and paste, create, edit, delete, putOnLine|putOffLine
 */
class ActionGroupAdmin extends CopixActionGroup {
	/**
   * Gets the list of menu.
   * PROFILE_CCV_SHOW required on this menu
   * @param this->vars['id_menu'] == the level we wants to administrate
   */
	function getAdmin () {

		//$tpl = & new CopixTpl ();
		//$tpl->assign ('TITLE_PAGE', CopixI18N::get ('menu.titlePage.admin'));
		$level = CopixRequest::get('id_menu',null,true);

		// Get Menu and profilePath info
		$dao = CopixDAOFactory::getInstanceOf ('menu_2|Menu');
		$menu = $dao->get($level);
		$menu->profilePath = $dao->getProfilePath($level);

		//do we have write permissions on the destination ?
		if (CopixUserProfile::valueOf ($menu->profilePath,'menu_2') < PROFILE_CCV_SHOW) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('menu.error.rightmissing.admin'),
			'back'=>CopixUrl::get ()));
		}

		//$tpl->assign ('MAIN', CopixZone::process ('Menu_2AdminHeading', array('id_menu'=>$level)));

		//return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'menu_2', 'id_menu'=>$level, 'id_head'=>CopixRequest::get('id_head',null,true))));
}

	/**
    * prepare a new menu item to edit.
    * PROFILE_CCV_ADMIN required on father menu
    */
	function doCreate (){
		$father_menu = CopixRequest::get('father_menu',null,true);

		// Get Menu and profilePath info
		$dao = CopixDAOFactory::getInstanceOf ('menu_2|Menu');
		$menu = $dao->get($father_menu);
		$menu->profilePath=$dao->getProfilePath($father_menu);

		// do we have admin permissions on the destination ?
		if (CopixUserProfile::valueOf ($menu->profilePath,'menu_2') < PROFILE_CCV_ADMIN) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('menu.error.rightmissing.admin'),
			'back'=>CopixUrl::get ()));
		}

		$menu = & CopixDAOFactory::createRecord ('Menu');
		$menu->father_menu = $father_menu;
		$menu->typelink_menu = 'string';
		$menu->isonline_menu = 0;
		$menu->width_menu = 0;
		$menu->height_menu = 0;
        if ($father_menu == null) {
            $menu->id_head = CopixRequest::get('id_head',null,true);
        }
		$this->_setSessionMenu($menu);
		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'menu_2', 'id_head'=>CopixRequest::get('id_head',null,true))));
	}

	/**
    * prepare the menu item to be edited.
    * PROFILE_CCV_ADMIN required on this menu
    * check if we were given the menu id to edit, then try to get it.
    */
	function doPrepareEdit (){
		//check for the id.....
		if (CopixRequest::get('id_menu',null,true)==null){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('menu.error.unableGetBack'),
			'back'=>CopixUrl::get ('menu_2|admin|')));
		}

		$dao = CopixDAOFactory::getInstanceOf ('Menu');

		// Get Menu and profilePath info
		$menu = $dao->get(CopixRequest::get('id_menu',null,true));
		$menu->profilePath=$dao->getProfilePath(CopixRequest::get('id_menu',null,true));

		// do we have admin permissions on the destination ?
		if (CopixUserProfile::valueOf ($menu->profilePath,'menu_2') < PROFILE_CCV_ADMIN) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('menu.error.rightmissing.admin'),
			'back'=>CopixUrl::get ()));
		}

		//does the menu exists ?
		if (($menu = $dao->get (CopixRequest::get('id_menu',null,true))) == false){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('menu.error.unableGetBack'),
			'back'=>CopixUrl::get ('menu_2|admin|')));
		}
		$this->_setSessionMenu($menu);

		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'menu_2', 'id_head'=>CopixRequest::get('id_head',null,true))));
//		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('menu_2|admin|edit'));
	}

	/**
    * gets the edit page for the menu item.
    */
	function getEdit (){
		if (!$toEdit = $this->_getSessionMenu()){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('menu.error.unableGetBack'),
			'back'=>CopixUrl::get ('menu_2|admin|')));
		}
		$tpl = new CopixTpl ();
		$tpl->assign ('TITLE_PAGE', strlen ($toEdit->id_menu) >= 1 ? CopixI18N::get('menu.title.update') : CopixI18N::get('menu.title.create'));
		$tpl->assign ('MAIN', CopixZone::process ('MenuEdit', array ('toEdit'=>$toEdit, 'e'=>isset ($this->vars['e']), 'id_head'=>CopixRequest::get('id_head',null,true))));
		return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
	}

	/**
    * apply updates on the edited menu element.
    * Saves to the database if ok
    * PROFILE_CCV_ADMIN required on father
    */
	function doValid (){
		if (!$toValid = $this->_getSessionMenu()){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('menu.error.unableGetBack'),
			'back'=>CopixUrl::get ('menu_2|admin|')));
		}

		if (CopixConfig::get ('menu_2|useCache') == 1){
			CopixCache::clear ();
		}
		
		// Get Menu and profilePath info
		$dao           = CopixDAOFactory::getInstanceOf ('menu_2|Menu');
		$id_menu_right = (isset($toValid->id_menu))? $toValid->id_menu : $toValid->father_menu; // if creating : father right is checked else current edited menu right is checked
		$profilePath   = $dao->getProfilePath($id_menu_right);

		// do we have admin permissions on the father menu ?
		if (CopixUserProfile::valueOf ($profilePath,'menu_2') < PROFILE_CCV_ADMIN) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('menu.error.rightmissing.admin'),
			'back'=>CopixUrl::get ()));
		}

		//Apply the changes on the edited object
		$this->_validFromForm($toValid);

		// If menu has not got a position, he get max ordre +1
		$dao = CopixDAOFactory::getInstanceOf ('Menu');
		if (! is_numeric($toValid->order_menu)) {
			$toValid->order_menu = $dao->getNewPos($toValid->father_menu);
		}

		if (! $dao->check ($toValid)){
			$this->_setSessionMenu($toValid);
            return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'menu_2', 'e'=>1, 'id_head'=>CopixRequest::get('id_head',null,true))));
		} else {
			//modif ou création selon le cas.
			if ($toValid->id_menu !== null){
				$dao->update($toValid);
				CopixProfileTools::updateCapabilityPathDescription ($dao->getProfilePath($toValid->id_menu), $toValid->caption_menu);
			}else{
				// Insertion du nouveau menu
				$dao->insertWithCapability($toValid);
			}
			//retour sur la page de liste.
         $this->_setSessionMenu(null);
         return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'menu_2', 'id_menu'=>$toValid->father_menu, 'id_head'=>CopixRequest::get('id_head',null,true))));
		}
	}

	/**
    * Cancel the edition...... empty the session data
    */
	function doCancelEdit (){
		$menu = $this->_getSessionMenu();
		$this->_setSessionMenu(null);
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'menu_2', 'id_menu'=>$menu->father_menu, 'id_head'=>CopixRequest::get('id_head',null,true))));
	}

	/**
    * Selects destination CMSPage 
    */
	function getSelectPage (){
		$tpl = new CopixTpl ();
		if (!$toValid = $this->_getSessionMenu()){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('menu.error.unableGetBack'),
			'back'=>CopixUrl::get ('menu_2|admin|')));
		}
		$this->_validFromForm($toValid);
		$this->_setSessionMenu ($toValid);
		$tpl->assign ('TITLE_PAGE', CopixI18N::get ('menu.title.pageSelect'));
		$menu = $this->_getSessionMenu();
		$tpl->assign ('MAIN', CopixZone::process ('cms|SelectPage',
		array ('onlyLastVersion'=>1,
		'select'=>CopixUrl::get ('menu_2|admin|validPage', array('browse'=>'menu_2', 'id_menu'=>$menu->father_menu, 'id_head'=>CopixRequest::get('id_head',null,true))),
		'back'=>CopixUrl::get ('menu_2|admin|edit', array('browse'=>'menu_2', 'id_menu'=>$menu->father_menu, 'id_head'=>CopixRequest::get('id_head',null,true))))));

		return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
	}

	/**
    * validation d'une page pour la mise en ligne dans un élément de mnu.
    */
	function doValidPage (){
		if (!$toValid = $this->_getSessionMenu()) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('menu.error.unableGetBack'),
			'back'=>CopixUrl::get ('menu_2|admin|')));
		}

		if (isset ($this->vars['id']) && !empty ($this->vars['id'])){
			//Apply the changes on the edited object
			$toValid->id_cmsp = $this->vars['id'];
			$toValid->typelink_menu = 'cmsp';
		}
		if (CopixConfig::get ('menu_2|useCache') == 1)
		CopixCache::clear ();//TODO organize cache by types

		$this->_setSessionMenu ($toValid);
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'menu_2', 'id_head'=>CopixRequest::get ('id_head'))));
//		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('menu_2|admin|edit'));
	}

	/**
    * Deletes a menu item.
    * PROFILE_CCV_ADMIN required on this menu
    */
	function doDelete (){
		if (CopixConfig::get ('menu_2|useCache') == 1)
		CopixCache::clear ();//TODO organize cache by types

		//check for the id.....
		if (!isset ($this->vars['id_menu'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('menu.error.unableGetBack'),
			'back'=>CopixUrl::get ('menu_2|admin|')));
		}

		$dao = & CopixDAOFactory::getInstanceOf ('Menu');

		//does the menu exists ?
		if (($menu = $dao->get ($this->vars['id_menu'])) == false){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('menu.error.unableGetBack'),
			'back'=>CopixUrl::get ('menu_2|admin|')));
		}

		// Get Menu and profilePath info
		$menu->profilePath=$dao->getProfilePath($menu->id_menu);

		// do we have admin permissions on the father menu ?
		if (CopixUserProfile::valueOf ($menu->profilePath,'menu_2') < PROFILE_CCV_ADMIN) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('menu.error.rightmissing.admin'),
			'back'=>CopixUrl::get ()));
		}

		//Confirmation screen ?
		if (!isset ($this->vars['confirm'])){
			return CopixActionGroup::process ('genericTools|Messages::getConfirm',
			array ('title'=>CopixI18N::get ('menu.title.confirmDeleteMenu'),
			'message'=>CopixI18N::get ('menu.messages.confirmDeleteMenu', $menu->caption_menu),
			'confirm'=>CopixUrl::get ('menu_2|admin|delete', array ('id_menu'=>$this->vars['id_menu'], 'confirm'=>1)),
			'cancel'=>CopixUrl::get ('menu_2|admin|', array ('id_menu'=>$menu->father_menu))));
		}

		// Suppression du menu
		$dao->delete($menu->id_menu);
      return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'menu_2', 'id_menu'=>$menu->father_menu, 'id_head'=>CopixRequest::get ('id_head'))));
	}

	/**
    * doToggleDisplay
    * PROFILE_CCV_ADMIN required on this menu
    * @param id_menu
    * @return 
    */
	function doToggleDisplay () {
		if (CopixConfig::get ('menu_2|useCache') == 1){
			CopixCache::clear ();//TODO organize cache by types
		}

		if (!isset ($this->vars['id_menu'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('menu.error.unableGetBack'),
			'back'=>CopixUrl::get ('menu_2|admin|')));
		}

		$dao = CopixDAOFactory::getInstanceOf ('Menu');

		//does the menu exists ?
		if (($menu = $dao->get ($this->vars['id_menu'])) == false){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('menu.error.unableGetBack'),
			'back'=>CopixUrl::get ('menu_2|admin|')));
		}

		// Get Menu and profilePath info
		$menu->profilePath=$dao->getProfilePath($menu->id_menu);

		// do we have admin permissions on the father menu ?
		if (CopixUserProfile::valueOf ($menu->profilePath,'menu_2') < PROFILE_CCV_ADMIN) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('menu.error.rightmissing.admin'),
			'back'=>CopixUrl::get ()));
		}

		$dao->toggleDisplay($menu->id_menu);
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'menu_2', 'id_menu'=>$menu->father_menu, 'id_head'=>CopixRequest::get('id_head',null,true))));
//		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('menu_2|admin|list', array ('id_menu'=>$menu->father_menu)));
	}

	/**
    * Move the menu item up
    * PROFILE_CCV_ADMIN required on father_menu
    * @param id_menu
    */
	function doUp (){
		if (CopixConfig::get ('menu_2|useCache') == 1){
			CopixCache::clear ();
		}

		if (!isset ($this->vars['id_menu'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('menu.error.unableGetBack'),
			'back'=>CopixUrl::get ('menu_2|admin|')));
		}

		$dao = CopixDAOFactory::getInstanceOf ('Menu');
		//does the menu exists ?
		if (($menu = $dao->get ($this->vars['id_menu'])) == false){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('menu.error.unableGetBack'),
			'back'=>CopixUrl::get ('menu_2|admin|')));
		}

		// Get Menu and profilePath info
		$menu->profilePath=$dao->getProfilePath($menu->father_menu);

		// do we have admin permissions on the father menu ?
		if (CopixUserProfile::valueOf ($menu->profilePath,'menu_2') < PROFILE_CCV_ADMIN) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('menu.error.rightmissing.admin'),
			'back'=>CopixUrl::get ()));
		}

		$dao->doUp ($menu);
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'menu_2', 'id_menu'=>$menu->father_menu, 'id_head'=>CopixRequest::get('id_head',null,true))));
//		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('menu_2|admin|list', array ('id_menu'=>$menu->father_menu)));
	}

	/**
    * Move the menu item down
    * PROFILE_CCV_ADMIN required on father_menu
    * @param id_menu
    */
	function doDown (){
		if (CopixConfig::get ('menu_2|useCache') == 1){
		   CopixCache::clear ();
		}

		if (!isset ($this->vars['id_menu'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('menu.error.unableGetBack'),
			'back'=>CopixUrl::get ('menu_2|admin|')));
		}

		$dao = & CopixDAOFactory::getInstanceOf ('Menu');
		//does the menu exists ?
		if (($menu = $dao->get ($this->vars['id_menu'])) == false){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('menu.error.unableGetBack'),
			'back'=>CopixUrl::get ('menu_2|admin|')));
		}

		// Get Menu and profilePath info
		$menu->profilePath = $dao->getProfilePath($menu->father_menu);

		// do we have admin permissions on the father menu ?
		if (CopixUserProfile::valueOf ($menu->profilePath,'menu_2') < PROFILE_CCV_ADMIN) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('menu.error.rightmissing.admin'),
			'back'=>CopixUrl::get ()));
		}

		$dao->doDown ($menu);
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'menu_2', 'id_menu'=>$menu->father_menu, 'id_head'=>CopixRequest::get('id_head',null,true))));
//		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('menu_2|admin|list', array ('id_menu'=>$menu->father_menu)));
	}

	/**
    * Cut a menu element
    * PROFILE_CCV_ADMIN required
    * @param id_menu
    */
	function doCut () {
		if (!isset ($this->vars['id_menu'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('menu.error.unableGetBack'),
			'back'=>CopixUrl::get ('menu_2|admin|')));
		}

		$dao = & CopixDAOFactory::getInstanceOf ('Menu');

		//does the menu exists ?
		if (($menu = $dao->get ($this->vars['id_menu'])) == false){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('menu.error.unableGetBack'),
			'back'=>CopixUrl::get ('menu_2|admin|')));
		}

		// Get Menu and profilePath info
		$menu->profilePath=$dao->getProfilePath($menu->id_menu);

		// do we have admin permissions on the father menu ?
		if (CopixUserProfile::valueOf ($menu->profilePath,'menu_2') < PROFILE_CCV_ADMIN) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('menu.error.rightmissing.admin'),
			'back'=>CopixUrl::get ()));
		}

		$_SESSION ['MODULE_MENU_CUTEDMENU'] = $menu->id_menu;

        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'menu_2', 'id_menu'=>$menu->father_menu, 'id_head'=>CopixRequest::get('id_head',null,true))));
//		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('menu_2|admin|list', array ('id_menu'=>$menu->father_menu)));
	}

	/*
	* Paste a menu element
	* @param level
	*/
	function doPaste () {
		if (CopixConfig::get ('menu_2|useCache') == 1)
		CopixCache::clear ();//TODO organize cache by types

		if (!isset ($this->vars['father_menu'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('menu.error.unableGetBack'),
			'back'=>CopixUrl::get ('menu_2|admin|')));
		}

		$dao = CopixDAOFactory::getInstanceOf ('Menu');

		//does the cuted menu exists ?
		if (($cuted_menu = $dao->get ($_SESSION ['MODULE_MENU_CUTEDMENU'])) == false){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('menu.error.unableGetBack'),
			'back'=>CopixUrl::get ('menu_2|admin|')));
		}

		// Verify that this menu is not a child of cuted menu
		$arPath = $dao->getPath($this->vars['father_menu']);
		foreach ($arPath as $key=>$menu) {
			if ($menu->id_menu == $cuted_menu->id_menu) {
				return CopixActionGroup::process ('genericTools|Messages::getError',
				array ('message'=>CopixI18N::get('menu.error.unablePasteAsChild'),
				'back'=>CopixUrl::get ('menu_2|admin|')));
			}
		}

		$oldProfilePath = $dao->getProfilePath($cuted_menu->id_menu);
		$newProfilePath = $dao->getProfilePath($this->vars['father_menu']).'|'.$cuted_menu->id_menu;

		// do Paste actions
		$oldFatherMenu = $cuted_menu->father_menu;
		$cuted_menu->father_menu = ($this->vars['father_menu']=='' ? null : $this->vars['father_menu']);
		if ($cuted_menu->father_menu!=null) {
        	$cuted_menu->id_head = CopixRequest::get('id_head',null,true);
		}
		$cuted_menu->order_menu = $dao->getNewPos($cuted_menu->father_menu);
		$dao->update($cuted_menu);
		$dao->reOrder($oldFatherMenu);
		//Update Capability path
		if ($oldProfilePath!=$newProfilePath) {
			CopixProfileTools::moveCapabilityPath ($oldProfilePath, $newProfilePath);
		}
		unset($_SESSION ['MODULE_MENU_CUTEDMENU']);
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'menu_2', 'id_menu'=>$cuted_menu->father_menu, 'id_head'=>CopixRequest::get('id_head',null,true))));
//		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('menu_2|admin|list', array ('id_menu'=>$cuted_menu->father_menu)));
	}

	/**
    * updates informations on a single menu object from the vars.

    */
	function _validFromForm (& $toUpdate){
		$toCheck = array ('caption_menu', 'var_name_menu', 'tooltip_menu', 'typelink_menu', 'url_menu', 'popup_menu', 'width_menu', 'height_menu','tpl_menu');
		foreach ($toCheck as $elem) {
			if (isset ($this->vars[$elem])){
				$toUpdate->$elem = isset($this->vars[$elem]) ? $this->vars[$elem] : null;
			}
		}
	}

	/**
    * sets the current edited menu.

    */
	function _setSessionMenu ($toSet){
		$_SESSION['MODULE_MENU_EDITED_MENU'] = ($toSet !== null) ? serialize($toSet) : null;
	}
	/**
    * gets the current edited menu item.

    */
	private static function _getSessionMenu () {
		$dao = CopixDAOFactory::getInstanceOf ('Menu');
		return (isset ($_SESSION['MODULE_MENU_EDITED_MENU'])) ? unserialize($_SESSION['MODULE_MENU_EDITED_MENU']) : null;
	}
    
    public static function getSessionMenu () {
        return ActionGroupAdmin::_getSessionMenu();
	}
}
?>
