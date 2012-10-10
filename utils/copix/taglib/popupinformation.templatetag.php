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
class TemplateTagPopupInformation extends CopixTemplateTag {
    public function process ($pParams, $pContent = null) {
	   if (is_null ($pContent)) {
	     return;
	   }
	   
	   if (!isset ($pParams['alt'])){
	   	$pParams['alt'] = '';
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
	       case 'onclick':
	           $toReturn  = '<a rel="'.$id.'" id="div'.$id.'" class="divclickpopup" href="javascript:void(null);">';
	           $close = '</a>';
	         break;
	      default:
	      	   //prend également en charge onmouseover qui est le handler par défaut.
	           $toReturn  = '<div rel="'.$id.'" id="div'.$id.'" class="divpopup" style="display:inline;">';
	           $close = '</div>';
	         break;
	   }
	   $toReturn .= $pParams['displayimg']  === true ? '<img src="'.$pParams['img'].'" alt="'.$pParams['alt'].'" />' : '';
	   $toReturn .= strlen ($pParams['text']) ? $pParams['text'] : '';
	   $toReturn .= isset($pParams['imgnext']) ? '<img src="'.$pParams['imgnext'].'" />' : '';
	   $toReturn .= $close;

	   $toReturn .= '<div class="'.$pParams['divclass'].'" id="'.$id.'" style="visibility:hidden;" >';
	   $toReturn .= $pContent;
	   $toReturn .= '</div>';
	   
   
	   $jsCode = "
				 window.addEvent('domready',function () {
					 $$('.divpopup').each( function (el) {
							 el.addEvent('trash',function () {
								var rel = $(el.getProperty('rel')); 
								rel.remove(); 
							 });

							 var rel = $(el.getProperty('rel'));
							 rel.injectInside(document.body);
							 el.addEvent('mouseenter', function (e) {
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
							 //el.addEvent.('event',function (e) { var e=new Event(e); console.debug(e);});
							
					 });

					$$('.divclickpopup').each( function (el) {
                        var rel = $(el.getProperty('rel'));
                        rel.injectInside(document.body);
                        rel.setStyles({
						    'position':'absolute',
							'top' : el.getCoordinates().bottom,
							'left' : el.getCoordinates().right,						
							'visibility':'hidden' 
								 });
                        el.addEvent('click', function (e) {
                            if (rel.getStyle('visibility') == 'hidden') {
                                 rel.setStyle('visibility','visible');
                            } else {
                                rel.setStyle('visibility','hidden');
                            }  
						});
					});

				});
				 ";

	   CopixHTMLHeader::addJSCode ($jsCode, 'popupinformation');
	   _tag ('mootools');
       return $toReturn;
   }
}
?>