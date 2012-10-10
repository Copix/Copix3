<?php
/**
* @package   standard
* @subpackage plugin_copixlog
* @author   Croes Gérald, Salleyron Julien
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Plugin qui permet d'associer un thème à un module
* @package   standard
* @subpackage plugin_copixlog
*/
class PluginCopix_Log extends CopixPlugin {
	public function beforeProcess (){
	    CopixHTMLHeader::addJsCode("
window.addEvent('domready',
	function () {
		var divlog = new Element('div');
		divlog.injectInside(document.body);
		divlog.setStyles({
				'background-color':'white',
				'width':'500px',
				'height':'200px',
				'overflow':'auto'
			});
//		divlog.setOpacity('0.5');
		divlog.makeDraggable();

		var ajax = new Ajax('".CopixUrl::get('generictools|ajax|getZone')."',
		{
			method: 'post',
			update: divlog,
			evalScripts : true,
			data : {'zone':'admin|showlog','profil':'test'},
			onComplete: function () {
			}
		}).request()	
	}
);    
	    ");
	}
}
?>