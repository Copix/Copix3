<?php
/**
* @package   copix
* @subpackage log
* @author    Landry Benguigui
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Log dans les éléments systèmes
 * @package copix
 * @subpackage log
 */
class CopixLogSystemStrategy implements ICopixLogStrategy {
	/**
	 * Sauvegarde les logs dans le fichier
	 *
	 * @param String $pProfil
	 * @param String $pType
	 * @param String $pLevel
	 * @param String $pMessage log à sauvegarder
	 * @param String $pArExtra tableau d'option
	 */
	public function log ($pProfil, $pType, $pLevel, $pDate, $pMessage, $pArExtra){
		error_log ($this->_formatLog ($pProfil, $pMessage, $pLevel, $pArExtra), 0);
	}
	
	/**
	 * Demande de suppression du profil
	 */
	public function deleteProfile ($pProfil){
		throw new Exception("Impossible de supprimer les logs système");
	}
	
	/**
	 * retourne les logs sous forme d'itérateur
	 */
	public function getLog ($pProfil){
		throw new Exception ("Impossible de lire les logs système");
	}
	
	/**
	 * Formate le message à sauvegarder
	 * 
	 * @param String $pProfil nom du profil configurer dans copixConfig
	 * @param String $pMessage message
	 * @param String $pLevel niveau du message 
	 * @param Array $tab tableau d'option 
	 * @return boolean la chaine formaté
	 */
	private function _formatLog ($pProfil, $pMessage, $pLevel, $tab){
		$suffixe = "";
		if (isset ($tab['classname'])){
			$suffixe .= "  - class: ".$tab['classname'];
		}
		if (isset ($tab['user'])){
			$suffixe .= " - user: ".$tab['user'];
		}
		return "Profil: ".$pProfil.$suffixe." - Niveau: ".$pLevel." : ".$pMessage;
	}
}
?>