<?php
/**
* @package cms
* @subpackage	document
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* @package cms
* @subpackage	document
* gets all version of a document
*/
class ZoneViewVersion extends CopixZone {
   function _createContent (&$toReturn){
      $tpl = & new CopixTpl ();
      $documentWorkflow = & CopixClassesFactory::getInstanceOf ('copixheadings|Workflow');

      $dao = CopixDAOFactory::getInstanceOf ('Document');
      $sp  = CopixDAOFactory::createSearchParams ();
      $sp  = & CopixDAOFactory::createSearchParams ();
      $sp->addCondition ('id_doc', '=', $this->_params['id_doc']);

      $tpl->assign ('arDocuments' , $dao->findBy ($sp));
      $toReturn = $tpl->fetch ('documents.allversion.tpl');

      return true;
   }
}
?>