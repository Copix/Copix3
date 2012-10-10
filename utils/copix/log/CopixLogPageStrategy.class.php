<?php
/**
* @package   copix
* @subpackage log
* @author    Gérald Croës
* @copyright CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Log en mémoire pour affichage sur la page
 * 
 * @package   copix
 * @subpackage log
 */
class CopixLogPageStrategy implements ICopixLogStrategy {
	/**
	 * Tableau des logs générés sur la page
	 *
	 * @var array
	 */
	private static $_logs = array ();
	
	/**
	 * Sauvegarde les logs dans le fichier
	 *
	 * @param String $pMessage log à sauvegarder
	 * @param String $tab tableau d'option
	 */
	public function log ($pProfil, $pType, $pLevel, $pDate, $pMessage, $pArExtra){
		$log = array ('date'=>$pDate, 'message'=>$pMessage, 'level'=>$pLevel, 'type'=>$pType);
		$log = $log + $pArExtra;
		if (!isset (self::$_logs[$pProfil])){
			self::$_logs[$pProfil] = array ();
		}
		self::$_logs[$pProfil][] = $log;
	}
	
	/**
	 * Supprimer tous les log de ce profil
	 * @param	string	$pProfil	Le nom du profil dont on souhaite supprimer les contenus
	 * @return int	nombre de logs supprimés 
	 */
	public function deleteProfile ($pProfil){
		self::$_logs[$pProfil] = array (); 		
	}
	
	/**
	 * Retourne les logs sous forme d'itérateur
	 * @return Iterator
	 */
	public function getLog ($pProfil){
		return isset (self::$_logs[$pProfil]) ? new ArrayObject (self::$_logs[$pProfil]) : new ArrayObject (array ());
	}
}
?>