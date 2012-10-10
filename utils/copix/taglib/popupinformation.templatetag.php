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
	   if (is_null ($pContent) && !isset($pParams['zone'])) {
	     return;
	   }
	   _tag ('mootools',array('plugins'=>array('overlayfix')));
	   if (!isset ($pParams['alt'])){
	   	$pParams['alt'] = '';
	   }
	   
	   $text = '';
	   if (isset ($pParams['text'])){
	        $text = $pParams['text'];
	        unset($pParams['text']);
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
	       case 'window':
	           $toReturn  = '<a rel="'.$id.'" id="div'.$id.'" class="divwindowpopup" href="javascript:void(null);">';
	           $close = '</a>';
	           $jsCode = "
				window.addEvent('domready',function () {
					$$('.divwindowpopup').each( function (el) {
						var rel = $(el.getProperty('rel'));
                        rel.injectInside(document.body);
						el.removeEvents();
						el.addEvent('trash',function () {
						    var rel = $(el.getProperty('rel')); 
							rel.remove(); 
						});
					    el.addEvent('sync', function (e) {
						    var rel = $(el.getProperty('rel'));
							rel.fixdivHide();
							rel.fixdivShow();
							rel.setStyle('display','none');
							rel.setStyle('display','');
							rel.addEvent('mouseenter', function () {
									var closer = rel.getElement('.divcloser');
									if (closer == null) {
										closer = new Element('div');
										closer.addClass('divcloser');
										closer.injectInside(rel);
										closer.setHTML('X Fermer');
										closer.setOpacity(0.5);
										closer.setStyles({
											'position':'absolute',
											'background-color':'white',
											'border':'1px solid black',
											'margin':'0',
											'padding-left':'4px',
											'padding-right':'4px',
											'cursor':'pointer'
										});
										closer.addEvent('click', function () {
											rel.fixdivHide();
                                			rel.setStyle('display','none');
											closer.setStyle('display','none');
										});
									
    									var temp = rel.getSize().size.x - 70;
    									closer.setStyles({
    										'top':0,
    										'left':temp
    									});
									}
										closer.addEvent('click', function () {
											rel.fixdivHide();
                                			rel.setStyle('display','none');
											closer.setStyle('display','none');
										});
									closer.setStyle('display','');
								});
								rel.addEvent('mouseleave', function () {
									var closer = rel.getElement('.divcloser');
									closer.setStyle('display','none');
								});

						});

                        el.addEvent('click', function (e) {
                            if (rel.getStyle('display') == 'none') {
							 	 var zone = $('zone_'+el.getProperty('rel'));
								 if (zone != null) {
								     zone.fireEvent('display');
								 } else {
									rel.addEvent('mouseenter', function () {
    									var closer = rel.getElement('.divcloser');
    									if (closer == null) {
    										closer = new Element('div');
    										closer.addClass('divcloser');
    										closer.injectInside(rel);
    										closer.setHTML('X Fermer');
    										closer.setOpacity(0.5);
    										closer.setStyles({
    											'position':'absolute',
    											'background-color':'white',
    											'border':'1px solid black',
    											'margin':'0',
    											'padding-left':'4px',
    											'padding-right':'4px',
    											'cursor':'pointer'
    										});
    										closer.addEvent('click', function () {
    											rel.fixdivHide();
                                    			rel.setStyle('display','none');
    											closer.setStyle('display','none');
    										});
    									}
    
    									var temp = rel.getSize().size.x - 70;
    									closer.setStyles({
    										'top':0,
    										'left':temp
    									});
    									closer.setStyle('display','');
    								});
    								rel.addEvent('mouseleave', function () {
    									var closer = rel.getElement('.divcloser');
    									closer.setStyle('display','none');
    								});
								}
								var e = new Event(e);
								var largeurElem = parseInt(rel.getSize().size.x);
								var hauteurElem = parseInt(rel.getSize().size.y);
								var correctionPlacementx = 0;
								var correctionPlacementy = 0;
								
								if( (temp = (window.getSize().size.y - (e.client.y + hauteurElem))) < 0 ){
									//20px c'est la taille du navigateur en bas (scroll + barre d'état)
									correctionPlacementy = temp-20;
								}								
								if( (temp = (window.getSize().size.x - (e.client.x + largeurElem))) < 0 ){
									correctionPlacementx = temp+temp*0.1;
								}

								rel.setStyles({
									'position':'absolute',
									'top' : (e.page.y+5+correctionPlacementy)+'px',
									'left' : (e.page.x+5+correctionPlacementx)+'px',
									'zIndex':'999'
								});
								rel.setStyle('display','');
								rel.fixdivShow();
								rel.makeDraggable(
								{
									'onStart':function () {
										rel.setOpacity(0.5);
									},
									'onComplete': function () {
										rel.setOpacity(1);
									},
									'onDrag':function () {
										rel.fixdivUpdate();
									}
								}
								);
                            } else {
								rel.fixdivHide();
                                rel.setStyle('display','none');
                            }  
						});
					});
				});
			   ";
	           break;
	       case 'clickdelay':
	           $toReturn  = '<a rel="'.$id.'" id="div'.$id.'" class="divclickdelaypopup" href="javascript:void(null);">';
	           $close = '</a>';
	           $jsCode = "
			   window.addEvent('domready',function () {
					$$('.divclickdelaypopup').each( function (el) {
						var rel = $(el.getProperty('rel'));
						rel.removeEvents('mouseenter');
						rel.addEvent ('mousemove', function () {
							save.flag = true;
						});

						rel.removeEvents('mouseleave');
    					rel.addEvent ('mouseleave', function () {
							save.flag = false;
    						save.hide.delay(1000);
								
    					});



						el.removeEvents();
						el.addEvent('trash',function () {
						    var rel = $(el.getProperty('rel')); 
							rel.remove(); 
						});
						rel.injectInside(document.body);
						var save = {
							click : false,
							flag : false,
							hide : function () {
								if (!save.flag) {
									save.flag = false;
									save.click = false;
									rel.fixdivHide();
									rel.setStyle('display','none');
								}
							} 
						};

					    el.addEvent('sync', function (e) {
						    var rel = $(el.getProperty('rel'));
							rel.fixdivHide();
							rel.fixdivShow();
							rel.setStyle('display','none');
							rel.setStyle('display','');
						});

						el.addEvent('click', function (e) {
                               if (rel.getStyle('display') == 'none') {
                                 rel.setStyle('display','');
							 	 var zone = $('zone_'+el.getProperty('rel'));
								 if (zone != null) {
								     zone.fireEvent('display');
								 }
									var e = new Event(e);
								
									var largeurElem = parseInt(rel.getSize().size.x);
									var hauteurElem = parseInt(rel.getSize().size.y);
									var correctionPlacementx = 0;
									var correctionPlacementy = 0;
								
									if( (temp = (window.getSize().size.y - (e.client.y + hauteurElem))) < 0 ){
										//20px c'est la taille du navigateur en bas (scroll + barre d'état)
										correctionPlacementy = temp-20;
									}								
									if( (temp = (window.getSize().size.x - (e.client.x + largeurElem))) < 0 ){
										correctionPlacementx = temp+temp*0.1;
									}

									rel.setStyles({
										'position':'absolute',
										'top' : (e.page.y+5+correctionPlacementy)+'px',
										'left' : (e.page.x+5+correctionPlacementx)+'px',
										'zIndex':'999'
									});
									save.click = true;
							    	//rel.fixdivShow();
                            	} else {
									rel.fixdivHide();
									save.flag = false;
									save.click = false;
                                	rel.setStyle('display','none');
                            	}
							 	
						});

    					el.addEvent('mouseleave', function () {
							save.flag = false;
    						if (save.click) {
    							save.hide.delay(1000);
    						}
    					});

					});
				});
				 ";
	           break;
	       case 'onclick':
	           $toReturn  = '<a rel="'.$id.'" id="div'.$id.'" class="divclickpopup" href="javascript:void(null);">';
	           $close = '</a>';
	           $jsCode = "
			   window.addEvent('domready',function () {

					$$('.divclickpopup').each( function (el) {
                        var rel = $(el.getProperty('rel'));
                        rel.injectInside(document.body);
						el.removeEvents();
						el.addEvent('trash',function () {
						    var rel = $(el.getProperty('rel')); 
							rel.remove(); 
						});
					    el.addEvent('sync', function (e) {
						    var rel = $(el.getProperty('rel'));
							rel.fixdivHide();
							rel.fixdivShow();
							rel.setStyle('display','none');
							rel.setStyle('display','');
						});

                        el.addEvent('click', function (e) {
                            if (rel.getStyle('display') == 'none') {
							 	 var zone = $('zone_'+el.getProperty('rel'));
								 if (zone != null) {
								     zone.fireEvent('display');
								 }
								var e = new Event(e);
								
								var largeurElem = parseInt(rel.getSize().size.x);
								var hauteurElem = parseInt(rel.getSize().size.y);
								var correctionPlacementx = 0;
								var correctionPlacementy = 0;
								
								if( (temp = (window.getSize().size.y - (e.client.y + hauteurElem))) < 0 ){
									//20px c'est la taille du navigateur en bas (scroll + barre d'état)
									correctionPlacementy = temp-20;
								}								
								if( (temp = (window.getSize().size.x - (e.client.x + largeurElem))) < 0 ){
									correctionPlacementx = temp+temp*0.1;
								}

								rel.setStyles({
									'position':'absolute',
									'top' : (e.page.y+5+correctionPlacementy)+'px',
									'left' : (e.page.x+5+correctionPlacementx)+'px',
									'zIndex':'999'
								});
								rel.setStyle('display','');
								rel.fixdivShow();
                            } else {
                                rel.setStyle('display','none');
								rel.fixdivHide();
                            }  
						});
					});

				});
				 ";
	           
	         break;
	      default:
	      	   //prend également en charge onmouseover qui est le handler par défaut.
	           $toReturn  = '<div rel="'.$id.'" id="div'.$id.'" class="divpopup" style="display:inline;">';
	           $close = '</div>';
	           $jsCode = "
				 window.addEvent('domready',function () {
					 $$('.divpopup').each( function (el) {
							el.removeEvents();
						    el.addEvent('sync', function (e) {
							    var rel = $(el.getProperty('rel'));
								rel.fixdivHide();
								rel.fixdivShow();
								rel.setStyle('display','none');
								rel.setStyle('display','');
							});

							el.addEvent('trash',function () {
							    var rel = $(el.getProperty('rel')); 
								rel.remove(); 
							});

							 var rel = $(el.getProperty('rel'));
							 rel.injectInside(document.body);
							 el.addEvent('mouseenter', function (e) {
								 var zone = $('zone_'+el.getProperty('rel'));
								 if (zone != null) {
								     zone.fireEvent('display');
								 }
								 var e = new Event(e);
	    				         rel.setStyle('display','');
							 });

							 el.addEvent('mousemove', function (e) {
								var e = new Event(e);
								var largeurElem = parseInt(rel.getSize().size.x);
								var hauteurElem = parseInt(rel.getSize().size.y);
								var correctionPlacementx = 0;
								var correctionPlacementy = 0;
								
								if( (temp = (window.getSize().size.y - (e.client.y + hauteurElem))) < 0 ){
									//20px c'est la taille du navigateur en bas (scroll + barre d'état)
									correctionPlacementy = temp-20;
								}								
								if( (temp = (window.getSize().size.x - (e.client.x + largeurElem))) < 0 ){
									correctionPlacementx = temp+temp*0.1;
								}

								rel.setStyles({
									'position':'absolute',
									'top' : (e.page.y+5+correctionPlacementy)+'px',
									'left' : (e.page.x+5+correctionPlacementx)+'px',
									'zIndex':'999'
								});
							 });
							 el.addEvent('mouseleave', function (e) {
							     var e = new Event(e);
						         rel.setStyle('display','none');
		
							 });							
					 });
				});
	           ";
	         break;
	   }
	   $toReturn .= $pParams['displayimg']  === true ? '<img src="'.$pParams['img'].'" title="'.$pParams['alt'].'" alt="'.$pParams['alt'].'" />' : '';
	   $toReturn .= strlen ($text) ? $text : '';
	   $toReturn .= isset($pParams['imgnext']) ? '<img src="'.$pParams['imgnext'].'" />' : '';
	   $toReturn .= $close;
       $toReturn .= '<div class="'.$pParams['divclass'].'" id="'.$id.'" style="display:none;" >';
	   if (isset($pParams['zone'])) {
	       $zone = $pParams['zone'];
	       unset($pParams['zone']);
	       $toReturn .= _tag('copixzone', array_merge($pParams,array('onComplete'=>'$(\'div'.$id.'\').fireEvent(\'sync\');','process'=>$zone,'ajax'=>true, 'id'=>'zone_'.$id)));
	   } else {
	       $toReturn .= $pContent;
	   }
	   $toReturn .= '</div>';
   
	   CopixHTMLHeader::addJSCode ($jsCode, 'popupinformation'.$pParams['handler']);
	   
       return $toReturn;
   }
}
?>
