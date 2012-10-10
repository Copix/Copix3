<?php
/**
 * @package		copix
 * @subpackage	auth
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Exceptions utilisateurs
 *
 * @package		copix
 * @subpackage	auth
 */
class CopixUserException extends CopixException {}

/**
 * Authentification et gestion des droits
 *
 * @package		copix
 * @subpackage	auth
 */
class CopixUser implements ICopixUser {
	/**
	 * Tableau des sources où l'utilisateur est connecté
	 *
	 * @var array
	 */
	private $_logged = array ();

	/**
	 * Cache des éléments déja testés
	 *
	 * @var array
	 */
	private $_asserted = array ();

	/**
	 * Liste des groupes de l'utilisateur
	 *
	 * @var array False veut dire qu'on n'a pas encore listé les groupes
	 */
	private $_groups = array ();

	/**
	 * Demande de connexion
	 *
	 * @param array $pParams Paramètres envoyés à la demande de login
	 * @return bool
	 */
	public function login ($pParams = array ()) {
		$this->_asserted = array ();
		$this->_groups = array ();

		$responses = array();
		$isConnected = false;
		// N.B: les gestionnaires étant triés par rang croissant, les réponses le seront aussi...
		foreach (CopixConfig::instance ()->copixauth_getRegisteredUserHandlers () as $handler) {
			if (!isset ($pParams['handlers']) || (isset ($pParams['handlers']) && in_array ($handler['name'], $pParams['handlers']))) {
				//Si on est en mode append on ne fait rien si le handler est déjà validé
				if (isset ($pParams['append']) && $pParams['append'] && isset ($this->_logged[$handler['name']]) && $this->_logged[$handler['name']] && $this->_logged[$handler['name']]->getResult ()) {
					continue;
				}
				$result = CopixUserHandlerFactory::create ($handler['name'])->login ($pParams);
				if( $result->getResult () ) {
					$isConnected = true;
				} elseif ($handler['required'] === true && (!isset ($pParams['append']) || !$pParams['append'])) {
					//On ne passe pas dans le required si on est en mode append
					$isConnected = false;
					break;
				}
				$responses[$handler['name']] = $result;
			} else if ($handler['required'] === true) {
				$isConnected = false;
				break;
			}
		}
		//Si on est en mode  append on fait un merge
		if (isset ($pParams['append']) && $pParams['append']) {
			$this->_logged = $isConnected ? array_merge($this->_logged, $responses) : $this->_logged;
		} else {
			$this->_logged = $isConnected ? $responses : array();
		}
		return $isConnected;
	}

	/**
	 * Demande de déconnexion
	 *
	 * @param array $pParams Paramètres envoyés à la demande de logout
	 */
	public function logout ($pParams = array ()) {
		foreach (CopixConfig::instance ()->copixauth_getRegisteredUserHandlers () as $handler) {
			CopixUserHandlerFactory::create ($handler['name'])->logout ($pParams);
		}
		//On vide le cache des users quand on se déconnecte
		CopixSession::destroyNamespace ('CopixCacheUser');
		$this->_logged = array ();
		$this->_asserted = array ();
		$this->_groups = array ();
	}

	/**
	 * Retourne la liste des groupes de l'utilisateur, sous la forme d'un tableau (id => caption)
	 *
	 * @return array
	 */
	public function getGroups () {
		$sharedGroupsKey = CopixConfig::instance ()->copixauth_sharedcredentialskey;
		if (array_key_exists ($sharedGroupsKey, $this->_groups) && (CopixConfig::instance ()->copixauth_cache == true)) {
			return $this->_groups[$sharedGroupsKey];
		}
		$results = array ();

		//On parcours la liste des gestionnaires de groupes enregistrés.
		foreach (CopixConfig::instance ()->copixauth_getRegisteredGroupHandlers () as $handlerDefinition) {
			$handler = CopixGroupHandlerFactory::create ($handlerDefinition['name']);
			$arGroupHandler = array ();
			//Pour chaque utilisateur testé lors du processus de login, on demande la liste de ses groupes
			foreach ($this->getResponses(true) as $logResult) {
				foreach ($handler->getUserGroups ($logResult->getId (), $logResult->getHandler()) as $id => $group) {
					$arGroupHandler[$id] = $group;
				}
			}
			//on rajoute également les groupes pour les "non authentifiés" (groupes anonymes par exemple)
			foreach (CopixConfig::instance ()->copixauth_getRegisteredUserHandlers () as $userHandler => $userHandlerInformations) {
				foreach ($handler->getUserGroups (null, $userHandler) as $id => $group) {
					$arGroupHandler[$id] = $group;
				}
			}
			if (count ($arGroupHandler)) {
				$results[$handlerDefinition['name']] = $arGroupHandler;
			}
		}
		return $this->_groups[$sharedGroupsKey] = $results;
	}

	/**
	 * Vérifie les droits sur un élément de l'utilisateur courant. Génère une CopixCredentialException si le droit n'est pas accordé.
	 *
	 * @param string $pString Chaine de droit à tester (ex : basic:admin@news)
	 * @throws CopixCredentialException
	 */
	public function assertCredential ($pString) {
		if (!$this->testCredential ($pString)) {
			throw new CopixCredentialException ($pString);
		}
	}


	/**
	 * Test les droits en retournant true / false
	 *
	 * @param string $pString Chaine de droit à tester (ex : basic:admin@news)
	 * @return bool
	 */
	public function testCredential ($pString) {
		$sharedCredentialsKey = CopixConfig::instance ()->copixauth_sharedcredentialskey;
		if (array_key_exists ($sharedCredentialsKey, $this->_asserted) && array_key_exists ($pString, $this->_asserted[$sharedCredentialsKey]) && (CopixConfig::instance ()->copixauth_cache == true)) {
			return $this->_asserted[$sharedCredentialsKey][$pString];
		}

		$pStringType = substr ($pString, 0, strpos ($pString, ':'));
		$pStringString = substr ($pString, strpos ($pString, ':') + 1);

		$success = false;
		foreach (CopixConfig::instance ()->copixauth_getRegisteredCredentialHandlers() as $handler) {
			if ((is_array ($handler['handle']) && in_array ($pStringType, $handler['handle'])) || $handler['handle'] === 'all') {
				if (!((is_array ($handler['handleExcept']) && in_array ($pStringType, $handler['handleExcept'])) || $handler['handleExcept'] === $pStringType)) {
					$result = CopixCredentialHandlerFactory::create ($handler['name'])->assert ($pStringType, $pStringString, $this);
					if ($result === false) {
						if ($handler['stopOnFailure']) {
							return $this->_asserted[$sharedCredentialsKey][$pString] = false;
						}
						$success = $success || false;
					} else if ($result === true) {
						if ($handler['stopOnSuccess']) {
							return $this->_asserted[$sharedCredentialsKey][$pString] = true;
						}
						$success = true;
					}
				}
			}
		}

		$this->_asserted[$sharedCredentialsKey][$pString] = $success;
		return $success;
	}

	/**
	 * Indique si l'utilisateur courant est connecté
	 *
	 * @return boolean
	 */
	public function isConnected () {
		return (count ($this->_logged) > 0);
	}

	/**
	 * Retourne l'identifiant de l'utilisteur courant
	 *
	 * @return string ou null si non trouvé
	 */
	public function getId () {
		return !is_null($response = $this->_getFirstLogged ()) ? $response->getId () : null;
	}

	/**
	 * Retourne le libellé de l'utilisteur courant
	 *
	 * @return string ou nul si non trouvé
	 */
	public function getCaption () {
		return !is_null($response = $this->_getFirstLogged ()) ? $response->getCaption () : null;
	}

	/**
	 * Retourne le login de l'utilisateur courant
	 *
	 * @return string ou null si non trouvé
	 */
	public function getLogin () {
		return !is_null($response = $this->_getFirstLogged ()) ? $response->getLogin () : null;
	}

	/**
	 * Retourne le nom du gestion de l'utilisateur courant.
	 *
	 * @return string ou null si non trouvé
	 */
	public function getHandler () {
		return !is_null($response = $this->_getFirstLogged ()) ? $response->getHandler () : null;
	}

	/**
	 * Retourne l'identité principale de l'utilisateur (couple )
	 *
	 * @return array Tableau de la forme ("nom_du_gestionnaire", "id_utilisateur") ou null
	 */
	public function getIdentity() {
		return !is_null($response = $this->_getFirstLogged ()) ? $response->getIdentity() : null;
	}

	/**
	 * Retourne la liste des identités de l'utilisateur, i.e. des réponses poi
	 *
	 * @return array Tableau de la forme ("nom_du_gestionnaire", "id_utilisateur"), potentiellement vide
	 */
	public function getIdentities() {
		$toReturn = array();
		foreach($this->_logged as $response) {
			if($response->getResult()) {
				$toReturn[] = $response->getIdentity();
			}
		}
		return $toReturn;
	}

	/**
	 * Retourne la première réponse positive.
	 *
	 * @return CopixUserLogResponse
	 */
	private function _getFirstLogged () {
		// Rappelez vous : les réponses sont classées par rang
		foreach($this->_logged as $response) {
			if($response->getResult()) {
				return $response;
			}
		}
		return null;
	}

	/**
	 * Indique si l'utisateur à été correctement identifié via un handler donné
	 *
	 * @param string $pHandlerName Nom du handler
	 * @return bool
	 * @deprecated
	 * @see CopixUser::isConnectedWith
	 */
	public function isLoggedWith ($pHandlerName) {
		return $this->isConnectedWith ($pHandlerName);
	}

	/**
	 * Indique si l'utilisateur est connecté avec un handler donné.
	 *
	 * @param string $pHandlerName Nom du handler
	 * @return bool
	 */
	public function isConnectedWith ($pHandlerName) {
		foreach($this->_logged as $response) {
			if($response->getResult() && $response->getHandler() == $pHandlerName) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Vérifie si l'utilisateur est connecté avec le gestionnaire et l'identifiant indiqué.
	 *
	 * @param string $$pHandlerName Nom du gestionnaire.
	 * @param mixed $pUserId Identifiant de l'utilisateur.
	 * @return boolean Vrai si l'utilisateur est reconnu.
	 */
	public function isConnectedAs($pHandlerName, $pUserId) {
		foreach($this->_logged as $response) {
			if($response->getResult() && $response->getHandler() == $pHandlerName && $response->getId() == $pUserId) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Indique la réponse qu'a apporté le handler donné lors de la demande de connexion
	 *
	 * @param string $pHandlerName Nom du handler
	 * @return array of CopixUserResponse  / false si aucune réponse
	 */
	public function getHandlerResponse ($pHandlerName) {
		$toReturn = array();
		foreach($this->_logged as $response) {
			if($response->getHandler() == $pHandlerName) {
				$toReturn[] = $response;
			}
		}
		switch(count($toReturn)) {
			case 0: return false;
			case 1: return $toReturn[0];
			default: return $toReturn;
		}
	}

	/**
	 * Retourne les réponses qu'ont apportées les handlers lors des tentatives de connexion
	 *
	 * @return array of CopixUserLogResponse
	 */
	public function getResponses () {
		return $this->_logged;
	}

	/**
	 * recherche l'information $pInformationId dans les réponses apportées durant le processus de login.
	 *
	 * @param string $pInformationId	Le nom de l'information que l'on recherche
	 * @param string $pUserHandler    Dans quelle réponse on cherche. Si rien n'est donné, on prend la première information qui porte le nom demandé
	 */
	public function getExtra ($pInformationId, $pUserHandler = null){
		if ($pUserHandler === null){
			foreach ($this->_logged as $userHandler=>$userResponse){
				$extra = $userResponse->getExtra ();
				if (isset ($extra[$pInformationId])){
					return $extra[$pInformationId];
				}
			}
		}else{
			if (isset ($this->_logged[$pUserHandler])){
				$extra = $this->_logged[$pUserHandler]->getExtra ();
				return isset ($extra[$pInformationId]) ? $extra[$pInformationId] : null;
			}
		}
		return null;
	}

	/**
	 * Définition d'information supplémentaire pour les gestionnaires d'authentification
	 *
	 * @param string $pInformationId    le nom de l'information a définir
	 * @param string $pInformationValue la valeur de l'information a placer
	 * @param string $pUserHandler   pour quel userhandler on va défnir l'information supplémentaire.
	 *                               Si null est donné, on placera l'information dans le premier gestionnaire
	 *                               connecté.
	 * @return boolean si l'information a bien été ajoutée
	 */
	public function setExtra ($pInformationId, $pInformationValue, $pUserHandler = null){
		if ($pUserHandler === null){
			//Si le handler n'est pas spécifié, on prend le premier loggué
			$userHandlerResponse = $this->_getFirstLogged ();
		}else{
			//Si le handler est spécifié, on prend la réponse apportée par ce dernier
			$userHandlerResponse = $this->getHandlerResponse ($pUserHandler);
		}

		if ($userHandlerResponse !== null){
			//Si on est bien connecté avec au moins un utilisateur, il est possible de
			//définir l'information supplémentaire
			return $userHandlerResponse->addExtra ($pInformationId, $pInformationValue);
		}

		//l'information n'a pas pu être ajoutée, pas de réponse valide.
		return false;
	}

    /**
     * Méthode qui vide les caches de droits et de groupes du user
     */
    public function clearCache () {
        $this->_asserted = array ();
        $this->_groups   = array ();
    }
}

/**
 * Enregistrement des réponses des handlers
 *
 * @package		copix
 * @subpackage	auth
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
	 *
	 * @param bool $pOk Résultat de la demande de connexion
	 * @param string $pHandler Nom du handler
	 * @param mixed $pId Identifiant de l'utilisateur
	 * @param string $pLogin Login de l'utilisateur
	 * @param array $pExtra Informations supplémentaires
	 */
	public function __construct ($pOk, $pHandler, $pId, $pLogin, $pExtra = array ()) {
		$this->_data['result'] = $pOk;
		$this->_data['handler'] = $pHandler;
		$this->_data['id'] = $pId;
		$this->_data['login'] = $pLogin;
		$this->_data['extra'] = $pExtra;
	}

	/**
	 * Récupère le résultat de la connexion
	 *
	 * @return boolean
	 */
	public function getResult () {
		return $this->_data['result'];
	}

	/**
	 * Récupère l'identifiant unique de la personne connectée
	 *
	 * @return string
	 */
	public function getId () {
		return $this->_data['id'];
	}

	/**
	 * Récupère le login de la personne
	 *
	 * @return string
	 */
	public function getLogin () {
		return $this->_data['login'];
	}

	/**
	 * Récupère le libellé à appliquer à l'utilisateur
	 *
	 * @return string
	 */
	public function getCaption () {
		if (isset ($this->_data['extra']['caption'])) {
			return $this->_data['extra']['caption'];
		}
		return $this->getLogin ();
	}

	/**
	 * Récupère le handler capable de gérer l'utilisateur
	 *
	 * @return string
	 */
	public function getHandler () {
		return $this->_data['handler'];
	}

	/**
	 * Récupération des données supplémentaires qui ont put être fournies par le système d'authentification
	 *
	 * @return array
	 */
	public function getExtra () {
		return $this->_data['extra'];
	}

	/**
	 * Retourne le couple (handlerName, userId) qui identifie l'utilisateur
	 *
	 * @return array(handlerName, userId)
	 */
	public function getIdentity () {
		return array($this->_data['handler'], $this->_data['id']);
	}

	/**
	 * Ajoute une information dans le gestionnaire d'utilisateur
	 *
	 * @return boolean si l'information a été mise à jour ou non
	 */
	public function addExtra ($pInformationName, $pInformationValue, $pOverwrite = true){
		if (! $pOverwrite){
			if (array_key_exists ($pInformationName, $this->_data['extra'])){
				return false;
			}
		}

		$this->_data['extra'][$pInformationName] = $pInformationValue;
		return true;
	}
}
?>