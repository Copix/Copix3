<?php
/**
* @package		copix
* @subpackage	taglib
* @author		Gérald Croës, Salleyron Julien
* @copyright	CopixTeam
* @link			http://www.copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Pour afficher une zone Copix 
* @package		copix
* @subpackage	taglib
*/
class TemplateTagCopixZone extends CopixTemplateTag  {
	public function process ($pParams, $pContent=null){
		if (!isset ($pParams['process'])){
			throw new CopixTemplateTagException ('[copixzone smarty tag] - missing required process parameter'); 
		}else{
			$zone = $pParams['process'];
			unset ($pParams['process']);
		}
	
		$fileInfo = new CopixModuleFileSelector ($zone);
		if (! CopixModule::isEnabled ($fileInfo->module)) {
			if (isset ($pParams['required']) && $pParams['required'] == false) {
				return "";
			}
	    }
	    
	    if (isset ($pParams['ajax']) && $pParams['ajax']) {
	    	return $this->_ajaxZone ($zone, $pParams);
	    } else {
			return CopixZone::process ($zone, $pParams);
	    }
	}
	
	private function _ajaxZone($pZone, $pParams) {
		   
        _tag ('mootools');
        
	   $toReturn = '';
	   
	   if (!isset($pParams['id'])) {
           $id = uniqid('id');
	   } else {
           $id = $pParams['id'];
           unset($pParams['id']);
	   }
	   
	   $extra = '';
	   if (isset($pParams['extra'])) {
	       $extra = $pParams['extra'];
	       unset($pParams['extra']);
	   }
	   
       $onDisplay = '';
	   if (isset($pParams['onDisplay'])) {
	       $onDisplay = $pParams['onDisplay'];
	       unset($pParams['onDisplay']);
	   }
	   
	   $onHide = '';
	   if (isset($pParams['onHide'])) {
	       $onHide = $pParams['onHide'];
	       unset($pParams['onHide']);
	   }
	   
	   if (isset($pParams['idClick'])) {
	       $id_click = $pParams['idClick'];
	       unset($pParams['idClick']);
	   }
	   
	   if (isset($pParams['text'])) {
	       $id_click = uniqid('clicker');
	       $toReturn .= '<span id="'.$id_click.'">'.$pParams['text'].'</span>';
	       unset($pParams['text']);
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
		div.setStyle('display','');
		div.fireEvent('load');
		$('$id_click').fireEvent('display');
	});

	div.addEvent('hide', function () {
		div.setStyle('display','none');
		$('$id_click').fireEvent('hide');
	});


	$('$id_click').addEvent ('click', function () {
		if ($('$id').getStyle('display') == 'none') {
			$('$id').fireEvent('display');
		} else {
			$('$id').fireEvent('hide');
		}
	});
});
			");
	   } else {
	   	CopixHTMLHeader::addJSCode("
window.addEvent('domready', function () {
	var div = $('$id');
	div.addEvent('display', function () {
		div.setStyle('display','');
		div.fireEvent('load');
		$onDisplay
	});

	div.addEvent('hide', function () {
		div.setStyle('display','none');
		$onHide
	});
});
	");
	   }
	   
	   $onComplete = '';
	   if (isset($pParams['onComplete'])) {
	       $onComplete = $pParams['onComplete'];
	       unset($pParams['onComplete']);
	   }
	   
	   $toReturn .= '<div style="display:none;" id="'.$id.'" '.$extra.' ></div>';
	   
	   $sessionVar = uniqid();
	   $sessionZone = uniqid();
	   
	   CopixSession::set($sessionZone,$pZone);
	   
	   CopixSession::set($sessionVar,$pParams);

	   $jsCode = "
		window.addEvent('domready', function () {
			var div = $('$id');
    			div.addEvent('load', function () {
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
	       $jsCode .= "div.fireEvent('display');";
	   }
	   $jsCode.= "
		});
		";
	   
        CopixHTMLHeader::addJSCode($jsCode);	   
	   
       return $toReturn;
	}
	
}	
?>