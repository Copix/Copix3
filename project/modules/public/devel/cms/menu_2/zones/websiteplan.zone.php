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
class ZoneWebSitePlan extends CopixZone {
   /**
   * Constructor, we wants to be ble to use the cache
   */
   function ZoneWebSitePlan (){
       $this->_useCache = intval (CopixConfig::get ('menu_2|useCache')) === 1;
   }

   function _createContent (& $toReturn) {
      $tpl      = & new CopixTpl ();

      $dao = & CopixDAOFactory::getInstanceOf ('menu_2|Menu');
      $startNode = (isset($this->_params['id_menu'])) ? $this->_params['id_menu'] : 2;
      $depth     = (intval($this->_params['depth'])>0)  ? intval($this->_params['depth']) : null;

      $arPlan = $dao->getMenu ($startNode,array('right'=>PROFILE_CCV_SHOW,'depth'=>$depth));
      $arPlan = $dao->getAssocMenu($arPlan);

      $tpl->assign ('startNode', $startNode);
      $tpl->assign ('arPlan',$arPlan);

      $this->template = (isset($this->_params['template'])) ? $this->_params['template'] : 'menu_2|normal.websiteplan.tpl';
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
		$cache->depth   = (intval((isset ($this->_params['depth']) ? $this->_params['depth'] : 0))>0)  ? intval($this->_params['depth']) : null;
		return $cache;
	}
}
?>
