<?php
/**
* @package		standard
* @subpackage	plugin_i18n
* @author		Jouanneau Laurent, Croës Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Classe de configuration pour le plugin i18n
 * @package standard
 * @subpackage plugin_i18n
 */
class PluginConfigI18n {
	/**
     * codes des langages disponibles sur le site
     */
	var $availableLanguageCode = array('fr', 'en');

	/**
    * code language par defaut
    */
	var $defaultLanguageCode = 'fr';

	/**
    * utilisation du language indiqué dans le navigateur
    */
	var $useDefaultLanguageBrowser = false;

	/**
    * active la detection du changement de language via l'url fournie
    */
	var $enableUserLanguageChoosen = true;

}
?>