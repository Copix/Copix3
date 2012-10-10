<?php
/**
* @package		copix
* @subpackage	taglib
* @author		Gérald Croës
* @copyright	2000-2006 CopixTeam
* @link			http://www.copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Balise capable d'afficher une liste déroulante
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagAjax_PopupInformation extends CopixTemplateTag {
    public function process ($pParams) {
	   if (!isset($pParams['zone'])) {
	       new CopixTemplateTagException('zone');
	   }
	   
	   $sessionVar = uniqid();
	   $sessionZone = uniqid();
	   
	   CopixSession::set($sessionZone,$pParams['zone']);
	   unset ($pParams['zone']);
	   CopixSession::set($sessionVar,$pParams);
	   
	   
	   if (!isset ($pParams['alt'])){
	   	$pParams['alt'] = '';
	   }
	   
       if (!isset ($pParams['imgtitle'])){
	   	$pParams['imgtitle'] = '';
	   }
	   
	   if (!isset ($pParams['text'])){
	    $pParams['text'] = '';
	   }
	   
	   if (!isset ($pParams['displayimg'])) {
	    $pParams['displayimg']  = true;
	   }
	   
	   if (!isset ($pParams['img'])){
	    $pParams['img'] = _resource ('img/tools/information.png');
	   }
	   
	   if (!isset ($pParams['divclass'])){
	    $pParams['divclass'] = 'popupInformation';
	   }
	   
	   if (!isset ($pParams['handler'])) {
	        $pParams['handler']  = 'onmouseover';
	   }
	   
	   $id  = uniqid ('popupInformation');
	   switch ($pParams['handler']) {
	       case 'click':
	           $toReturn  = '<a rel="'.$id.'" id="div'.$id.'" href="javascript:void(null);">';
	           $close = '</a>';
	         break;
	      default:
	      	   //prend également en charge onmouseover qui est le handler par défaut.
	           $toReturn  = '<div rel="'.$id.'" id="div'.$id.'" class="divpopup" style="display:inline;">';
	           $close = '</div>';
	         break;
	   }
	   $toReturn .= $pParams['displayimg']  === true ? '<img src="'.$pParams['img'].'" title="'.$pParams['imgtitle'].'" alt="'.$pParams['alt'].'" />' : '';
	   $toReturn .= strlen ($pParams['text']) ? $pParams['text'] : '';
	   $toReturn .= isset($pParams['imgnext']) ? '<img src="'.$pParams['imgnext'].'" />' : '';
	   $toReturn .= $close;

	   $toReturn .= '<div class="'.$pParams['divclass'].'" id="'.$id.'" style="display:none;" >';
	   //$toReturn .= $pContent;
	   $toReturn .= '</div>';
	   

	   $jsCode = "
var ajax_zone = { 
	getZone : function (pZone,pSession,pDiv) {
		new Ajax('"._url ('generictools|ajax|getZone')."', {
        					method: 'post',
        					update: pDiv,
        					evalScripts : true,
							data: {'zone':pZone,'sessionvar':pSession}
        				}).request();
	}
};
";
        CopixHTMLHeader::addJSCode($jsCode,'ajaxpopup');
	   switch ($pParams['handler']) {
	       case 'click':
	           	$jsCode = "
				window.addEvent('domready',function () {
    				var el = $('div$id');
    				var rel = $(el.getProperty('rel'));
					var save$id = {
						click : false,
						flag : false,
						hide : function () {
							if (!save$id.flag) {
								rel.fireEvent('hide');
							}
						} 
					};
                	el.addEvent ('click', function (e) {
						save$id.click = true;
                	    rel.fireEvent('display');
                	});
					el.addEvent('mouseleave', function () {
						if (save$id.click) {
							save$id.hide.delay(1000);
						}
					});
                	rel.addEvent ('mouseleave', function () {
                		rel.fireEvent('hide');
                	});
					rel.addEvent ('mouseenter', function () {
						save$id.flag = true;
					});
					rel.addEvent ('display', function (e) {
						if (rel.getStyle('display') == 'none') {
							if (rel.innerHTML=='') {
									rel.setHTML('<img src=\"".CopixUrl::getResource('img/tools/load.gif')."\" />');
									ajax_zone.getZone('$sessionZone','$sessionVar',rel);
							}
							rel.setStyles({'left':(el.getLeft()+el.getSize().size.x)+'px'});
                	    	rel.setStyle('display','');
                	    } else {
                	    	rel.setStyle('display','none');
                	    }
					});
					rel.addEvent ('hide', function () {
						save$id.flag = false;
						save$id.click = false;
						rel.setStyle('display','none');
					});
				});
				";
	           break;
	       default:
	       $jsCode = "
				 window.addEvent('domready',function () {
					 		 el = $('div$id');
							 var rel = $(el.getProperty('rel'));
							 /*
							 el.addEvent('trash',function () {
								rel.remove();
							 });
							 */
							 
							 rel.injectInside(document.body);
							 el.addEvent('mouseenter', function (e) {
								if (rel.innerHTML=='') {
									rel.setHTML('<img src=\"".CopixUrl::getResource('img/tools/load.gif')."\" />');
									ajax_zone.getZone('$sessionZone','$sessionVar',rel)
								}
								var e = new Event(e);
	    				        rel.setStyle('visibility','visible');
							 });

							 el.addEvent('mousemove', function (e) {
								var e = new Event(e);
								 rel.setStyles({
									'position':'absolute',
									'top' : (e.page.y+5)+'px',
									'left' : (e.page.x+5)+'px'
								 });
							 });

							 el.addEvent('mouseleave', function (e) {
							     var e = new Event(e);
						         rel.setStyle('visibility','hidden');
							 });
							

				});
				 ";
	   }
	   CopixHTMLHeader::addJSCode ($jsCode);
	   
	   _tag ('mootools');
       return $toReturn;
   }
}
?>