<?php
/**
* @package   standard
* @subpackage plugin_speedview
* @author   Croes Gérald, Laurent Jouanneau
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Classe de configuration pour le plugin speedview
 * @package standard
 * @subpackage plugin_speedview
 */
class PluginDefaultConfigSpeedView {
	/**
    * indique ce qui declenche le comptage de temps
    * 'url' => doit y avoir un paramètre  SpeedView=show dans l'url
    * 'display' => Affiche du moment que l'action concerne un affichage
    */
	var $trigger = 'display';

	/**
     * indique là où on affiche le temps d'execution
     * 'display' => sur la page résultat à la fermeture du </body>
     * 'comment' => sur la page résultat mais en tant que commentaire juste après <head>
     * 'log' => affiche via CopixLog dans le type speedview au niveau INFORMATION
     */
 	var $target = 'display';
}
?>