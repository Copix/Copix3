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
 
class CopixFormCheckException extends CopixException {
	
	private $_arErrors = array ();
	
	public function __construct ($pMessage, $pField = null) {
		if (is_array($pMessage)) {
			$this->_arErrors = $this->_arErrors + $pMessage; 
		} else {
			if ($pField != null) {
			    $this->_arErrors[$pField] = $pMessage;
			} else {
				$this->_arErrors[] = $pMessage;
			}
		}
		parent::__construct ($this->getErrorMessage());
	}
	
	public function getErrors () {
		return $this->_arErrors;
	}
	
	public function getErrorMessage () {
		$toReturn = '';
		foreach ($this->_arErrors as $key=>$error) {
			if (is_array($error)) {
				foreach ($error as $errorMessage) {
					$toReturn .= $key.' : '.$errorMessage."\n";
				}
			} else {
				$toReturn .= $key.' : '.$error."\n";
			}
		}
		return $toReturn;
	}
}

class CopixFormException extends CopixException {} 

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
	
	public static function delete ($pId) {
	    CopixSession::set($pId, null, 'COPIXFORM');
	}
}