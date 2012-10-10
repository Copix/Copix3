<?php
/**
* @package		copix
* @subpackage	auth
* @author		Croës Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Classe de base pour les exceptions utilisateurs
 * @package		copix
 * @subpackage	auth
 */
class CopixUserException extends CopixException {}

/**
 * Classe de base pour l'authentification et la gestion des droits
 * @package   copix
 * @subpackage auth
 */
class CopixUser {
	/**
	 * Tableau des sources ou l'utilisateur est connecté
	 */
	private $_logged = array ();
	
	/**
	 * Cache des éléments déja testés
	 */
	private $_asserted = array ();
	
	/**
	 * Liste des groupes de l'utilisateur
	 */
	private $_groups = false;

    /** 
     * Gère une demande de connexion
     * @param	array	$pParams	paramètres envoyés à la demande de login 
     */
    public function login ($pParams = array ()) {
    	$this->_asserted = array ();
    	$this->_groups = false;
    	 
    	$results = array ();
    	foreach (CopixConfig::instance ()->copixauth_getRegisteredUserHandlers () as $handler){
    		$result = CopixUserHandlerFactory::create ($handler['name'])->login ($pParams);
    		if (! $result->getResult ()){
    			if ($handler['required'] === true){
    				$this->_logged = array ();
    				return false;
    			}
    		}
    		$this->_logged[$handler['name']] = $result;
	   	}

	   	//On regarde si au moins un des handlers à répondu OK
	   	foreach ($this->_logged as $handlerName=>$handlerResult){
	   		if ($handlerResult->getResult ()){
	   			return true;
	   		}
	   	}
	   	
	   	$this->_logged = array ();	   	
	   	return false;
    }
    
    /**
     * Gère une demande de connexion
     * @param	array	$pParams	paramètres envoyés à la demande de logout 
     */
    public function logout ($pParams = array ()) {
    	foreach (CopixConfig::instance ()->copixauth_getRegisteredUserHandlers () as $handler){
    		CopixUserHandlerFactory::create ($handler['name'])->logout ($pParams);
	   	}
	   	$this->_logged = array ();
    	$this->_asserted = array ();
    	$this->_groups = false;
    }

    /**
     * Retourne la liste des groupes (id, caption) de l'utilisateur
     */
    public function getGroups (){
    	if ($this->_groups !== false){
    		return $this->_groups;
    	}
	   	$results = array ();
    	foreach (CopixConfig::instance ()->copixauth_getRegisteredGroupHandlers () as $handler){
    		$arGroupHandler = array ();
    		foreach ($this->_logged as $userHandler=>$logResult){
    			if ($logResult->getResult ()){
		    		foreach (CopixGroupHandlerFactory::create ($handler['name'])->getUserGroups ($logResult->getId (), $userHandler)
		    		as $id=>$group){
		    			$arGroupHandler[$id] = $group;
		    		}
    			}
    		}
    		if (count ($arGroupHandler)){
    			$results[$handler['name']] = $arGroupHandler;
    		}
	   	}
	   	return $this->_groups = $results;
    }
    
    /**
     * Test les droits en retournant true / false 
     */
    public function testCredential ($pString){
    	if (isset ($this->_asserted[$pString]) && (CopixConfig::instance ()->copixauth_cache == true)){
    		return $this->_asserted[$pString]; 
    	}

    	$pStringType   = substr ($pString, 0, strpos ($pString, ':'));
    	$pStringString = substr ($pString, strpos ($pString, ':')+1);

    	$success = false;
    	foreach (CopixConfig::instance ()->copixauth_getRegisteredCredentialHandlers() as $handler){
    		if ((is_array ($handler['handle']) && in_array ($pStringType, $handler['handle'])) || $handler['handle'] === 'all'){
    			if (! ((is_array ($handler['handleExcept']) && in_array ($pStringType, $handler['handleExcept'])) 
    			       || $handler['handleExcept'] === $pStringType)){
		    		$result = CopixCredentialHandlerFactory::create ($handler['name'])->assert ($pStringType, $pStringString, $this);
		    		if ($result === false){
		    			if ($handler['stopOnFailure']){
		    				return $this->_asserted[$pString] = false;
		    			}
		    			$success = $success || false;
		    		}elseif ($result === true){
		    			if ($handler['stopOnSuccess']){
		    				return $this->_asserted[$pString] = true;
		    			}
		    			$success = true;
		    		}
    			}
	   		}
   		}
   		
   		$this->_asserted[$pString] = $success;
   		return $success;
    }

    /**
     * Vérifie les droits sur un élément de l'utilisateur courant
     *
     * @param string $pString
     */
    public function assertCredential ($pString){
    	if (!$this->testCredential ($pString)){
	   		throw new CopixCredentialException ('Pas les droits');
    	}
    }

    /**
     * Indique si l'utilisateur courant est connecté
     * 
     * @return boolean
     */
    public function isConnected (){
    	return (count ($this->_logged) > 0);
    }

    /**
     * Retourne l'identifiant de l'utilisteur courant
     * @return string ou null si non trouvé
     */
    public function getId () {
    	try {
    		return $this->_getFirstLogged ()->getId ();
    	}catch (Exception $e){
    		return null;
    	}
    }
    
    /**
     * Retourne le libellé de l'utilisteur courant
     */
    public function getCaption () {
    	try {
    		return $this->_getFirstLogged ()->getCaption ();
    	}catch (Exception $e){
    		return null;
    	}
    }
    
    /**
     * Retourne le login de l'utilisateur courant
     * @return string ou null si non trouvé
     */
    public function getLogin () {
    	if ($this->isConnected ()){
    		try {
    			return $this->_getFirstLogged ()->getLogin ();
    		}catch (Exception $e){
    			return null;
    		}
    	}
    	return null;
    }

    /**
     * Retourne le premier élément de réponse loggé pour l'utilisateur
     * @return CopixUserLogResponse 
     */
    private function _getFirstLogged (){
    	foreach ($this->_logged as $logResponse){
    		if ($logResponse->getResult ()){
    			return $logResponse;
    		}
    	}
    	//TODO: I18N
    	throw new CopixException ('Demande non valide, aucune connexion active');
    }

    /**
     * Indique si l'utisateur à été correctement identifié via un driver donné
     * @return boolean
     */
	public function isLoggedWith ($pHandlerName){
		return isset ($this->_logged[$pHandlerName]) && $this->_logged[$pHandlerName]->getResult ();
	}

	/**
	 * Indique la réponse qu'a apporté le handler donné lors de la demande de connexion
	 * @return CopixUserResponse / array of CopixUserResponse 
	 */
	public function getHandlerResponse ($pHandlerName){
		return isset ($this->_logged[$pHandlerName]) ? $this->_logged[$pHandlerName] : false; 
	}

	/**
	 * Retourne les réponse qu'ont apportés les handler lors des tentatives de connexion
	 * @return array of CopixUserResponse
	 */
	public function getResponses (){
		return $this->_logged;
	}
}

/**
 * @package		copix
 * @subpackage 	auth
 */
class CopixUserLogResponse {
	/**
	 * Résultats de l'authentification
	 *
	 * @var array
	 */
	private $_data = array ();

	/**
	 * Construction
	 */
	public function __construct ($pOk, $pHandler, $pId, $pLogin, $pExtra = array ()){
		$this->_data['result']  = $pOk;
		$this->_data['handler'] = $pHandler;
		$this->_data['id']      = $pId;
		$this->_data['login']   = $pLogin;
		$this->_data['extra']   = $pExtra;
	}

	/**
	 * Récupère le résultat de la connexion
	 *
	 * @return boolean
	 */
	public function getResult (){
		return $this->_data['result'];
	}

	/**
	 * Récupère l'identifiant unique de la personne connectée
	 *
	 * @return string
	 */
	public function getId (){
		return $this->_data['id'];
	}

	/**
	 * Récupère le login de la personne
	 *
	 * @return string
	 */
	public function getLogin (){
		return $this->_data['login'];
	}

	/**
	 * Récupère le libellé à appliquer à l'utilisateur
	 *
	 * @return string
	 */
	public function getCaption (){
		if (isset ($this->_data['extra']['caption'])){
			return $this->_data['extra']['caption'];
		}
		return $this->getLogin ();
	}

	/**
	 * Récupère le handler capable de gérer l'utilisateur
	 * 
	 * @return string
	 */
	public function getHandler (){
		return $this->_data['handler'];
	}

	/**
	 * Récupération des données supplémentaires qui ont put être fournies par le système d'authentification
	 *
	 * @return array
	 */
	public function getExtra (){
		return $this->_data['extra'];
	}
}
?>