<?php
/**
* @package		copix
* @subpackage	forms
* @author		Croës Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
* @experimental
*/
 
class CopixFormException extends Exception {}

/**
 * Classe principale pour CopixForm
 * @package		copix
 * @subpackage	forms
 */
class CopixFormFactory {
    
    private static $currentId=null;
    
    public static function setCurrentId ($pId) {
        CopixFormFactory::$currentId = $pId;
    }
    
    public static function getCurrentId () {
        return CopixFormFactory::$currentId;
    }
    
	/**
	 * Récupération / création d'un formulaire 
	 * @param string $pId l'identifiant du formulaire à créer. 
	 *  Si rien n'est donné, un nouveau formulaire est crée
	 */
	public static function get ($pId = null){
		//Aucun identifiant donné ? bizarre, mais créons lui un identifiant
		if ($pId === null){
		    if (CopixFormFactory::getCurrentId () === null) {
		    	//@TODO I18N
		    	throw new CopixException ("Aucun ID en cours, vous devez en spécifier un pour votre formulaire");
		    } else {
		        $pId = CopixFormFactory::getCurrentId ();
		    }
		}
		
		CopixFormFactory::setCurrentId ($pId);

		//le formulaire existe ?
		if (isset ($_SESSION['COPIX']['FORMS'][$pId])){
			return $_SESSION['COPIX']['FORMS'][$pId];
		}

		//Création du nouveau formulaire
		return $_SESSION['COPIX']['FORMS'][$pId] = new CopixForm ($pId);
	}
	
	public static function delete ($pId) {
	    if (isset ($_SESSION['COPIX']['FORMS'][$pId])){
	        unset($_SESSION['COPIX']['FORMS'][$pId]);
	    }
	}
}
?>