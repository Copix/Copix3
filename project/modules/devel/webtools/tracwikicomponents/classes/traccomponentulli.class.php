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

class TracComponentUlLi extends AbstractTokenizerComponent {
	protected $_startTag = null;
    public function getStartTagLength ($pData = null) {
    	return strlen ($pData);
    }
    
	public function getEndTagLength ($pData = null) {
    	return strlen ($pData);
    }
    public function startWithStartTag ($pString) {
    	if (preg_match ('%^\ *\*%',$pString, $matches) > 0) {
    		return $matches[0];
    	} else {
    		return false;
    	}
    	
    }
    
	public function getStartTagsPosition ($pString) {
    	preg_match_all ('%\ +\*%', $pString, $matches,  PREG_OFFSET_CAPTURE);
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
				$start = $pToken->getStartTag();
				if (preg_match ('%^\ *\*%',substr($pString,2), $matches) > 0) {
					if (strlen ($matches[0]) > strlen($start)) {
						return false;
    				}
    				if (strlen ($matches[0]) == strlen($start)) {
    					return '  '; 
    				}
    			}
				return '';
			} else {
				return false;
			}
    }
	public function render ($pText, $pToken) {
		$toReturn = '';
		$previous = $pToken->getBrotherFrom (-1);
		if (($previous == null) || !($previous->getComponent() instanceof AbstractTokenizerComponentUlLi )) {
			$toReturn .= '<ul>';
		}
		$toReturn .= '<li>'.$pText.'</li>';
		$next = $pToken->getBrotherFrom (1);
		if (($next === null) || ($next->getComponent() == null) || !($next->getComponent() instanceof AbstractTokenizerComponentUlLi )) {
			$toReturn .= '</ul>';
		}
		return $toReturn;
	}
}

?>