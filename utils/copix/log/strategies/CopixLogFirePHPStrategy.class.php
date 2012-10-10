<?php
/**
 * @package copix
 * @subpackage log
 * @author Croës Gérald
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Stratégie de stockage de logs avec FirePHP
 * 
 * @package	copix
 * @subpackage log
 */
class CopixLogFirePHPStrategy extends CopixLogAbstractStrategy {
	/**
	 * Instance de FirePHP
	 *
	 * @var FirePHP
	 */
	private $_firePHP = false;

	/**
	 * Constructeur
	 */
	public function __construct () {
		Copix::RequireOnce (COPIX_PATH . '../FirePHPCore/FirePHP.class.php');
		$this->_firePHP = FirePHP::getInstance (true); 		
	}
	
	/**
	 * Sauvegarde les logs dans le fichier
	 *
	 * @param String $pMessage log à sauvegarder
	 * @param String $tab tableau d'option
	 */
	public function log ($pProfil, $pType, $pLevel, $pDate, $pMessage, $pArExtra) {
		$this->_firePHP->trace ($pMessage);
	}
	
	/**
	 * Indique si on peut lire le contenu du profil de log indiqué
	 *
	 * @param string $pProfile Nom du profil
	 * @return boolean
	 */
	public function isReadable ($pProfile) {
		return false;
	}
	
	/**
	 * Indique si on peut écrire dans le profil de log indiqué
	 *
	 * @param string $pProfile Nom du profil
	 * @return boolean
	 */
	public function isWritable ($pProfile) {
		return class_exists ('FirePHP', false);
	}
}