<?php
/**
* @package   copix
* @subpackage core
* @author   Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Exceptions potentiellement générées par les services
 * @package copix
 * @subpackage core
 */
class CopixServicesException extends Exception {}

/**
 * Cette classe permet de définir des services qui seront appelés dans un contexte transactionnel, 
 *  et ce de façon automatique.
 * @package copix
 * @subpackage core
 */
class CopixServices {
	/**
	 * Paramètres passés au service. 
	 */
	private $_params = array ();
	
	/**
	 * Constructeur privé 
	 */
	protected function __construct ($pParams){
		$this->_params = $pParams;
	}

	/**
    * Extraction du chemin à partir de l'identifiant donné(de la forme module|service::nomMethode)
    * Si aucun module n'est donné, on utilise le contexte courant.
    * @param string $pServiceIf L'identifiant du service
    */
	protected static function _extractPath ($pServiceId){
		$extract = explode ('|', $pServiceId);
		if (count ($extract) == 1){
			return self::_extractPath (CopixContext::get ().'|'.$pServiceId);
		}

		$extractMethod = explode ('::', $extract[1]);
		if (count ($extractMethod) !== 2){
			throw new CopixServicesException ('Wrong Service ID '.$pServiceId);
		}

		$extracted = new StdClass ();
		$extracted->module  = $extract[0] === '' ? CopixContext::get () : $extract[0];
		$extracted->service = $extractMethod[0];
		$extracted->method  = $extractMethod[1];

		return $extracted;
	}
	
	/**
	* Création d'une instance d'un objet service
	* @param Object $pServiceDescription un objet qui décrit les composantes du service (avec les propriétés module, service, methode)
	*/
	private static function _create ($pServiceDescription, $pParams = array ()){
        $serviceID = $pServiceDescription->module.'|'.$pServiceDescription->service;

        $execPath = CopixModule::getPath ($pServiceDescription->module);
		$fileName = $execPath.COPIX_CLASSES_DIR.strtolower (strtolower ($pServiceDescription->service)).'.services.php';
		if (! Copix::RequireOnce ($fileName)){
			throw new CopixServicesException ('Cannot load service from '.$fileName);
		}

		//Nom des objets/méthodes à utiliser.
		$objName  = 'Services'.$pServiceDescription->service;
		return new $objName ($pParams);
	}
	
	/**
    * Demande l'exécution d'un service donné
    * @param string $pServiceId L'identifiant du service que l'on souhaite lancer
    * @param array $vars parameters
    */
	public static function process ($pServiceId, $pParams=array (), $pTransactionContext = null){
		$extractedPath = self::_extractPath ($pServiceId);
	
		$service = self::_create ($extractedPath, $pParams);
		$methName = $extractedPath->method;

		CopixContext::push ($extractedPath->module);
		try {
			if ($pTransactionContext == self::NEW_TRANSACTION){
			   CopixDB::begin ();
			}
		    $toReturn = $service->$methName ();
			if ($pTransactionContext == self::NEW_TRANSACTION){
   		    	CopixDB::commit ();
			}
		    CopixContext::pop ();
		    return $toReturn;
		}catch (Exception $e){
			if ($pTransactionContext == self::NEW_TRANSACTION){
 				CopixDB::rollback ();
			}
		    CopixContext::pop ();
		    throw $e;
		}
	}

	/**
    * Gets the value of a request variable. If not defined, gets its default value.
    * @param string $varName the name of the request variable
    * @param mixed $varDefaultValue the default value of the request variable
    * @return mixed the request variable value
    */
	protected function getParam ($pParamName, $pParamDefaultValue=null){
		if (array_key_exists ($pParamName, $this->_params)){
			return $this->_params[$pParamName];
		}else{
			return $pParamDefaultValue;
		}
	}
	
	/**
	 * Nouvelle transaction,
	 */
	const NEW_TRANSACTION = 1;
	
	/**
	 * Transaction
	 */
	const CURRENT_TRANSACTION = 2; 
}
?>