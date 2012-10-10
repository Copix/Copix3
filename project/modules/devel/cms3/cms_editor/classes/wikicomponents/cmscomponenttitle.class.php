<?php
/**
 * @package		cms_editor
 * @subpackage	cms3
 * @copyright	CopixTeam
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author		Sylvain VUIDART
 * @link		http://www.copix.org
 */

_classInclude ('wikirenderer|abstracttokenizercomponent');

class CmsComponentTitle extends AbstractTokenizerComponent {
    protected $_startTag = null;
    
    public function getStartTagLength ($pData = null) {
    	return strlen ($pData);
    }
    
	public function getEndTagLength ($pData = null) {
    	return strlen ($pData);
    } 
    
	public function getStartTagsPosition ($pString) {
		preg_match_all ("|^#+|m", $pString, $matches,  PREG_OFFSET_CAPTURE);
    	$arPos = array ();
    	foreach ($matches[0] as $match) {
    		$pos = $match[1];
    		$tag = $match[0];
    		$arPos[$pos] = $tag;
    	}
    	return $arPos;
    }
    
	public function getEndingTag ($pString, $pToken) {
		if (!strcmp(substr ($pString, 0, 2), chr(13).chr(10)) || substr ($pString, 0, 1) == "\n") {
			return '';
		} else {
			return false;
		}
    }
    
    public function render ($pText, $pToken) {
    	$level = strlen($pToken->getStartTag ());
    	return "<h$level>".$pText."</h$level>";
    }
}

?>