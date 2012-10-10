<?php
/**
* @package	 cms
* @subpackage copixheadings
* @author	Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* @package	 cms
* @subpackage copixheadings
* Show a select page "dialog"
*/
class ZoneSelectHeading extends CopixZone {
   /**
   * Constructor, we wants to be ble to use the cache
   */
   function ZoneSelectHeading (){
       $this->_useCache = intval (CopixConfig::get ('copixheadings|useCache')) === 1;
   }

   /**
   * Calculates the ID of the cache. (basically, we use the groups)
   */
   function _makeId (){
   	   $cache = new STdClass ();
       $cache->_params = $this->getParam('select');
       $cache->back   = $this->getParam('back');
       $cache->groups = CopixUserProfile::getGroups();
       return $cache;
   }

   function _createContent (&$toReturn) {
      //Création du sous template.
      $tpl = new CopixTpl ();
      
      $servicesHeadings = CopixClassesFactory::getInstanceOf ('copixheadings|CopixHeadingsServices');
      $headings         = CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices')->filterTree($servicesHeadings->getTree());

      $tpl->assign ('arHeadings', $headings);

      $tpl->assign ('select',  $this->getParam('select'));
      $tpl->assign ('back',    $this->getParam('back'));
      $tpl->assign ('selected', $this->getParam('selected'));

      $toReturn = $tpl->fetch (isset ($this->_params['mini']) ? 'headingtree.mini.ptpl' : 'headingtree.select.ptpl');
      return true;
   }
}
?>