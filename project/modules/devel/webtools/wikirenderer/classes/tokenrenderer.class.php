<?php
/**
 * @package		webtools
 * @subpackage	wikirenderer
 * @copyright	CopixTeam
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author		Julien SALLEYRON
 * @link		http://www.copix.org
 */

_classInclude ('wikirenderer|token');

/**
 * Classe qui transforme un arbre de token en rendu, en utilisant les composants
 * 
 * @package		webtools
 * @subpackage	wikirenderer
 */
class TokenRenderer {
	
	private $_cache = false;
	
	public function isFromCache () {
		return $this->_cache;
	}
	
	/**
	 * Transforme l'arbre de token en rendu
	 *
	 * @param Token $token l'arbre de token
	 * @param string $pType le type de rendu
	 * @return string le rendu
	 */
	public function render (Token $token, $pType = 'HTML', $pUseCache = true, $pAcceptHtml = true) {
		switch ($token->getType ()) {
			case 'document':
				CopixHTMLHeader::addCSSLink(_resource('wiki|styles/wiki.css'));
				if ($pUseCache && CopixCache::exists(array ($token, $pType))) {
					$this->_cache = true;
					return CopixCache::read(array ($token, $pType));
				}
				$toReturn = '';
				foreach ($token->getChildrens() as $children) {
					$toReturn .= $this->render ($children, $pType, $pUseCache, $pAcceptHtml);
				}
				if ($pUseCache){
					CopixCache::write(array ($token, $pType), $toReturn);
				}
				return $toReturn;
			case 'text':
				return $pAcceptHtml ? $token->getText () : htmlentities($token->getText (), ENT_COMPAT, 'UTF-8');
			default:
				$toReturn = '';
				foreach ($token->getChildrens() as $children) {
					$toReturn .= $this->render ($children, $pType, $pUseCache, $pAcceptHtml);
				}
				if ($token->getComponent() != null) {
					return $token->getComponent()->render ($toReturn, $token);
				} else {
					return "PROBLEME###".$toReturn.'###PROBLEME';
				}				
				
		}
		
		
	}
	
}

?>