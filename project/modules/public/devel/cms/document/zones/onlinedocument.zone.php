<?php
/**
* @package cms
* @subpackage	document
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* @package cms
* @subpackage	document
* get all document online in this heading
*/
/**
 * @ignore
 */
require_once (COPIX_UTILS_PATH.'CopixPager.class.php');

/**
* @package	cms
* @subpackage document
* ZoneOnlineDocument
*/
class ZoneOnlineDocument extends CopixZone {
   function _createContent (&$toReturn){
      $tpl = & new CopixTpl ();

      $dao = CopixDAOFactory::getInstanceOf ('Document');
      $sp  = CopixDAOFactory::createSearchParams ();
      $documentWorkflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
      $sp->addCondition ('id_head', '=',$this->_params['id_head']);
      $sp->addCondition ('status_doc', '=', $documentWorkflow->getPublish ());
      $arTempDocuments = $dao->findBy($sp);
      $arDocuments = array ();
      foreach ($arTempDocuments as $index=>$document){
         if ($document->version_doc == $dao->getLastVersion($document->id_doc)) {
            $arDocuments[] = $document;
         }
      }
      if (count($arDocuments)>0) {
          $params = Array(
            'perPage'    => 10,
            'delta'      => 5,
            'recordSet'  => $arDocuments,
         );
         $Pager = CopixPager::Load($params);
         $tpl->assign ('pager'       , $Pager->GetMultipage());
         $tpl->assign ('arDocuments' , $Pager->data);
      }

      $toReturn = $tpl->fetch ('documents.online.tpl');
      return true;
   }
}
?>