<?php
/**
* @package		copix
* @subpackage	db
* @author		Croës Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Classe de base pour les exceptions de base de données
 * @package		copix
 * @subpackage	db
 */
class CopixDBException extends CopixException {}

/**
 * Interface de base pour tous les drivers que l'on peut souhaiter implémenter
 * @todo le faire implémenter par les drivers lorsque le Bug PHP sera résolu
 * @package copix
 * @subpackage db
 */
interface ICopixDBConnection {
	public function iDoQuery ($pQueryString, $pParameters = array (), $pOffset = null, $pCount = null);
	public function doQuery ($pQueryString, $pParameters = array (), $pOffset = null, $pCount = null);
	public function lastId ($pFromSequence = null);
    public function commit ();
    public function rollback ();
    public function begin ();
    public function getTableList ();
    public function getFieldList ($pTableName);
    public static function isAvailable ();
}

/**
 * Description des champs d'une table
 * @package copix
 * @subpackage db
 */
class CopixDBFieldDescription {
    
    /**
     * Nom du champ
     */
    public $name;
    
    /**
     * Est ce que le champ accepte les valeurs null
     */
    public $notnull;
    
    /**
     * Clé primaire
     */
    public $pk;
    
    /**
     * Type du champ
     */
    public $type;
    
    /**
     * Longueur du champ
     */
    public $length;
    
	/**
	 * Construction de la description d'un champ
	 *
	 * @param string $pName	le nom du champ
	 */
    public function __construct ($pName){
	    $this->name = $pName;
	} 
}

/**
 * Un profil de connexion à une base de données
 * @package		copix
 * @subpackage	db
 */
class CopixDBProfile {
	/**
	 * Pour spécifier une connexion persistante.
	 */
	const PERSISTENT = PDO::ATTR_PERSISTENT;
     
	/**
	 * Pour spécifier l'émulation des prepares et non des prépares réels
	 */
	const EMULATE_PREPARES = PDO::ATTR_EMULATE_PREPARES;

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
    	$driverName = $this->getDriverName (); 

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
    	if (($position = strpos ($this->_connectionString, ':')) === false){
    		throw new CopixDBException ('[CopixDBProfile] Nom du driver manquant pour le profil '.$this->_name);
    	}
    	return substr ($this->_connectionString, 0, $position);
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
    public function getOption ($pOption){
    	if (isset ($this->_options[$pOption])){
    		return $this->_options[$pOption];
    	}
    	return null;
    }
    
    /**
     * Défini un certain nombre d'options dans le driver
     * @param array $arOptions tableau d'options à définir.
     *   On rajoutera les options trouvées aux options actuelles
     */
    public function setOptions ($pArOptions){
    	if (count ($pArOptions)){
    		$this->_options = array_merge ($pArOptions, $this->_options);
    	}
    }
    
    /**
     * Supprime les options définies dans le driver
     */
    public function clearOptions (){
    	$this->_options = array ();
    }
    
    /**
     * Récupère le type de base de données
     * Les types standards définis par Copix sont OCI, MYSQL, MSSQL, PGSQL
     * @return string
     */
    public function getDatabase (){
    	static $driversBase = array ('mysql'=>'mysql', 'pdo_mysql'=>'mysql', 'oci'=>'oci', 
              'pdo_oci'=>'oci', 'mssql'=>'mssql', 'pdo_mssql'=>'pdo_mssql', 'dblib'=>'mssql', 
              'pdo_dblib'=>'mssql','pdo_sqlite'=>'sqlite','pdo_pgsql'=>'pgsql');
    	$driverName = $this->getDriverName ();
    	if (isset ($driversBase[$driverName])){
    		return $driversBase[$driverName];
    	}else{
    		throw new CopixDBException ('[CopixDBProfile] Type de base inconnu pour le driver '.$driverName);
    	}
    }
}

/**
 * Classe pour manipuler les bases de données
 * 
 * @package   copix
 * @subpackage db
 */
class CopixDB {
	/**
	 * Tableau des connexions utilisées pour les transactions en cours
	 * @var array
	 */
	private static $_transactions = array ();

	/**
	 * Tableau des identifiants de transaction en cours
	 * @var array
	 */
	private static $_transactionId = array ();

	/**
	 * Récupère une connexion disponible
	 * <code>
	 *  //Récupération de la connexion par défaut
	 *  $ct = CopixDB::getConnection ();
	 *  //récupération de la connexion nommée "test" (qui aura été définie comme telle dans 
	 *  le fichier de configuration, par exemple $config->copix_db_defineprofile ('test', ....);
	 *  $ct = CopixDB::getConnection ('test');
	 * </code>
	 * @param	string	$pNamed	le nom de la connexion que l'on souhaite récupérer
	 * @return	CopixDBConnection 
	 */
	public static function getConnection ($pNamed = null){
		if ($pNamed === null){
           if (($pNamed = CopixConfig::instance ()->copixdb_getDefaultProfileName ()) === null){
           	throw new CopixDBException ('Aucun profil de base défini par défaut.');
           }			
		}

		$currentTransaction = self::getCurrentTransactionId ();
		if (! isset (self::$_transactions[$currentTransaction][$pNamed])){
		   $pProfil = CopixConfig::instance ()->copixdb_getProfile ($pNamed);
		   self::$_transactions[$currentTransaction][$pNamed] = CopixDB::_createConnection ($pProfil);
		   if ($currentTransaction !== 'default'){
   		      self::$_transactions[$currentTransaction][$pNamed]->begin ();
		   }
		}
        return self::$_transactions[$currentTransaction][$pNamed];		
	}

	/**
	 * Demarre une transaction. 
	 * 
	 * Seule les connexions récupérées après l'appel à begin seront inclues dans la transaction
	 * ainsi démarrée
	 * 
	 * <code>
	 * //la connexion ct ne fait pas partie de la transaction
	 * $ct = CopixDB::getConnection ();
	 * 
	 * CopixDB::begin ();
	 * $ct1 = CopixDB::getConnection ('profile_mysql');
	 * $ct2 = CopixDB::getConnection ('profile_oci');
	 * //....
	 * CopixDB::commit ();//ct1 et ct2 sont validées
	 * </code>
	 * @return string l'identifiant de la transaction démarrée 
	 */
	public static function begin (){
        //génération d'un identifiant de transaction
        $transactionId = uniqid ('transaction_');

        //on crée une entrée dédiée à la trasaction dans le tableau
        self::$_transactionId[] = $transactionId; 
        self::$_transactions[$transactionId] = array ();
        return $transactionId;
	}
	
	/**
	 * Retourne l'identifiant de la transaction en cours
	 * @todo	doit on réellement mettre cette méthode publique ?
	 * @return string
	 */
	public static function getCurrentTransactionId (){
       if (($count = (count (self::$_transactionId) - 1)) < 0){
          return 'default';
       }
       return self::$_transactionId[$count];		
	} 

	/**
	 * Valide (et termine) une transaction
	 * <code>
	 * CopixDB::begin ();
	 * $ct1 = CopixDB::getConnection ('profile_mysql');
	 * $ct2 = CopixDB::getConnection ('profile_oci');
	 * $ct1->doQuery ('update ..... ');
	 * $ct1->doQuery ('insert .....');
	 * $ct2->doQuery ('insert into log_table ......');
	 * CopixDB::commit ();//ct1 et ct2 sont validées
	 * </code>
	 * @param string $pTransactionId l'identifiant de la transaction que l'on souhaites valider
	 */
	public static function commit ($pTransactionId = null){
		if ($pTransactionId === null){
            //on calcul l'identifiant de transaction si pas donné.
			$pTransactionId = self::getCurrentTransactionId (); 
		}else{
            //on vérifie que l'identifiant de transation trouvé existe bien
		    self::_assertExistingtransaction ($pTransactionId);
		}
	
		foreach (self::$_transactions[$pTransactionId] as $profile=>$connection){
			$connection->commit ();
		}
		self::_removeTransaction ($pTransactionId);
	}
	
	/**
	 * Supression d'une transaction de la pile
	 * @param string $pTransactionId l'identifiant de la transaction à supprimer
	 */
	private static function _removeTransaction ($pTransactionId){
		//Supression de la trasaction dans la liste des identifiant de transaction en cours.
		$tmp = self::$_transactionId;//on passe pour un tableau temporaire car on veut garder
		//des identifiants de tableaux séquentiels sans blanc (0, 1, 2, 3, et non 0, 2, 3, 6)
		self::$_transactionId = array ();
		foreach ($tmp as $transactionPosition=>$transactionId){
			if ($transactionId != $pTransactionId){
				self::$_transactionId[] = $transactionId;
			}
		}
		//on supprime la transaction courante
		unset (self::$_transactions[$pTransactionId]);
	}
	
	/**
	 * On s'assure que la transaction d'identifiant donné existe bien
	 * @param	string	$pTransactionId	l'identifiant de la transaction
	 * @throws	CopixDBException
	 */
	private static function _assertExistingTransaction ($pTransactionId){
		if (!in_array ($pTransactionId, self::$_transactionId)){
			throw new CopixDBException ('Transaction '.$pTransactionId.' inconnue dans la pile des transactions');
		}
		if (!isset (self::$_transactions[$pTransactionId])){
			throw new CopixDBException ('Transaction '.$pTransactionId.' introuvable dans la liste des transactions'); 
		}
    } 

	/**
	 * Annule et termine une transaction
	 * @param string $pTransactionId l'identifiant de la transaction à rollbacker  
	 */
	public static function rollback ($pTransactionId = null){
		if ($pTransactionId === null){
            //on calcul l'identifiant de transaction si pas donné.
			$pTransactionId = self::getCurrentTransactionId (); 
		}else{
            //on vérifie que l'identifiant de transation trouvé existe bien
		    self::_assertExistingtransaction ($pTransactionId);
		}

		foreach (self::$_transactions[$pTransactionId] as $profile=>$connection){
           $connection->rollback ();
		}
		self::_removeTransaction ($pTransactionId);
	}
	
    /**
    * Création d'un objet connexion
    * @param object $pProfil description de la connexion que l'on souhaites utiliser
    * @return CopixDbConnection  objet de connection vers la base de donnée
    */
    private static function _createConnection ($pProfil){
    	Copix::RequireOnce (COPIX_PATH.'db/drivers/'.$pProfil->getDriverName ().'/CopixDbConnection.'.$pProfil->getDriverName ().'.class.php');
        $class = 'CopixDbConnection'.$pProfil->getDriverName ();
        return new $class ($pProfil);
   }
   
   /**
    * Retourne la liste des drivers disponibles sur la plateforme actuelle
    */
   public static function getAvailableDrivers (){
     	return self::_getDrivers (true);
   }
   
   public static function getAllDrivers () {
		return self::_getDrivers (false);
   }
   
   private static function _getDrivers  ($pGetOnlyAvailables) {
   		$arDrivers   = array ();
   		$arAvailable = array ();

   		if ($dir      = @opendir (COPIX_PATH.'db/drivers/')){
   			while (false !== ($file = readdir($dir))) {
   				if (is_dir (COPIX_PATH.'db/drivers/'.$file)){
   					if (file_exists ($fileName = COPIX_PATH.'db/drivers/'.$file.'/CopixDbConnection.'.$file.'.class.php')){
   						
   						if ($pGetOnlyAvailables) {
	   						Copix::RequireOnce ($fileName);
	   						$class = 'CopixDbConnection'.$file;
	   						if (class_exists ($class)){
		   						if (call_user_func (array ($class, 'isAvailable'))){
		   							$arAvailable[$file] = $file;
		   						}
	   						}
   						} else {
   							$arAvailable[$file] = $file;
   						}
   					}
   				}
   			}
   			closedir ($dir);
   		}
   		clearstatcache();
   		return $arAvailable;
   }

   /**
    * Méthode capable de tester la validité d'une connexion.
    * @param	CopixDBProfile	$pProfil	profil de connexion
    * @return	boolean true on success, string on failure
    */
   public static function testConnection ($pProfil){
		if (in_array ($pProfil->getDriverName (), self::getAvailableDrivers ())){
			try {
				$ct = self::_createConnection ($pProfil);
				return true;
			}catch (Exception $e){
				return $e->getMessage ();
			}
		}
		return 'Unavailable driver';
   }
}
?>