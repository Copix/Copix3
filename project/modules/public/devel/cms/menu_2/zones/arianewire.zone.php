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
class ZoneArianeWire extends CopixZone {
   /**
   * Constructor, we wants to be ble to use the cache
   */
   function ZoneArianeWire (){
       $this->_useCache = intval (CopixConfig::get ('menu_2|useCache')) === 1;
   }
   
   function _createContent (& $toReturn) {
      $tpl      = & new CopixTpl ();
      
      if (isset($this->_params['id_cmsp'])) {
         $id_cmsp = $this->_params['id_cmsp'];
      }
      if (isset($id_cmsp)){
          CopixContext::push ('menu');
          $dao = & CopixDAOFactory::getInstanceOf ('menu_2|Menu');
          $arMenu = $dao->findByIdCmsp($id_cmsp);
          
          // On prend le premier menu qui convient (à modifier)
          $menu = isset ($arMenu[0]) ? $arMenu[0] : null;
          
          // On retrouve le chemin depuis la racine : 
          $arPath = $dao->getPath($menu->id_menu);
          CopixContext::pop();
          
          // On enlève la racine du menu
          array_shift($arPath);
          
          // On complète pour chaque menu le lien HTML
          foreach ($arPath as $key=>$menu) {
            $arPath[$key]->htmlLink = $dao->getHTMLLink($menu);
          }
          $tpl->assign ('arPath', $arPath);
          $tpl->assign ('title', isset($this->title)?$this->title:'');
          
          $this->template = (isset($this->_params['template'])) ? $this->_params['template'] : 'menu_2|normal.arianewire.tpl';
          $toReturn = $tpl->fetch ($this->template);
      }
      return true;
   }
}
?>