<?php
/**
* @package		copix
* @subpackage	core
* @author		Croës Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Interface de base pour les classes qui prennent en charge les URL.
 * @package copix
 * @subpakage core
 */
interface ICopixUrlHandler {
	/**
	 * Analyse une URL et la transforme en un tableau de paramètres.
	 * @param	array	$pUrl	tableau qui reçoit en paramètre les éléments "chemin" de l'url
	 * @param	string	$pMode	le mode de gestion de l'url
	 * @return false si ne gère pas l'url, array si l'url à été gérée.
	 */
	public function parse ($pUrl, $pMode);

	/**
	 * Construction d'une URL.
	 * @param	array	$pDest		destination (trigramme copx) qui décrit l'action
	 * @param	array	$pParams	liste des paramètres supplémentaires donnés pour l'url
	 * @param 	string	$pMode		Le mode de gestion des URL actuel
	 * @return CopixUrlHandlerGetResponse, false si pas pris en charge
	 */
	public function get   ($pDest, $pParams, $pMode);
}

/**
 * Classe de base pour les handler d'url
 * @package copix
 * @subpackage core	
 */
abstract class CopixUrlHandler implements ICopixUrlHandler {
	/**
	 * Construction d'une URL.
	 * @param	array	$pDest		destination (trigramme copx) qui décrit l'action
	 * @param	array	$pParams	liste des paramètres supplémentaires donnés pour l'url
	 * @param 	string	$pMode		Le mode de gestion des URL actuel
	 * @return CopixUrlHandlerGetResponse, false si pas pris en charge
	 */
	public function get ($pDest, $pParams, $pMode){
		return false;
	}

	/**
	 * Analyse une URL et la transforme en un tableau de paramètres.
	 * @param	array	$pUrl	tableau qui reçoit en paramètre les éléments "chemin" de l'url
	 * @param	string	$pMode	le mode de gestion de l'url
	 * @return false si ne gère pas l'url, array si l'url à été gérée.
	 */
	public function parse ($pUrl, $pMode){
		return false;
	}
}