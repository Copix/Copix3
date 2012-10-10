<?php
/**
* @package		copix
* @subpackage	lists
* @author		Salleyron Julien
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
* @experimental
*/

/**
 * Exceptions de base pour les listes
 * @package copix
 * @subpackage lists
 */
class CopixListException extends Exception {}

/**
 * Classe principale pour CopixList
 * @package		copix
 * @subpackage	lists
 */
class CopixListFactory {
    
    private static $currentId = array();
    
    public static function pushCurrentId ($pId) {
        array_push(CopixListFactory::$currentId,$pId);
        //_log('pop : '.CopixListFactory::getCurrentId(), 'factory');
    }
    
    public static function getCurrentId () {
       // _log('ahhh','factory');
        return (count (CopixListFactory::$currentId)> 0) ? CopixListFactory::$currentId[count(CopixListFactory::$currentId)-1] : null; 
    }
    
    public static function popCurrentId () {
        //_log('pop : '.CopixListFactory::getCurrentId(), 'factory');
        if (count (CopixListFactory::$currentId)> 0) {
            array_pop (CopixListFactory::$currentId);
        }
    }
	/**
	 * Récupération / création d'un formulaire 
	 * @param string $pId l'identifiant du formulaire à créer. 
	 *  Si rien n'est donné, un nouveau formulaire est crée
	 * @return CopixList
	 */
	public static function get ($pId = null){
		//Aucun identifiant donné ? bizarre, mais créons lui un identifiant
		if ($pId === null){
		    if (CopixListFactory::getCurrentId () === null) {
		    	//@TODO I18N
		    	throw new CopixException ("Aucun ID en cours, vous devez en spécifier un pour votre formulaire");
		    } else {
		        $pId = CopixListFactory::getCurrentId ();
		    }
		}
		if ($pId != CopixListFactory::getCurrentId ()) {
		    CopixListFactory::pushCurrentId ($pId);
		}
		
		//le formulaire existe ?
	    $list = CopixSession::get ($pId, 'COPIXLIST');
		if ($list != null){
			return $list;
		}
		$list = new CopixList ($pId);
		CopixSession::set ($pId, $list, 'COPIXLIST');
		//Création du nouveau formulaire
		return $list;
	}
	
	public static function delete ($pId) {
		CopixSession::set ($pId, null, 'COPIXLIST');
	}
}
?>