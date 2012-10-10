<?php
/**
 * @package copix
 * @subpackage utils
 * @author Croës Gérald
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Exceptions potentiellement générées par les services
 * 
 * @package copix
 * @subpackage utils
 */
class CopixServicesException extends Exception {}

/**
 * Cette classe permet de définir des services qui seront appelés dans un contexte transactionnel, et ce de façon automatique.
 * 
 * @package copix
 * @subpackage utils
 */
class CopixServices {
	/**
	 * Nouvelle transaction
	 */
	const NEW_TRANSACTION = 1;
	
	/**
	 * Transaction courante
	 */
	const CURRENT_TRANSACTION = 2; 
	
	/**
	 * Paramètres passés au service
	 * 
	 * @var array
	 */
	private $_params = array ();
	
	/**
	 * Constructeur privé
	 * 
	 * @param array $pParams Paramètres
	 */
	protected function __construct ($pParams) {
		$this->_params = $pParams;
	}

	/**
     * Extraction du chemin à partir de l'identifiant donné (de la forme module|service::nomMethode). Si aucun module n'est donné, on utilise le contexte courant
     * 
     * @param string $pServiceId Identifiant du service
     * @return string
     */
	protected static function _extractPath ($pServiceId) {
		$extract = explode ('|', $pServiceId);
		if (count ($extract) == 1) {
			return self::_extractPath (CopixContext::get () . '|' . $pServiceId);
		}

		$extractMethod = explode ('::', $extract[1]);
		if (count ($extractMethod) !== 2) {
			throw new CopixServicesException ('Wrong Service ID ' . $pServiceId);
		}

		$extracted = new StdClass ();
		$extracted->module  = ($extract[0] === '') ? CopixContext::get () : $extract[0];
		$extracted->service = $extractMethod[0];
		$extracted->method  = $extractMethod[1];

		return $extracted;
	}
	
	/**
	 * Création d'une instance d'un objet service
	 * 
	 * @param Object $pServiceDescription Objet qui décrit les composantes du service (avec les propriétés module, service, methode)
	 * @return CopixServices
	 */
	private static function _create ($pServiceDescription, $pParams = array ()) {
        $serviceID = $pServiceDescription->module . '|' . $pServiceDescription->service;

        $execPath = CopixModule::getPath ($pServiceDescription->module);
		$fileName = $execPath . COPIX_CLASSES_DIR . strtolower (strtolower ($pServiceDescription->service)) . '.services.php';
		if (!Copix::RequireOnce ($fileName)) {
			throw new CopixServicesException ('Cannot load service from ' . $fileName);
		}

		//Nom des objets/méthodes à utiliser.
		$objName  = 'Services' . $pServiceDescription->service;
		return new $objName ($pParams);
	}
	
	/**
     * Demande l'exécution d'un service donné
     * 
     * @param string $pServiceId Identifiant du service que l'on souhaite lancer
     * @param array $pParams parameters
     * @param int $pTransactionContext Contexte de la transaction, utiliser les constantes CopixServices::NEW_TRANSACTION et CURRENT_TRANSACTION
     * @return mixed
     */
	public static function process ($pServiceId, $pParams = array (), $pTransactionContext = CopixServices::NEW_TRANSACTION) {
		$extractedPath = self::_extractPath ($pServiceId);
	
		$service = self::_create ($extractedPath, $pParams);
		$methName = $extractedPath->method;

		CopixContext::push ($extractedPath->module);

		
		try {
			if ($pTransactionContext == self::NEW_TRANSACTION) {
			   CopixDB::begin ();
			}
		    $toReturn = $service->$methName ();

		    if ($pTransactionContext == self::NEW_TRANSACTION) {
   		    	CopixDB::commit ();
			}
		    CopixContext::pop ();
		    return $toReturn;
		} catch (Exception $e) {
			if ($pTransactionContext == self::NEW_TRANSACTION) {
 				CopixDB::rollback ();
			}
		    CopixContext::pop ();
		    throw $e;
		}
	}

	/**
     * Retourne la valeur pour un paramètre donné. Si ce paramètre n'existe pas, retourne la valeur par défaut
     * 
     * @param string $pParamName Nom du paramètre
     * @param mixed $pParamDefaultValue Valeur par défaut
     * @return mixed
     */
	protected function getParam ($pParamName, $pParamDefaultValue = null) {
		if (array_key_exists ($pParamName, $this->_params)) {
			return $this->_params[$pParamName];
		} else {
			return $pParamDefaultValue;
		}
	}
	
	/**
	 * Retourne tous les paramètres et leur valeur
	 *
	 * @return array
	 */
	protected function getParams () {
		return $this->_params;
	}
}