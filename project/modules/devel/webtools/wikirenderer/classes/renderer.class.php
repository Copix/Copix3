<?php 
/**
 * @package		webtools
 * @subpackage	wikirenderer
 * @copyright	CopixTeam
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author		Julien SALLEYRON
 * @link		http://www.copix.org
 */

_classInclude ('wikirenderer|tokenizercomponents');

/**
 * Classe de rendu qui transforme un texte au format Wiki en un texte de type $pType
 * 
 * @package		webtools
 * @subpackage	wikirenderer
 */
class renderer {
	
	private $_errors = null;
	
	public function getErrors () {
		return $this->_errors;
	}
	
	/**
	 * Lance le rendu en passant pas un arbre de token
	 *
	 * @param string $pText le texte a rendre
	 * @param string $pType Type de rendu
	 * @return string le rendu du texte
	 */
	public function render ($pText, $pType = 'HTML') {
		_classInclude('wikirenderer|componentparsehandler');
		$tokenizer = _class ('wikirenderer|tokenizer');
		$timer = new CopixTimer ();
		$components = ComponentParseHandler::getInstallComponents();
		$tokens = $tokenizer->getTokens ($pText, $components);
		$this->_errors = $tokenizer->getErrors();
		return _ioClass('wikirenderer|tokenrenderer')->render($tokens, $pType);		
	}
	
}

?>