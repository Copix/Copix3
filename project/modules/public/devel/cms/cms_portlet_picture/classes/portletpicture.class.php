<?php
/**
* @package	 cms
* @subpackage cms_portlet_picture
* @author	Bertrand Yan
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('cms|portlet');

/**
 * @package cms
 * @subpackage cms_portlet_picture
 * PortletPicture
 */
class PortletPicture extends Portlet {
	/**
    * The picture ID
    * @var integer
    */
	var $id_pict;

	/**
    * The picture width
    * @var int
    */
	var $width;

	/**
    * The picture height
    * @var int
    */
	var $height;

	/**
    * Keep the picture ratios (false) or not (true)
    * @var boolean
    */
	var $force;

	/**
    * constructor
    */
	function PortletPicture ($id) {
		parent::Portlet ($id);
		$this->force = 0;
		$this->id_pict   = null;
	}

	/**
	* gets the parsed article.
	*/
	function getParsed ($context) {
		//essaye de récupérer l'image
		$daoPicture = & CopixDAOFactory::getInstanceOf ('pictures|pictures');
		$picture    = $daoPicture->get ($this->id_pict);
		$widthString = '';
		$heightString = '';
		if ($picture === null) {
			return '';
		}

		if (isset($picture->url_pict) && strlen($picture->url_pict) > 0) {
			$toReturn  = '<img src="'.$picture->url_pict;
			if ((strlen($this->width) > 0) && ($this->width <> 0)) {
				$widthString = ' width="'.intval ($this->width).'"';
			}
			if ((strlen($this->height) > 0) && ($this->height <> 0)) {
				$heightString = ' height="'.intval ($this->height).'"';
			}
		}else{
			$params['id_pict'] = $this->id_pict;
			if ((strlen($this->width) > 0) && ($this->width <> 0)) {
				$widthString = ' width="'.intval ($this->width).'"';
				$params['width'] = intval ($this->width);
			}
			if ((strlen($this->height) > 0) && ($this->height <> 0)) {
				$heightString = ' height="'.intval ($this->height).'"';
				$params['height'] = intval ($this->height);
			}
			if ($this->force == 1) {
				$params['force'] = 1;
			}
			$toReturn = '<img src="'.CopixUrl::get ('pictures||get', $params);
		}
		return $toReturn.'"'.$heightString.$widthString.' />';
	}

	function getGroup (){
		return 'general';
	}
	function getGroupI18NKey (){
		return 'cms_portlet_picture|cms_portlet_picture.group';
	}
	function getI18NKey (){
		return 'cms_portlet_picture|cms_portlet_picture.portletdescription';
	}
}
/**
 * @package cms
 * @subpackage cms_portlet_picture
* Pour des raisons de compatibilité, l'ancienne convention étant de nommer les portlet XXXPortlet et non PortletXXX
*/
class PicturePortlet extends PortletPicture {}
?>