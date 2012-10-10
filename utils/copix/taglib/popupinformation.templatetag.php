<?php
/**
 * @package copix
 * @subpackage taglib
 * @author Gérald Croës
 * @copyright CopixTeam
 * @link http://www.copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Affiche une zone d'informations
 *
 * @package copix
 * @subpackage taglib
 */
class TemplateTagPopupInformation extends CopixTemplateTag {
	/**
	 * Génération du contenu
	 *
	 * @param string $pContent Contenu du tag
	 * @return string
	 */
	public function process ($pContent = null) {
		$pParams = $this->getParams ();
		if ($pContent == null && !isset ($pParams['zone'])) {
			return;
		}

		// si on a beaucoup d'appels au tag, ça fait beaucoup d'appels inutiles
		static $isFirstCall = true;
		if ($isFirstCall) {
			_tag ('mootools', array ('plugin' => array ('overlayfix')));
			CopixHTMLHeader::addJsLink (_resource ('js/taglib/popupinfo.js'));
			$isFirstCall = false;
		}

		// on n'utilise pas le système des paramètres pour gagner énormément en temps de traitement sur beaucoup d'appels au tag
		// le système de paramètre n'est utile que si on veut appeler des validateurs automatiquement
		$alt = (isset ($pParams['alt'])) ? $pParams['alt'] : null;
		$text = (isset ($pParams['text'])) ? $pParams['text'] : null;
		$url = (isset ($pParams['url'])) ? $pParams['url'] : 'javascript:void(null);';
		$displayimg = (isset ($pParams['displayimg'])) ? $pParams['displayimg'] : true;
		$img = (isset ($pParams['img'])) ? $pParams['img'] : _resource ('img/tools/information.png');
		$divclass = (isset ($pParams['divclass'])) ? $pParams['divclass'] : 'popupInformation';
		$clickerclass = (isset ($pParams['clickerclass'])) ? $pParams['clickerclass'] : null;
		$handler = (isset ($pParams['handler'])) ? $pParams['handler'] : 'onmouseover';
		$title = (isset ($pParams['title'])) ? $pParams['title'] : null;
		$id = (isset ($pParams['id'])) ? $pParams['id'] : uniqid ('popupInformation');
		$imgNext = (isset ($pParams['imgnext'])) ? '<img src="' . $pParams['imgnext'] . '" alt="" />' : null;
		$width = (isset ($pParams['width'])) ? $pParams['width'] : null;
		$maxWidth = (isset ($pParams['max-width'])) ? $pParams['max-width'] : null;
		$height = (isset ($pParams['height'])) ? $pParams['height'] : null;
		$disabled = (isset ($pParams['disabled'])) ? $pParams['disabled'] : null;
		
		$js = new CopixJSWidget ();
		$js->Copix->register_popup ($id, $pParams);
		CopixHTMLHeader::addJSDOMReadyCode ($js, 'popupinformation' . $id);

		switch ($handler) {
			case 'onclick':
			case 'clickdelay':
				$toReturn  = '<a href="' . $url . '" class="' . $clickerclass . '" rel="' . $id . '" id="div' . $id . '" title="' . $title . '" '.(($disabled) ? 'disabled="disabled"':'') .'>';
				$toReturn .= $displayimg  === true ? '<img src="' . $img . '" alt="' . $alt . '" />' : '';
				$toReturn .= $text . $imgNext;
				$toReturn .= '</a>';
				break;
			default:
				$toReturn  = '<a href="' . $url . '" class="' . $clickerclass . '" rel="' . $id . '" id="div' . $id . '" title="' . $title . '" '.(($disabled) ? 'disabled="disabled"':'') .'>';
				$toReturn .= $displayimg  === true ? '<img src="' . $img . '" alt="' . $alt . '" />' : '';
				$toReturn .= $text . $imgNext;
				$toReturn .= '</a>';
		}

		$toReturn .= '<div id="' . $id . '" class="' . $divclass . '" ';
		$style = 'display: none; ';
		if ($width != null) {
			$style .= 'width:' . (($width != 'auto') ? $width . 'px' : $width) . ';';
		}
		if ($maxWidth != null) {
			$style .= 'max-width: ' . $maxWidth . 'px;';
		}
		if ($height != null) {
			$style .= 'height:' . (($height != 'auto') ? $height . 'px' : $height) . ';';
		}

		if ($style != '') {
			$toReturn .= 'style="' . $style . '" ';
		}
		$toReturn .= '>';

		if (isset ($pParams['zone'])) {
			$zone = $pParams['zone'];
			unset ($pParams['zone']);
			$toReturn .= _tag ('copixzone', array_merge ($pParams, array (
				'onComplete' => '$(\'div' . $id . '\').fireEvent(\'sync\');',
				'process' => $zone,
				'ajax' => true,
				'id' => 'zone_' . $id
			)));
		} else {
			if (isset ($pParams['ajax']) && $pParams['ajax']) {
				$toReturn .= _tag ('copixzone', array (
					'onComplete' => '$(\'div' . $id . '\').fireEvent(\'sync\');',
					'process' => 'generictools|passthrough',
					'ajax' => true,
					'id' => 'zone_' . $id,
					'MAIN' => $pContent,
					'template' => 'generictools|blank.tpl'
				));
			} else {
				$toReturn .= $pContent;
			}
		}
		$toReturn .= '</div>';
		return $toReturn;
	}
}