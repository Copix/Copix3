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

class TracComponentBold extends AbstractTokenizerComponent {
    protected $_startTag = '\'\'\'';
    protected $_endTag = '\'\'\'';
    
    public function render ($pText, $pToken) {
    	return '<strong>'.$pText.'</strong>';
    }
}

?>