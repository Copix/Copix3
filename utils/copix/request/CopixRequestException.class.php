<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link 		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de base pour les exceptions sur la requête
 * 
 * @package		copix
 * @subpackage	core
 */
class CopixRequestException extends CopixException {	
	/**
	 * Les variables manquantes
	 * 
	 * @var array 
	 */ 
	private $_vars = array ();

	/**
	 * Construction du message d'erreur
	 * 
	 * @param array $pMessage Tableau des variables manquantes
	 * @param int $pCode Code de l'erreur
	 */
	public function __construct ($pMessage, $pCode = null) {
		$this->_vars = is_array ($pMessage) ? $pMessage : array ($pMessage);
		$caller = CopixDebug::getCaller (1);
		$this->file = $caller['file'];
		$this->line = $caller['line'];
		parent::__construct (_i18n ('copix:copix.error.missingRequestVar', implode (', ', $this->_vars)), $pCode);
	}
	
	/**
	 * Indique quelle sont les variables manquantes dans la requ�te qui ont provoqu�e l'exception 
	 * @return array  
	 */
	public function getMissing (){
		return $this->_vars;
	}
}