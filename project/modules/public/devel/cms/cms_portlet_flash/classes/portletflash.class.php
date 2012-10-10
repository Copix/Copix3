<?php
/**
* @package		cms
* @subpackage	cms_portlet_flash
* @author		Croës Gérald, Salleyron Julien
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('cms|Portlet');

/**
 * Portlet pour afficher des documents flash
 * @package		cms
 * @subpackage	cms_portlet_flash 
 */
class PortletFlash extends Portlet {
    /**
    * L'identifiant de l'élément flash à afficher
    * @var string
    */
    var $id_flash = null;

    /**
    * retourne le code HTML de la portlet telle qu'elle doit être affichée dans une page
    */
    function getParsed ($context) {
    	return '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#3,0,0,0">
<param name="SRC" value="'.CopixUrl::get ('flash|default|get', array ('id'=>$this->id_flash)).'">
<param name="QUALITY" value="high">
<embed src="'.CopixUrl::get ('flash|default|get', array ('id'=>$this->id_flash)).'" pluginspage="http://www.macromedia.com/shockwave/download/" type="application/x-shockwave-flash" quality="high">
</embed>
</object>';    	
    }

    function getGroup (){
        return 'general';
    }
    function getI18NKey (){
        return 'cms_portlet_flash|flash.portletdescription';
    }
    function getGroupI18NKey (){
        return 'cms_portlet_flash|flash.group';
    }
    
    	/**
    * accès à la page de selection d'une photo
    */
	function getSelectFlash (){
		return CopixActionGroup::process ('flash|FlashBrowser::getBrowser',
		array ('select'=>CopixUrl::get ('cms_portlet_flash||edit'),
		'back'=>CopixUrl::get ('cms_portlet_flash||edit')));
	}
}
?>