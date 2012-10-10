<?php
/**
 * @package cms
 * @subpackage heading
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author Steevan BARBOYON & S VUIDART
 */

/**
 * Parseur des tags liens pour le wysiwyg
 * 
 * @package cms
 * @subpackage heading
 */
class LinkWysiwygParser implements ICMSWysiwygParser {
	/**
	 * Transforme le texte en parsant et modifiant ce que le parseur veut changer
	 *
	 * @param string $pText Texte de base, parsé par les parseurs précédents
	 * @return string
	 */
	public function transform ($pText) {
		preg_match_all ('%\{copixurl url=(.*)\}%', $pText, $matches, PREG_SET_ORDER);
		foreach ($matches as $itemToReplace) {
			$pText = str_replace ($itemToReplace[0], _url ($itemToReplace[1]), $pText);
		}
		
		
		preg_match_all ('%\(cms:(\d*#?[a-zA-Z]*)\)%', $pText, $matches, PREG_SET_ORDER);
		foreach ($matches as $itemToReplace) {
			$extra = explode ('#',$itemToReplace[1]);
			$pText = str_replace ($itemToReplace[0], _url ('heading||', array ('public_id' => $extra[0])) . (array_key_exists ('1', $extra) ? '#' . $extra[1] : ''), $pText);
		}

		return $pText;
	}
}