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
 * Objet token contenant son parent, ses enfants, ses tags d'ouverture et fermeture et son texte de contenu
 * 
 * @package		webtools
 * @subpackage	wikirenderer
 */
class Token {
	
	private $_parent = null;
	
	private $_childrens = array ();
	
	private $_type = null;
	
	private $_component = null;
	
	private $_text = null;
	
	private $_startTag = null;
	
	private $_endTag = null;
	
	public function getStartTag () {
		return $this->_startTag;
	}
	public function getEndTag () {
		return $this->_endTag;
	}
	
	public function setStartTag ($pStartTag) {
		$this->_startTag = $pStartTag;
	}
	
	public function setEndTag ($pEndTag) {
		$this->_endTag = $pEndTag;
	}
	
	public function getParent () {
		return $this->_parent;
	}
	
	public function getChildrens () {
		return $this->_childrens;
	}
	
	public function getType () {
		return $this->_type;
	}
	
	public function getComponent () {
		return $this->_component;
	}
	
	public function setParent (Token $pParent) {
		$this->_parent = $pParent;
	}
	
	public function setText ($pText) {
		$this->_text = $pText;
	}
	
	public function addChild (Token $pChildren) {
		$this->_childrens[] = $pChildren;
	}
	public function getText () {
		return $this->_text;
	}
	
	
	public function setType ($pType) {
		$this->_type = $pType;
	}
	
	public function setComponent (ITokenizerComponent $pComponent) {
		$this->_component = $pComponent;
	}
	
	public function getBrotherFrom ($pPositionRelative = 1) {
		$arTokens = $this->getParent()->getChildrens();
		if ($pPositionRelative < 0) {
			$arTokens = array_reverse ($this->getParent()->getChildrens());
			$pPositionRelative = -$pPositionRelative;
		}
		$compteur = -1;
		foreach ($arTokens as $token) {
			if ($compteur >= 0) {
				$compteur++;
			}
			if ($this === $token) {
				$compteur++;
			}
			if ($compteur == $pPositionRelative) {
				return $token;
			}
		}
		return null;
	}
}
?>