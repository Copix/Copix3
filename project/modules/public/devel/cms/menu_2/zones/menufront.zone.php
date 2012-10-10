<?php
/**
* @package	cms
* @subpackage menu_2
* @author	Sylvain DACLIN
* @copyright 2001-2006 CopixTeam
* @link		http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.htmlGNU Leser General Public Licence, see LICENCE file
*/

/**
* @package	cms
* @subpackage menu_2
* show a menu.
*/
class ZoneMenuFront extends CopixZone {
	var $arSelected = null;

	/**
    * Constructor, we wants to be ble to use the cache
    */
	function ZoneMenuFront (){
		//vérifie si l'on doit utiliser le cache.
		$this->_useCache = intval (CopixConfig::get ('menu_2|useCache')) === 1;
	}

	/**
    * Création du contenu, simplement
    */
	function _createContent (& $toReturn) {
		$tpl      = new CopixTpl ();
		$dao = CopixDAOFactory::getInstanceOf ('menu');
		if ($this->arSelected !== null){
			$tpl->assign ('arSelectedMenu', $this->arSelected);
		}else{
			if (($plugin = CopixController::instance ()->getPlugin ('menu_2|menu_2', false)) !== null){
				$tpl->assign ('arSelectedMenu', $plugin->getPath());
			}else{
				$tpl->assign ('arSelectedMenu', array ());
			}
		}

		$startNode = (isset($this->_params['id_menu'])) ? $this->_params['id_menu'] : 2;
		$depth      = (intval((isset ($this->_params['depth']) ? $this->_params['depth'] : 0))>0)  ? intval($this->_params['depth']) : null;

		//$arMenu = $dao->getMenu ($startNode,array('right'=>PROFILE_CCV_SHOW,'depth'=>$depth));
		$arMenu = $dao->getMenu ($startNode,array('right'=>0,'depth'=>$depth));
		$arMenu = $dao->getAssocMenu($arMenu);

		$tpl->assign ('startNode', $startNode);
		$tpl->assign ('arMenu',$arMenu);

		$this->template = (isset($this->_params['template'])) ? $this->_params['template'] : 'menu_2|normal.menu_2.ptpl';

		$toReturn = $tpl->fetch ($this->template);
		return true;
	}

	/**
    * On crée l'identifiant de cache en fonction du groupe auquel appartient l'utilisateur, de l'identifiant de menu demandé, puis du niveau de profondeur demandé.
    * @return string l'identifiant unique du cache qui corresponds au contexte
    */
	function _makeId (){
		$cache = new StdClass ();
		$cache->groups  =  CopixUserProfile::getGroups ();
		$cache->id_menu = (isset($this->_params['id_menu'])) ? $this->_params['id_menu'] : 2;
		$cache->depth   = (intval($this->_params['depth'])>0)  ? intval($this->_params['depth']) : null;
		if (($plugin = & $GLOBALS['COPIX']['COORD']->getPlugin ('menu_2|menu_2', false)) !== null){
			$this->arSelected =  $plugin->getPath();
		}else{
			$this->arSelected =  array ();
		}
		$cache->arSelected = $this->arSelected;
		return $cache;
	}
}
?>
