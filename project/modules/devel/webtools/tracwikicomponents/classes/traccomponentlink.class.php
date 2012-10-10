<?php
/**
 * @package		webtools
 * @subpackage	tracwikicomponents
 * @copyright	CopixTeam
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author		Julien SALLEYRON
 * @link		http://www.copix.org
 */

_classInclude ('wikirenderer|abstracttokenizercomponent');

class TracComponentLink extends AbstractTokenizerComponent {
    protected $_startTag = '[';
    protected $_endTag = ']';
    protected $_mustBeParse = false;
    
	public function render ($pText, $pToken) {
		$data = explode (' ', $pText,2);
		if (count ($data) > 1) {
			return '<a href="'.$data[0].'">'.$data[1].'</a>';
		} else {
			return $pText;
		}
	}
}

?>