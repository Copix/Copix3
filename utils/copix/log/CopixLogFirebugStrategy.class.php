<?php
/**
* @package   copix
* @subpackage log
* @author    Patrice Ferlet
* @copyright CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Stratégie de stockage de logs dans la session
 * @package	copix
 * @subpackage log
 */
class CopixLogFireBugStrategy implements ICopixLogSTrategy {
	/**
	 * Sauvegarde les logs dans le fichier
	 *
	 * @param String $pMessage log à sauvegarder
	 * @param String $tab tableau d'option
	 */
	public function log ($pProfil, $pType, $pLevel, $pDate, $pMessage, $pArExtra){
		$log = array ('date'=>$pDate, 'message'=>$pMessage, 'level'=>$pLevel, 'type'=>$pType);
		//on concatene le tableau info avec le tableau issu du backtrace
		$log = $log + $pArExtra;
		CopixSession::push ('copix|log|firebug|'.$pProfil, $log);
	}
	
	/**
	 * Supprime tout les log de ce profil
	 * @param	string	$pProfil	le nom du profil à vider
	 */
	public function deleteProfile ($pProfil){
		CopixSession::set ('copix|log|firebug|'.$pProfil, null);
	}

	/**
	 * Retourne les logs sous forme d'itérateur
	 * @param	string	$pProfil	Le nom du profil dont on souhaite récupérer les logs
	 * @return array
	 */
	public function getLog ($pProfil){	
		if (is_array ($profile = CopixSession::get ('copix|log|firebug|'.$pProfil))){
	        $arLog = array ();
	        foreach ($profile as $log){
   				$object = new StdClass();
				$object->date = $log['date'];
				$object->classname = $log['classname'];
				$object->message = $log['message'];						
				$object->line = $log['line'];		
				$object->file = $log['file'];		
				$object->functionname = $log['functionname'];	
				$object->user = $log['user'];										
				$object->level = $log['level'];
				$object->profil = $pProfil;
				$object->type = $log['type'];
        		$arLog[] = $object;
	        }
	        $arrayObject = new ArrayObject (array_reverse ($arLog));
	        return $arrayObject->getIterator();
		}
		return new ArrayObject ();
	}
}
?>