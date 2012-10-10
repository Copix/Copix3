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
 * @ignore
 */
require_once (COPIX_UTILS_PATH.'CopixPager.class.php');

/**
* @package cms
* @subpackage	document
* show online document for a given heading.
*/

class ZoneSelectDocument extends CopixZone {
    /**
    * @param int $this->_params['id_head'] the CopixHeading id.
    * @param string $this->_params['back'] url back
    * @param string $this->_params['select'] url select
    * @param string $this->_params['editorName'] name of the editor for htmlarea
    */
    function _createContent (& $toReturn) {
         $tpl = & new CopixTpl ();

         $back   = (isset($this->_params['back']))   ? $this->_params['back']   : 'HTMLAREA';
         $select = (isset($this->_params['select'])) ? $this->_params['select'] : 'HTMLAREA';

         $sHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixHeadingsServices');
         $headings  = $sHeadings->getTree();

         $dao              = CopixDAOFactory::getInstanceOf ('Document');
         $sp               = CopixDAOFactory::createSearchParams ();
         $documentWorkflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
         
         $sp->addCondition ('status_doc', '=', $documentWorkflow->getPublish ());
         $arTempDocuments = $dao->findBy($sp);
         $arDocuments     = array ();
         foreach ($arTempDocuments as $index=>$document){
            if ($document->version_doc == $dao->getLastVersion($document->id_doc)) {
               $arDocuments[$document->id_head][] = $document;
            }
         }


         $tpl->assign ('arPublished', $arDocuments);
         $tpl->assign ('arHeadings' , $headings);
         $tpl->assign ('back'       , $back);
         $tpl->assign ('select'     , $select);
         $tpl->assign ('editorName' , $this->getParam ('editorName', null));
         
         $toReturn = $tpl->fetch ('documents.select.ptpl');
      
         return true;
    }

}
?>
