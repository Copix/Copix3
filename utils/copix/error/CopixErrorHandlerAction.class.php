<?php
/**
 * @package		copix
 * @subpackage	error
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Description des actions à effectuer dans le cas d'erreurs
 * 
 * @package		copix
 * @subpackage	error 
 */
class CopixErrorHandlerAction {
	/**
	 * Niveau de log
	 * 
	 * @var int
	 */
	private $_logLevel;
	
	/**
	 * Le type d'information qui sera indiqué lors du processus de log
	 * 
	 * @var string
	 */
	private $_logProfile;
	
	/**
	 * Indique s'il faut lancer ou non une exception lors du déclenchement de l'erreur
	 * 
	 * @var	boolean
	 */
	private $_launchException;

	/**
	 * Construction de l'action à effectuer sur une erreur
	 *
	 * @param boolean $pException S'il faut lancer une exception ou non
	 * @param int $pLogLevel Niveau de log à demander
	 * @param string $pLogInformationType Nom du fichier de log à utiliser 	
	 */
	public function __construct ($pException, $pLogLevel, $pLogInformationType = 'errors') {
		$this->_launchException = $pException;
		$this->_logLevel = $pLogLevel;
		$this->_logProfile = $pLogInformationType;
	}
	
	/**
	 * Retourne le niveau de log à utiliser pour le type d'erreur
	 * 
	 * @return int
	 */
	public function getLogLevel () {
		return $this->_logLevel;
	}
	
	/**
	 * Retourne le nom du profil à utiliser
	 * 
	 * @return string
	 */
	public function getLogProfile () {
		return $this->_logProfile;
	}
	
	/**
	 * Indique s'il faut lancer une exception ou non lorsque l'erreur survient
	 * 
	 * @return boolean
	 */
	public function getLaunchException () {
		return $this->_launchException;
	}
}