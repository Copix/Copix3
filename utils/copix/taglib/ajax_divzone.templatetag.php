<?php
/**
* @package		copix
* @subpackage	taglib
* @author		Salleyron Julien
* @copyright	2000-2006 CopixTeam
* @link			http://www.copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Balise permettant de charger un div en ajax au moment de l'evenement display
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagAjax_DivZone extends CopixTemplateTag {
    public function process ($pParams) {
	   $toReturn = '';
       if (!isset($pParams['zone'])) {
	       new CopixTemplateTagException('[DivAjax] Vous devez préciser une zone');
	   }
	   
	   if (!isset($pParams['id'])) {
	       new CopixTemplateTagException('[DivAjax] Vous devez préciser un id');
	   }
	   $id = $pParams['id'];
	   
	   
	   $extra = '';
	   if (isset($pParams['extra'])) {
	       $extra = $pParams['extra'];
	   }
	   
       $onDisplay = '';
	   if (isset($pParams['onDisplay'])) {
	       $onDisplay = $pParams['onDisplay'];
	   }
	   
	   $onHide = '';
	   if (isset($pParams['onHide'])) {
	       $onHide = $pParams['onHide'];
	   }
	   if (isset($pParams['id_click'])) {
	       $id_click = $pParams['id_click'];
	   }
	   
	   if (isset($pParams['text'])) {
	       $id_click = uniqid();
	       $toReturn .= '<span id="'.$id_click.'">'.$pParams['text'].'</span>';
	   }
	   
	   if (isset($id_click)) {
	   	       CopixHTMLHeader::addJSCode("
window.addEvent('domready', function () {
	var div = $('$id');
	$('$id_click').addEvent('display', function () {
		$onDisplay
	});

	$('$id_click').addEvent('hide', function () {
		$onHide
	});


	div.addEvent('display', function () {
		$('$id_click').fireEvent('display');
	});

	div.addEvent('hide', function () {
		$('$id_click').fireEvent('hide');
	});


	$('$id_click').addEvent ('click', function () {
		if ($('$id').getStyle('display') == 'none') {
			$('$id').setStyle('display','');
			$('$id').fireEvent('display');
		} else {
			$('$id').setStyle('display','none');
			$('$id').fireEvent('hide');
			$onHide
		}
	});
});
			");
	   }
	   $onComplete = '';
	   if (isset($pParams['onComplete'])) {
	       $onComplete = $pParams['onComplete'];
	   }
	   
	   $toReturn .= '<div style="display:none;" id="'.$id.'" '.$extra.' ></div>';
	   
	   $sessionVar = uniqid();
	   $sessionZone = uniqid();
	   
	   CopixSession::set($sessionZone,$pParams['zone']);
	   unset ($pParams['zone']);
	   
	   CopixSession::set($sessionVar,$pParams);

	   $jsCode = "
		window.addEvent('domready', function () {
			var div = $('$id');
    			div.addEvent('display', function () {
					if (div.innerHTML == '') {
						div.setHTML('<img src=\"".CopixUrl::getResource('img/tools/load.gif')."\" />');
        				new Ajax('"._url ('generictools|ajax|getZone',array('ajaxreturn'=>'true'))."', {
                			method: 'post',
                			update: div,
                			evalScripts : true,
                			data: {'zone':'$sessionZone','sessionvar':'$sessionVar'},
							onComplete: function () {
								$onComplete
							}

                		}).request();
					} else {
						$onComplete
					}
			    });";
	   if (isset($pParams['auto']) && $pParams['auto']) {
	       $jsCode .= "div.setStyle('display','');";
	       $jsCode .= "div.fireEvent('display');";
	   }
	   $jsCode.= "
		});
		";
	   
        CopixHTMLHeader::addJSCode($jsCode);	   
	   _tag ('mootools');
	   
       return $toReturn;
   }
}
?>