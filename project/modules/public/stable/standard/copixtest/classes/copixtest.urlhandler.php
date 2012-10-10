<?php
/**
 * @package standard
 * @subpackage copixtest
 * @author	Gérald Croës
 * @copyright CopixTeam
 * @link      http://copix.org
 * @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de test pour les URLHandler personnalisés.
 * @package standard
 * @subpackage copixtest
 */
class UrlHandlerCopixTest extends CopixUrlHandler {
	/**
	 * Simple fonction de get pour tester les URL significatives
	 */
	function get ($dest, $parameters, $mode) {
		$toReturn = new CopixUrlHandlerGetResponse ();

		if ($dest['module'] == 'copixtest' && $dest['group']=='google'){
	    	$toReturn->externUrl = 'http://www.google.fr';
	    	return $toReturn;	
	    }
	    
		if (isset ($parameters['var'])) {
			$toReturn->path = array_merge ($dest, array ('var' => CopixUrl::escapeSpecialChars ($parameters['var'])));
			unset ($parameters['var']);
		}
		$toReturn->vars = $parameters;
		return $toReturn;
	}
}
?>