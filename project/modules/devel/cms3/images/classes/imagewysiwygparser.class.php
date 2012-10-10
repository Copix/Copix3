<?php
/**
 * @package cms
 * @subpackage images
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 */

/**
 * Parseur des tags images pour le wysiwyg
 * 
 * @package cms
 * @subpackage images
 */
class ImageWysiwygParser implements ICMSWysiwygParser {
	/**
	 * Transforme le texte en parsant et modifiant ce que le parseur veut changer
	 *
	 * @param string $pText Texte de base, parsé par les parseurs précédents
	 * @return string
	 */
	public function transform ($pText) {
		// compatibilité avec l'ancien système d'images
		preg_match_all ('%\(image:(\d*)\)%', $pText, $matches, PREG_SET_ORDER);
		foreach ($matches as $itemToReplace) {
			$pText = str_replace ($itemToReplace[0], _url ('heading||', array ('public_id' => $itemToReplace[1])), $pText);
		}

		// nouveau système d'images
		preg_match_all ('%<img(.*)public_id="(\d*)"(.*)/>%U', $pText, $matches, PREG_SET_ORDER);
		foreach ($matches as $match) {
			$paramsStr = trim ($match[1]) . ' ' . trim ($match[3]);
			$params = array ('public_id' => $match[2]);

			$pos = 0;
			$isKey = true;
			$key = null;
			$isValue = false;
			$value = null;
			$valueQuote = null;
			$lengthParams = strlen ($paramsStr);
			while ($pos < $lengthParams) {
				$char = substr ($paramsStr, $pos, 1);
				if (!$isValue && $char == ' ') {
					$isKey = true;
					$key = null;
				} else if (!$isValue && $char == '=') {
					$isValue = true;
					$isKey = false;
					$temp = substr ($paramsStr, $pos + 1, 1);
					if ($temp == '"' || $temp == "'") {
						$valueQuote = $temp;
						$pos++;
					} else {
						$valueQuote = null;
					}
				} else if ($isKey) {
					$key .= $char;
				} else if ($isValue) {
					if (($valueQuote != null && $char == $valueQuote) || ($valueQuote == null && $char == ' ') || $pos == $lengthParams - 1) {
						$params[$key] = $value;
						$isValue = false;
						$value = null;
					} else {
						$value .= $char;
					}
				}

				$pos++;
			}

			// tag img avec tous les paramètres normaux
			$imgTag = '<img';
			foreach ($params as $name => $value) {
				if (!in_array ($name, array ('src', 'public_id', 'width', 'height', 'thumb_keep_proportions', 'thumb_show_image', 'thumb_galery_id', 'thumb_title', 'thumb_title_pos'))) {
					$imgTag .= ' ' . $name . '="' . $value . '"';
				}
			}

			// miniature
			if (array_key_exists ('thumb_show_image', $params)) {
				$url = _url ('heading||', array ('public_id' => $match[2]));
				$urlParams = array ('public_id' => $match[2]);
				if (array_key_exists ('width', $params)) {
					$urlParams['width'] = $params['width'];
				}
				if (array_key_exists ('height', $params)) {
					$urlParams['height'] = $params['height'];
				}
				$urlParams['keepProportions'] = (array_key_exists ('thumb_keep_proportions', $params) && $params['thumb_keep_proportions'] == 'true');
				$urlThumb = _url ('heading||', $urlParams);
				$imgTag .= ' src="' . $urlThumb . '" ';

				// mode d'affichage de l'image en taille réelle
				switch ($params['thumb_show_image']) {
					case 'smoothbox' :
						$url = _url ('heading||', array ('public_id' => $match[2], 'smoothboxType' => 'image'));
						_tag ('mootools', array ('plugins' => 'smoothbox'));
						$html = '<a href="' . $url  . '" class="smoothbox"';
						if (array_key_exists ('thumb_galery_id', $params)) {
							$html .= ' rel="' . $params['thumb_galery_id'] . '"';
						}
						if (array_key_exists ('title', $params)) {
							$html .= ' title="' . $params['title'] . '"';
						}
						if (array_key_exists ('alt', $params)) {
							$html .= ' alt="' . $params['alt'] . '"';
						}
						$html .= '>';
						$html .= $imgTag . ' />';
						$html .= '</a>';
						break;
					case '_blank' :
						$html = '<a href="' . $url  . '" target="_blank">';
						$html .= $imgTag . ' />';
						$html .= '</a>';
						break;
					default :
						$html = $match[0];
						break;
				}

				$pText = str_replace ($match[0], $html, $pText);

			// pas de miniature
			} else {
				if (isset ($params['width'])) {
					$imgTag .= ' width="' . $params['width'] . '"' ;
				}
				if (isset ($params['height'])) {
					$imgTag .= ' height="' . $params['height'] . '"' ;
				}
				$imgTag .= ' src="' . _url ('heading||', array ('public_id' => $params['public_id'])) . '"';
				$pText = str_replace ($match[0], $imgTag . ' />', $pText);
			}
		}

		return $pText;
	}
}