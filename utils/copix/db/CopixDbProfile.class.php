<?php
/**
 * @package		copix
 * @subpackage	db
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */


/**
 * Un profil de connexion à une base de données
 * @package		copix
 * @subpackage	db
 */
class CopixDBProfile {
	/**
	 * Pour spécifier une connexion persistante
	 * Valeur de PDO::ATTR_PERSISTENT, mais on n'utilise pas la constante dans le cas ou PDO n'est pas installé
	 */
	const PERSISTENT = 12;
	 
	/**
	 * Pour spécifier l'émulation des prepares et non des prépares réels
	 * Valeur de PDO::ATTR_EMULATE_PREPARES, mais on n'utilise pas la constante dans le cas ou PDO n'est pas installé
	 */
	const EMULATE_PREPARES = 20;

	/**
	 * Nom du profil
	 * @var string
	 */
	private $_name;

	/**
	 * Chaine de connexion
	 * @var string
	 */
	private $_connectionString;

	/**
	 * Utilisateur
	 * @var string
	 */
	private $_user;

	/**
	 * Mot de passe
	 * @var string
	 */
	private $_password;

	/**
	 * Nom du driver
	 *
	 * @var string
	 */
	private $_driverName = null;

	/**
	 * Type de la base de données
	 *
	 * @var string
	 */
	private $_database = null;

	/**
	 * options diverses
	 * @var array
	 */
	private $_options = array ();

	/**
	 * Construction
	 * @param	string	$pName nom du profil de connexion
	 * @param	string	$pString la chaine de connexion
	 * @param	string	$pUser le nom de l'utilisateur utilisé pour se connecter
	 * @param	string	$pPassword le mot de passe pour se connecter à la base 
	 * @param	array	$pOptions un tableau d'options, souvent spécifiques aux différents drivers
	 */
	public function __construct ($pName, $pString, $pUser, $pPassword, $pOptions = array ()) {
		$this->_name = $pName;
		$this->_connectionString = $pString;
		$this->_user = $pUser;
		$this->_password = $pPassword;
		$this->_options = $pOptions;

		if (($position = strpos ($this->_connectionString, ':')) === false){
			throw new CopixDBException ('[CopixDBProfile] Nom du driver manquant pour le profil '.$this->_name);
		}
		$this->_driverName = substr ($this->_connectionString, 0, $position);
		$this->_database = CopixDb::driverToDatabase ($this->_driverName);
	}

	/**
	 * Récupère le nom de la connexion
	 * @return string
	 */
	public function getName (){
		return $this->_name;
	}

	/**
	 * Récupère la chaine de connexion
	 * @return string
	 */
	public function getConnectionString (){
		return $this->_connectionString;
	}

	/**
	 * Récupère l'utilisateur de base de données
	 * @return string
	 */
	public function getUser (){
		return $this->_user;
	}

	/**
	 * Récupère le mot de passe de la base
	 * @return string
	 */
	public function getPassword (){
		return $this->_password;
	}

	/**
	 * Récupère les informations de la chaine de connexion sous la forme d'un tableau associatif
	 * @return array
	 */
	public function getConnectionStringParts (){
		$toReturn = array ();
		$driverName = $this->_driverName;

		$parts = explode (';', substr ($this->_connectionString, strlen ($driverName)+1));
		foreach ($parts as $part){
			$position = strpos ($part, '=');
			$toReturn[substr ($part, 0, $position)] = substr ($part, $position+1);
		}
		return $toReturn;
	}

	/**
	 * Récupère le nom du driver (driverName:chaineDeConnexionComplete)
	 * @return string
	 */
	public function getDriverName (){
		return $this->_driverName;
	}
	
	/**
	 * Récupère les options définies pour le driver
	 * @return array
	 */
	public function getOptions (){
		return $this->_options;
	}

	/**
	 * Indique la valeur d'une option
	 * @param	mixed	$pOption	L'option dont on souhaite connaitre la valeur
	 * @return mixed
	 */
	public function getOption ($pOption) {
		if (isset ($this->_options[$pOption])) {
			return $this->_options[$pOption];
		}
		return null;
	}

	/**
	 * Défini un certain nombre d'options dans le driver
	 * @param array $arOptions tableau d'options à définir.
	 *   On rajoutera les options trouvées aux options actuelles
	 */
	public function setOptions ($pArOptions) {
		if (count ($pArOptions)){
			$this->_options = array_merge ($pArOptions, $this->_options);
		}
	}

	/**
	 * Supprime les options définies dans le driver
	 */
	public function clearOptions () {
		$this->_options = array ();
	}

	/**
	 * Récupère le type de base de données
	 * Les types standards définis par Copix sont OCI, MYSQL, MSSQL, PGSQL
	 * @return string
	 */
	public function getDatabase () {
		return $this->_database;
	}
}