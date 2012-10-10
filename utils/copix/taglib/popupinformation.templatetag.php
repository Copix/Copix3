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

        $title = $this->getParam ('title');

		$id = $this->getParam ('id', uniqid ('popupInformation'));

        CopixHTMLHeader::addJsLink (_resource ('js/taglib/popupinfo.js'));
        $js = new CopixJSWidget ();
        CopixHTMLHeader::addJSDOMReadyCode ($js->Copix->register_popup ($id, $this->getParams()));


        switch ($handler) {
            case 'onclick':
            case 'clickdelay':
                $toReturn  = '<a href="javascript:void (null);" rel="'.$id.'" id="div'.$id.'" title="'.$title.'" >';
                $toReturn .= $displayimg  === true ? '<img src="'.$img.'" alt="'.$alt.'" />' : '';
                $toReturn .= strlen ($text) ? $text : '';
                $toReturn .= isset($pParams['imgnext']) ? '<img src="'.$pParams['imgnext'].'" />' : '';
                $toReturn .= '</a>';
                break;
            default:
                $toReturn  = '<span rel="'.$id.'" id="div'.$id.'" title="'.$title.'" >';
                $toReturn .= $displayimg  === true ? '<img src="'.$img.'" alt="'.$alt.'" />' : '';
                $toReturn .= strlen ($text) ? $text : '';
                $toReturn .= isset($pParams['imgnext']) ? '<img src="'.$pParams['imgnext'].'" />' : '';
                $toReturn .= '</span>';
        }



        $toReturn .= '<div id="'.$id.'" class="'.$divclass.'" ';
        $style = 'display: none; ';
        if ($this->getParam('width') != null) {
            $style .= 'width:'.($this->getParam('width') != 'auto' ? $this->getParam('width').'px' : $this->getParam('width'));
        }
        if ($this->getParam('height') != null) {
            $style .= 'height:'.($this->getParam('height') != 'auto' ? $this->getParam('height').'px' : $this->getParam('height'));
        }

        if ($style != '') {
            $toReturn .= 'style="'.$style.'" ';
        }
        $toReturn .= '>';

        if (isset($pParams['zone'])) {
            $zone = $pParams['zone'];
            unset($pParams['zone']);
            $toReturn .= _tag('copixzone', array_merge($this->getExtraParams(),array('onComplete'=>'$(\'div'.$id.'\').fireEvent(\'sync\');','process'=>$zone,'ajax'=>true, 'id'=>'zone_'.$id)));
        } else {
            if (isset ($pParams['ajax']) && $pParams['ajax']) {
                $toReturn .= _tag('copixzone', array('onComplete'=>'$(\'div'.$id.'\').fireEvent(\'sync\');','process'=>'generictools|passthrough','ajax'=>true, 'id'=>'zone_'.$id, 'MAIN'=>$pContent, 'template'=>'generictools|blank.tpl'));
            } else {

                $toReturn .= $pContent;
            }
        }
        $toReturn .= '</div>';
        return $toReturn;
    }
}