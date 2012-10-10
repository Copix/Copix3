<?php
/**
 * @package		webtools
 * @subpackage	wikirenderer
 * @copyright	CopixTeam
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author		Julien SALLEYRON
 * @link		http://www.copix.org
 */

/**
 * Factory de token
 * 
 * @package		webtools
 * @subpackage	wikirenderer
 */
class TokenFactory {
	
	/**
	 * Renvoi un token
	 *
	 * @param Token $pParent le token parent, si null on est sur la racine
	 * @param mixed $pType Type de token, si c'est un composant il sera mis dans component
	 * @param string $pText Text du token
	 * @return Token le token paramètré
	 */
	public static function create ($pParent, $pType, $pText = null) {
		$token = _class('wikirenderer|token');
		if ($pParent != null) {
			$token->setParent($pParent);
		}
		if ($pType instanceof ITokenizerComponent) {
			$token->setComponent ($pType);
		} else {
			$token->setType ($pType);
		}
		$token->setText ($pText);
		return $token;
	}
	
}

 ?>