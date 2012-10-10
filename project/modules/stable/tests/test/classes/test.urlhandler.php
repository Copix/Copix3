<?php
/**
 * @package standard
 * @subpackage test
 * @author	Gérald Croës
 * @copyright CopixTeam
 * @link      http://copix.org
 * @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de test pour les URLHandler personnalisés.
 * @package standard
 * @subpackage test
 */
class UrlHandlerTest extends CopixUrlHandler {
	/**
	 * Simple fonction de get pour tester les URL significatives
	 */
	function get ($dest, $parameters, $mode) {
		$toReturn = new CopixUrlHandlerGetResponse ();

		if ($dest['module'] == 'test' && $dest['group']=='google'){
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