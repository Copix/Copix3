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

class TracComponentParagraphe extends AbstractTokenizerComponent {
	protected $_startTag = '';
    protected $_endTag = '';
    
    public function __construct () {
    	$this->_startTag = chr(13).chr(10).chr(13).chr(10);
    }
    
    protected $_isContainerComponent = false;
    
    public function getStartTagsPosition ($pString) {
    	return false;
    }
    
	public function getStartingTag ($pString) {
		$nl = chr(13).chr(10).chr(13).chr(10);
		if (!strcmp (substr ($pString, 0, 4),$nl)) {
            return $nl;
        } else {
            return false;
        }
    }
    
	public function render ($pText, $pToken) {
		return '</p>-------------<p>';
	}
}

?>