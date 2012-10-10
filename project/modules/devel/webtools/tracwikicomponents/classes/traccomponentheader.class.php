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

class TracComponentHeader extends AbstractTokenizerComponent {
	protected $_startTag = null;
	public function getStartTagLength ($pData = null) {
    	return strlen ($pData);
    }
    
	public function getEndTagLength ($pData = null) {
    	return strlen ($pData);
    }
    public function startWithStartTag ($pString) {
    	if (substr($pString, 0, 1) == '=') {
    		preg_match ('%(=*)\ %', $pString, $matches);
    		list (,$start) = $matches;
        	return $start;
    	}
    	return false;
    }
    
    public function getStartTagsPosition ($pString) {
    	preg_match_all ('%(=+)\ %', $pString, $matches,  PREG_OFFSET_CAPTURE);
    	$arPos = array ();
    	foreach ($matches[1] as $match) {
    		$pos = $match[1];
    		$tag = $match[0];
    		$arPos[$pos] = $tag;
    	}
    	return $arPos;
    }
    
	public function getEndingTag ($pString, $pToken) {
		if ($pToken == null) {
			return false;
		}
		if (substr ($pString, 0, strlen($pToken->getStartTag ())) == $pToken->getStartTag ()) {
			if (substr ($pString, strlen($pToken->getStartTag ()), 1) != '=') {
				return $pToken->getStartTag ();
			} else {
				return null;
			}
		}
		if (substr($pString, 0, 1) == "\n") {
		    return null;
		}
		return false;
    }
    
    public function render ($pString, $pToken) {
    	if ($pToken->getStartTag () == $pToken->getEndTag()) {
    		return '</p><h'.strlen($pToken->getStartTag()).'>'.$pString.'</h'.strlen($pToken->getStartTag()).'><p>';
    	} else {
    		return $pToken->getStartTag ().$pString;
    	}
    }
	
}
?>