<?php
/**
* @package	cms
* @author	Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* @package cms
* Shows the list of the known portlets.
*/
class ZonePortletList extends CopixZone {
   /**
   *  Attends un objet de type textpage en paramètre.
   */
   function _createContent (&$toReturn){
      $tpl = new CopixTpl ();

      //inludes
      CopixClassesFactory::fileInclude ('cms|PortletInstaller');
      $objCatalogue = new PortletInstaller ();
      $tpl->assign ('list', $objCatalogue->getFromDatabase ());
      //appel du template.
      $toReturn = $tpl->fetch ('portlet.list.tpl');
   }
}
?>