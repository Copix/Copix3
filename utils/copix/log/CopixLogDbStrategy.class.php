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
 * Log en base de données
 * 
 * @package   copix
 * @subpackage log
 */
class CopixLogDbStrategy implements ICopixLogStrategy {
	/**
	 * Sauvegarde les logs dans le fichier
	 *
	 * @param String $pMessage log à sauvegarder
	 * @param String $tab tableau d'option
	 */
	public function log ($pProfil, $pType, $pLevel, $pDate, $pMessage, $pArExtra){
		$dao     = _ioDAO ('copixlog');
		$newLogs = _record ('copixlog');
		$newLogs->type	  = $pType;
		$newLogs->date    = $pDate;
		$newLogs->profile  = $pProfil;
		$newLogs->level   = $pLevel;
		$newLogs->message = $pMessage;
		if(isset ($pArExtra['classname'])){
			$newLogs->classname = $pArExtra['classname'];
		}
		if(isset ($pArExtra['line'])){
			$newLogs->line = $pArExtra['line'];
		}
		if(isset ($pArExtra['file'])){
			$newLogs->file = $pArExtra['file'];
		}
		if(isset ($pArExtra['functionname'])){
			$newLogs->functionname = $pArExtra['functionname'];
		}
		if(isset ($pArExtra['user'])){
			$newLogs->user = $pArExtra['user'];
		}
		$dao->insert ($newLogs);		
	}
	
	/**
	 * Supprimer tous les log de ce profil
	 * @param	string	$pProfil	Le nom du profil dont on souhaite supprimer les contenus
	 * @return int	nombre de logs supprimés 
	 */
	public function deleteProfile ($pProfil){		
		return _ioDAO ('copixlog')->deleteBy (_daoSP ()->addCondition ('profile', '=', $pProfil));
	}
	
	/**
	 * Retourne les logs sous forme d'itérateur
	 * @return Iterator
	 */
	public function getLog ($pProfil){
		$arrayObject = new ArrayObject (_ioDAO ('copixlog')->findBy (_daoSP ()->addCondition ('profile', '=', $pProfil)
																			  ->orderBy (array ('date', 'DESC'))));
        return $arrayObject->getIterator ();
	}
}
?>