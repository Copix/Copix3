<?php
/**
 * @package   copix
 * @subpackage log
 * @author    Landry Benguigui
 * @copyright 2001-2008 CopixTeam
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
	 * Retourne les logs sous forme d'idérateur
	 *
	 * @param 	string	$pProfil	Nom du profil dont on souhaite afficher le contenu
	 * @param 	int 	$pNbItems	Nombres d'items à afficher
	 * @return 	Iterator
	 */
	public function getLog ($pProfil, $pNbItems = 20){
		// Mise en place des limites
		$page = CopixSession::get('log|numpage')-1;
		$start = $page * $pNbItems;

		// Création du Search Params
		$sp = _daoSP ()->addCondition ('profile', '=', $pProfil)
		               ->orderBy (array ('date', 'DESC'));

		$dbNbLines = _ioDAO ('copixlog')->countBy ($sp);
		CopixSession::set ('log|nbpage', ceil ($dbNbLines/$pNbItems));
		$sp = $sp ->setLimit ($start, $pNbItems);

		return _ioDAO ('copixlog')->findBy ($sp);
	}
    
}
?>