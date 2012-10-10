<?php
/**
* @package	 cms
* @subpackage cms_portlet_links
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */

CopixClassesFactory::fileInclude ('cms|Portlet');

/**
 * @package	cms
 * @subpackage cms_portlet_links
 * LinkForPortlet
 */
class LinkForPortlet {
	/**
    * id of the link
    */
	var $id;

	/**
    * Name of the link
    */
	var $linkName;

	/**
    * Destination of the link, urlencoded
    */
	var $linkDestination;

	/**
    * Constructor.
    * @param string $name the name of the link (its caption)
    * @param string $destination the url of the link (may not be url encoded)
    */
	function LinkForPortlet ($name, $destination){
		$this->id              = uniqid ('l');
		$this->linkName        = $name;
		$this->linkDestination = $destination;
	}
}
/**
 * @package	cms
 * @subpackage cms_portlet_links
 * PortletLinks
 */
class PortletLinks extends Portlet {
	/**
    * the title of the link group
    */
	var $title;

	/**
    * the template
    */
	var $templateId;

	/**
    * Les liens de la portlet
    */
	var $links = array ();

	function PortletLinks ($id) {
		parent::Portlet ($id);
		$this->title        = null;
		$this->templateId = 'cms_portlet_links|normal.links.tpl';
	}

	/**
    * gets the parsed link portlet.
    * @param string $context the parsed context
    * @return string
    */
	function getParsed ($context) {
		$tpl = new CopixTpl ();
		$tpl->assign ('toShow', $this);

		/* added by ben 24-05-2004 utile pour le template jaune*/
		$newArray = array();
		if( is_array( $this->links ) ){
			foreach( $this->links as $key=>$value ){
				/* spécifique pour le select.preselect */
				$arrUrl = split( "&", $value->linkDestination );
				foreach( $arrUrl as $varUrl )
				if ( preg_match("(^id=[0-9]*)", $varUrl) )
				$value->idPortletToRedirect = strtr( $varUrl, array("id="=>"") );
				/* fin spécifique select.preselect */
				array_push( $newArray, $value );
			}
		}
		$tpl->assign ('toShow', $this);
		return $tpl->fetch ($this->templateId ? $this->getTemplateSelector() : 'cms_portlet_links|normal.links.tpl');
	}

	/**
    * Gets the portlet group id
    * @return string
    */
	function getGroup (){
		return 'general';
	}

	/**
    * gets the group caption I18N key
    * @return string the I18N key
    */
	function getGroupI18NKey (){
		return 'cms_portlet_links|links.group';
	}

	/**
    * Gets the I18n caption key
    * @return string the I18N key
    */
	function getI18NKey (){
		return 'cms_portlet_links|links.portletdescription';
	}

	/**
    * ajout d'un lien dans le groupe
    */
	function addLink ($link){
		$this->links[$link->id] = $link;
	}

	/**
    * supression d'un lien du groupe.
    */
	function removeLink ($id){
		unset ($this->links[$id]);
	}

	/**
    * gets the document $id position in the document array.
    */
	function _getLinkPositionInArray ($id){
		if (($founded = array_search ($id, array_keys ($this->links))) === false){
			$founded = null;
		}
		return $founded;
	}

	/**
    * Says if we can move the element up
    */
	function canMoveUp ($id){
		return ($this->_getLinkPositionInArray ($id) !== null) && ($this->_getLinkPositionInArray ($id) > 0);
	}

	/**
    * says if we can move the element down
    */
	function canMoveDown ($id){
		return ($this->_getLinkPositionInArray ($id) !== null) && ($this->_getLinkPositionInArray ($id) < (count ($this->links)-1));
	}

	/**
    * moves the document down
    */
	function moveDown ($id){
		if ($this->canMoveDown ($id)){
			$begin     = array_slice ($this->links, 0, $position = $this->_getLinkPositionInArray ($id));
			$docToSwap = array_reverse (array_slice ($this->links, $position, 2), true);
			$last      = array_slice ($this->links, $position + 2);

			$this->links = array_merge ($begin, $docToSwap, $last);
		}
	}

	/**
    * moves the document up
    */
	function moveUp ($id){
		if ($this->canMoveUp ($id)){
			//we insert (array_splice) the docs to swap (array_slice) in their reversed order (array_reverse) in the right position (_getDocumentPositionInArray)
			$begin     = array_slice ($this->links, 0, $position = ($this->_getLinkPositionInArray ($id)-1));
			$docToSwap = array_reverse (array_slice ($this->links, $position, 2), true);
			$last      = array_slice ($this->links, $position + 2);
			$this->links = array_merge ($begin, $docToSwap, $last);
		}
	}
}
/**
* @package	cms
* @subpackage cms_portlet_links
* Pour des raisons de compatibilité, l'ancienne convention étant de nommer les portlet XXXPortlet et non PortletXXX
*/
class LinksPortlet extends PortletLinks {}
?>