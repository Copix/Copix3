<?php
/**
* @package cms
* @subpackage	cms_portlet_document
* @version	$Id: portletdocument.class.php,v 1.1 2007/04/08 18:08:11 gcroes Exp $
* @author	Bertrand Yan, Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */

CopixClassesFactory::fileInclude ('cms|Portlet');

/**
 * @package cms
 * @subpackage	cms_portlet_document
 * PortletDocument
 */
class PortletDocument extends Portlet {
    /**
    * the subject of the document.
    * @var string
    */
    var $subject = null;

    /**
    * The selected documents id
    * @var array
    */
    var $arDocs = array ();

    function PortletDocument ($id) {
        parent::Portlet ($id);
        $this->template    = 'normal';
    }

    /**
    * gets the parsed document list.
    */
    function getParsed ($context) {
        $tpl = new CopixTpl ();

        $tpl->assign ('subject', $this->subject);
        $tpl->assign ('arDocs' , $this->getDocs ());

        return $tpl->fetch ($this->templateId ? $this->getTemplateSelector() : 'cms_portlet_document|normal.document.tpl');
    }
    
    /**
    * Get all doc
    * @return array

    */
    function getDocs () {
        $dao      = CopixDAOFactory::getInstanceOf ('document|document');
        $arDocs   = array ();
        foreach ($this->arDocs as $key=>$id_doc){
            $document = $dao->get ($id_doc, $dao->getLastVersion($id_doc));
            $arDocs[$key] = $document;
        }
        return $arDocs;
    }

    /**
    * Says if we can move the element up
    */
    function canMoveUp ($id){
        return array_key_exists ($id, $this->arDocs) && ($id > 0);
    }

    /**
    * says if we can move the element down
    */
    function canMoveDown ($id){
        return array_key_exists ($id, $this->arDocs)  && ($id < (count ($this->arDocs)-1));
    }

    /**
    * moves the document down
    */
    function moveDown ($id){
        if ($this->canMoveDown ($id)){
            $begin     = array_slice ($this->arDocs, 0, $position = $id);
            $docToSwap = array_reverse (array_slice ($this->arDocs, $position, 2), true);
            $last      = array_slice ($this->arDocs, $position + 2);

            $this->arDocs = array_merge ($begin, $docToSwap, $last);
        }
    }

    /**
    * moves the document up
    */
    function moveUp ($id){
        if ($this->canMoveUp ($id)){
            //we insert (array_splice) the docs to swap (array_slice) in their reversed order (array_reverse) in the right position (_getDocumentPositionInArray)
            $begin     = array_slice ($this->arDocs, 0, $position = ($id-1));
            $docToSwap = array_reverse (array_slice ($this->arDocs, $position, 2), true);
            $last      = array_slice ($this->arDocs, $position + 2);

            $this->arDocs = array_merge ($begin, $docToSwap, $last);
        }
    }

    function getGroup (){
        return 'general';
    }
    function getI18NKey (){
        return 'cms_portlet_document|document.portletdescription';
    }
    function getGroupI18NKey (){
        return 'cms_portlet_document|document.group';
    }
}

/**
* @package cms
* @subpackage	cms_portlet_document
* Pour des raisons de compatibilité, l'ancienne convention étant de nommer les portlet XXXPortlet et non PortletXXX
*/
class DocumentPortlet extends PortletDocument {}
?>