<?php
/**
 * Classe de gestion des sessions avec CURL
 * On construit un objet CopixHttpClient en insérant dans un même navigateur CURL
 * une URL permettant de s'authentifier sur un site et une URL (facultative) qui ferme la session.
 * On ajoute les autres requêtes dans la session
 * 
 * @author Alexandre JULIEN
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */
class Session {
	
	/**
	 * Objet CopixHttpClient contenant les requêtes de la session
	 * 
	 * @var CopixHttpClient
	 */
	private $_copixHttpClient;
	
	/**
	 * File de requêtes
	 *
	 * @var array
	 */
	private $_requestQueue = array ();
	
	/**
	 * Objet CopixHttpClient contenant la requête d'authentification pour créer la session
	 *
	 * @var CopixHttpClient
	 */
	private $_login;
	
	/**
	 * Objet CopixHttpClient contenant la requête de déconnexion pour supprimer la session
	 *
	 * @var CopixHttpClient
	 */
	private $_logout;
	
	/**
	 * Constructeur, création de l'objet CopixHttpClient
	 * On passe la requête pour s'authentifier et éventuellement la requête pour se déconnecter
	 * @param $login : CopixHttpClientRequest
	 * @param $logout : CopixHttpClientRequest (facultatif)
	 * @return void
	 */
	public function Session ($login, $logout) {
		$this->_copixHttpClient = new CopixHttpClient ();
		if ($login !== null) {
			$this->_login = $login;
		}
		if ($logout !== null) {
			$this->_logout = $logout;
		}
	}
	
	/**
	 * Modificateur du login
	 * 
	 * @param CopixHttpClientRequest $id_login
	 * @return void
	 */
	public function setLogin ($login) {
		$this->_login = $login;
	}
	
	/**
	 * Modification du logout
	 *
	 * @param CopixHttpClientRequest $id_logout
	 * @return void
	 */
	public function setLogout ($logout) {
		$this->_logout = $logout;
	}
	
	/**
	 * Insertion des requêtes à effectuer dans la session
	 *
	 * @param CopixHttpClientRequest $request
	 * @return void
	 */
	public function addHttpClientRequest ($request) {
		$this->_requestQueue[] = $request;
	}
	
	/**
	 * Accesseur, retourne le CopixHttpClient
	 * Assemblage les requêtes
	 * 
	 * @return CopixHttpClient
	 */
	public function getHttpClientRequest () {
		if ($this->_login) {
			$this->_copixHttpClient->addRequest($this->_login);
		}
		foreach ($this->_requestQueue as $request) {
			$this->_copixHttpClient->addRequest($request);
		}
		if ($this->_logout) {
			$this->_copixHttpClient->addRequest($this->_logout);
		}
		return $this->_copixHttpClient;
	}
}
?>