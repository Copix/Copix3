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
 * Interface de base pour tous les drivers que l'on peut souhaiter implémenter
 * @package copix
 * @subpackage db
 */
interface ICopixDBConnection {
	/**
	 * Lancement d'une requête SQL dont les résultats sont retournés sous la forme d'iterateurs.
	 *
	 * @param	string	$pQueryString	la requête à lancer
	 * @param	array	$pParameters	tableau de paramètres
	 * @param	int		$pOffset		l'offset à partir duquel nous allons lire les résultats => Si null, pas d'offset 
	 * @param	int		$pCount			le nombre d'élément que l'on souhaites récupérer depuis la base. Si null => le maximum
	 */
	public function iDoQuery ($pQueryString, $pParameters = array (), $pOffset = null, $pCount = null);

	/**
	 * Lancement d'une requête SQL
	 * @param	string	$pQueryString	la requête à lancer
	 * @param	array	$pParameters	tableau de paramètres
	 * @param	int		$pOffset		l'offset à partir duquel nous allons lire les résultats => Si null, pas d'offset 
	 * @param	int		$pCount			le nombre d'élément que l'on souhaites récupérer depuis la base. Si null => le maximum
	 */
	public function doQuery ($pQueryString, $pParameters = array (), $pOffset = null, $pCount = null);

	/**
	 * Dernier identifiant automatique affecté
	 * @param	string	$pFromSequence	le nom de la séquence depuis laquelle on veut récupérer le dernier identifiant.
	 * 	si null est donné, on suppose que le dernier identifiant provient d'un autoincrément
	 * @return	integer
	 */
	public function lastId ($pFromSequence = null);

	/**
     * Valide une transaction sur la connection donnée
     */
	public function commit ();

	/**
     * Annule une transcation sur la connection 
     */
	public function rollback ();

	/**
     * Demarre une transaction sur la connection donnée
     */
	public function begin ();

	/**
     * Récuération de la liste des tables connues depuis la connexion
     * @return array
     */
	public function getTableList ();

	/**
     * Retourne la liste des champs connus dans la table
     * @param	string	$pTableName le nom de la table dont on souhaite connaitre la liste des champs 
     */
	public function getFieldList ($pTableName);
	public static function isAvailable ();
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
					if (is_readable ($fileName = COPIX_PATH.'db/drivers/'.$file.'/CopixDbConnection.'.$file.'.class.php')){
							
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
		ksort ($arAvailable);
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

	/**
	 * Indique un identifiant de base en fonction d'un identifiant de driver
	 */
	public static function driverToDatabase ($pDriverName){
		static $driversBase = array (
			'mysql'=>'mysql', 'pdo_mysql'=>'mysql',
			'oci'=>'oci', 'pdo_oci'=>'oci',
			'mssql'=>'mssql', 'pdo_mssql'=>'pdo_mssql',
			'dblib'=>'mssql', 'pdo_dblib'=>'mssql',
			'pdo_sqlite'=>'sqlite',
			'pdo_pgsql'=>'pgsql'
		);
		if (!isset ($driversBase[$pDriverName])){
			throw new CopixDBException ('[CopixDB] Impossible de déterminer le type de base de données pour le driver [' . $this->_driverName.']');
		}
		return $driversBase[$pDriverName];
	}

}