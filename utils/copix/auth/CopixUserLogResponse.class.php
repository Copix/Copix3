<?php
/**
 * @package copix
 * @subpackage auth
 * @author Gérald Croës
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 */

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