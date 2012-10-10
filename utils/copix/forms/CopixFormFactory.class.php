<?php
/**
* @package		copix
* @subpackage	forms
* @author		Croës Gérald, Salleyron Julien
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
* @experimental
*/

/**
 * Classe principale pour CopixForm
 * 
 * @package		copix
 * @subpackage	forms
 */
class CopixFormFactory {
    /**
     * L'identifiant du dernier formulaire crée
     *  
     * @var string
     */
	private static $_currentId = null;
    
    /**
     * On définit le formulaire actuellement manipulé
     * 
     * @param string $pId
     */
	public static function setCurrentId ($pId) {
        CopixFormFactory::$_currentId = $pId;
    }
    
    /**
     * Récupèration du formulaire en cours de manipulation
     *
     * @return string
     */
    public static function getCurrentId () {
        return CopixFormFactory::$_currentId;
    }
    
	/**
	 * Récupération / création d'un formulaire 
	 * @param string $pId l'identifiant du formulaire à créer. 
	 *  Si rien n'est donné, un nouveau formulaire est crée
	 * @return CopixForm
	 */
	public static function get ($pId = null, $pParams = array ()){
		//Aucun identifiant donné ? bizarre, mais créons lui un identifiant
		if ($pId === null){
		    if (CopixFormFactory::getCurrentId () === null) {
		    	//@TODO I18N
		    	throw new CopixFormException ("Aucun ID en cours, vous devez en spécifier un pour votre formulaire");
		    } else {
		        $pId = CopixFormFactory::getCurrentId ();
		    }
		}
		
		CopixFormFactory::setCurrentId ($pId);

		//le formulaire existe ?
		
		$form = CopixSession::get($pId, 'COPIXFORM');
		if ($form === null){
			$form = new CopixForm ($pId);
			CopixSession::set($pId, $form, 'COPIXFORM');
		}
		if (count ($pParams) > 0) {
			$form->setParams ($pParams);
		}
		return $form;
		
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $pId
	 */
	public static function delete ($pId) {
	    CopixSession::set($pId, null, 'COPIXFORM');
	}
}