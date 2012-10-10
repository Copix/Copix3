<?php
/**
 * @package		webtools
 * @subpackage	wikirenderer
 * @copyright	CopixTeam
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author		Julien SALLEYRON
 * @link		http://www.copix.org
 */

_classInclude ('wikirenderer|itokenizercomponent');

/**
 * Classe abstraite d'un composant
 * 
 * @package		webtools
 * @subpackage	wikirenderer
 */
abstract class AbstractTokenizerComponent implements ITokenizerComponent {

	protected $_startLen = null;
	protected $_endLen = null;
	
	public function __construct () {
		if (isset ($this->_startTag)) {
			$this->_startLen = strlen($this->_startTag);
		}
		if (isset ($this->_endTag)) {
			$this->_endLen = strlen($this->_endTag);
		}
	}
	
	public function getStartTagsPosition ($pString) {
		if ($this->_startTag != null) {
			$arPos = array ();
			$startPosition = 0;
			while (($position = strpos (substr($pString, $startPosition), $this->_startTag)) !== false) {
				$arPos[$startPosition+$position] = $this->_startTag;
				$startPosition = $startPosition+$position+strlen($this->_startTag);
			}
			return $arPos;
		}
		return false;
	}
	
	public function isContainerComponent () {
		return isset ($this->_isContainerComponent) ? $this->_isContainerComponent :  true;
	}
	
	public function isEscapeComponent () {
		return isset ($this->_isEscapeComponent) ? $this->_isEscapeComponent :  false;
	}
	
	public function contentMustBeParse () {
		return isset ($this->_mustBeParse) ? $this->_mustBeParse :  true;
	}
	
	public function getStartTagLength ($pData = null) {
    	return $this->_startLen;
    }
	
	public function getEndTagLength ($pData = null) {
    	return $this->_endLen;
    }
    
    public function getLength () {
    	return $this->getStartTagLength();
    }
    
    public function getStartingTag ($pString) {
        if (isset ($this->_lineTag) && $this->_lineTag) {
    		if (strcmp ($pString, $this->_startTag) > 1) {
	        	list($line) = explode("\n", $pString);
				if (count (explode ($this->_endTag, substr($line,$this->_startLen))) > 1) {
	        		return $this->_startTag;
	        	}
        	}
        	return false;
    	} else {
    		return $this->startWithTag($pString, $this->_startTag);
    	}
    }
   
    public function getEndingTag ($pString, $pToken) {
        return $this->startWithTag($pString, $this->_endTag);
    }
   
    /**
     * Retourne $tag si la chaine commence par $tag
     *
     * @param string $pString
     * @param string $pTag
     * @return mixed false ou $tag
     */
    public function startWithTag ($pString, $pTag) {
        if (strcmp ($pString, $pTag) > 1) {
            return $pTag;
        } else {
            return false;
        }
    }
   
    public function getStartTag () {
        return $this->_startTag;
    }
   
    public function getEndTag () {
        return $this->_endTag;
    }
   
}


?>