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
	   
	   _tag ('mootools', array('plugin'=>array('overlayfix')));
	  
	   $alt        = $this->getParam ('alt','');
	   $text       = $this->getParam ('text', '');
	   $displayimg = $this->getParam ('displayimg', true);
	   $img        = $this->getParam ('img', _resource ('img/tools/information.png'));
	   $divclass   = $this->getParam ('divclass', 'popupInformation');
	   $handler    = $this->getParam ('handler', 'onmouseover');
	   $namespace  = $this->getParam ('namespace', 'default'); 
	   $wHandle    = $this->getParam ('handlerWindow');
	   
	   
	   $id  = uniqid ('popupInformation');
	   
	   switch ($handler) {
	       case 'window':
	           $toReturn  = '<a rel="'.$id.'" id="div'.$id.'" class="divwindowpopup" href="javascript:void(null);"';
	           if (isset($pParams['title'])) {
	               $toReturn .= ' title="'.$pParams['title'].'"';
	           }
	           $toReturn .= '>';
	           $close = '</a>';
	           CopixHTMLHeader::addJsLink (_resource ('js/taglib/popupinfo_window.js'));
	           $js = new CopixJSWidget ();
	           CopixHTMLHeader::addJSDOMReadyCode ($js->popupinfo_window ($wHandle), 'popupinfo_window');
	           break;
	       case 'clickdelay':
	           $toReturn  = '<a rel="'.$id.'" id="div'.$id.'" class="divclickdelaypopup" href="javascript:void(null);">';
	           $close = '</a>';
	           CopixHTMLHeader::addJsLink (_resource ('js/taglib/popupinfo_clickdelay.js'));
	           CopixHTMLHeader::addJSDOMReadyCode ('popupinfo_clickdelay ();', 'popupinfo_clickdelay');
	           break;
	       case 'onclick':
	           $toReturn  = '<a rel="'.$id.'" id="div'.$id.'" class="divclickpopup" href="javascript:void(null);">';
	           $close = '</a>';
	           CopixHTMLHeader::addJsLink (_resource ('js/taglib/popupinfo_click.js'));
	           CopixHTMLHeader::addJSDOMReadyCode ('popupinfo_click ();', 'popupinfo_click');
	           
	         break;
	      default:
	      	   //prend également en charge onmouseover qui est le handler par défaut.
	           $toReturn  = '<div rel="'.$id.'" id="div'.$id.'" class="divpopup" style="display:inline;">';
	           $close = '</div>';
	           CopixHTMLHeader::addJsLink (_resource ('js/taglib/popupinfo.js'));
	           CopixHTMLHeader::addJSDOMReadyCode ('popupinfo ();', 'popupinfo');
	         break;
	   }
	   $toReturn .= $displayimg  === true ? '<img src="'.$img.'" title="'.$alt.'" alt="'.$alt.'" />' : '';
	   $toReturn .= strlen ($text) ? $text : '';
	   $toReturn .= isset($pParams['imgnext']) ? '<img src="'.$pParams['imgnext'].'" />' : '';
	   $toReturn .= $close;
       $toReturn .= '<div class="'.$divclass.'" id="'.$id.'" style="display:none;" rel='.$namespace.' >';

	   if (isset($pParams['zone'])) {
	       $zone = $pParams['zone'];
	       unset($pParams['zone']);
	       $toReturn .= _tag('copixzone', array_merge($this->getExtraParams(),array('onComplete'=>'$(\'div'.$id.'\').fireEvent(\'sync\');','process'=>$zone,'ajax'=>true, 'id'=>'zone_'.$id)));
	   } else {
	       $toReturn .= $pContent;
	   }
	   $toReturn .= '</div>';
       return $toReturn;
   }
}
?>
