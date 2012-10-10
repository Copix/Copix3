<?php
/**
 * @package    copix
 * @subpackage smarty_plugins
 * @author     Guillaume Perréal
 * @copyright  2000-2008 CopixTeam
 * @link       http://www.copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Permet de créer une boîte de dialogue "modale", qui sera centrée sur la fenêtre du navigateur.
 *
 * Params:
 * <pre>
 *   id             string (optionnel) Identifiant du tag crée.
 *   customContent  string (optionnel) Identifiant du tag contenant le contenu.
 *   title          string (optionnel) Si customContent est omis, ajoute un titre à la 
 *   icon           string (optionnel) Si customContent est omis, ajoute un icon à gauche du contenu. 
 *   openTriggers   mixed  (optionnel) Tableau (ou liste délimitée par des virgules) d'identifiants de 
 *                                     tag qui permettront d'ouvrir la boîte en cliquant dessus.
 *   closeTriggers  mixed  (optionnel) Tableau (ou liste délimitée par des virgules) d'identifiants de 
 *                                     tag qui permettront de fermer la boîte en cliquant dessus.
 *   onOpen         string (optionnel) Code Javascript à exécuter à l'ouverture.
 *   onClose        string (optionnel) Code Javascript à exécuter à la fermeture.
 * </pre>
 *
 * @param  array   $params  tableau des paramètres passés à la balise
 * @param  string  $content contenu du block
 * @param  Smarty  $smarty  pointeur sur l'élement smarty
 * @return string
 * 
 */
function smarty_block_js_modalbox (&$params, $content, &$smarty) {	
	return ($content !== null) ? _tag('js_modalbox', $params, $content) : '';
}