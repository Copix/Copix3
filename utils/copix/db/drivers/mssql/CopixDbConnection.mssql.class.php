<?php
/**
 * @package      copix
 * @subpackage   db
 * @author       Duboeuf Damien
 * @copyright    CopixTeam
 * @link         http://copix.org
 * @license      http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de connexion à MysSQL en utilisant le driver mssql
 * @package copix
 * @subpackage db
 */
class CopixDBConnectionMsSQL extends CopixDBConnection {
	/**
	 * Identifiant de connexion à la base MySQL
	 * 
	 * @var int
	 */
	private $_ct = false;
	
	
	/**
	 * Construction de la connexion
	 * 
	 * @param CopixDBProfile $pProfil Profil de connexion à utiliser pour se connecter à la base de donées
	 */
	public function __construct ($pProfil) {
		parent::__construct ($pProfil);
		
		$parts = $this->_profil->getConnectionStringParts ();
		
		$parts['host'] = isset ($parts['host']) ? $parts['host'] : "localhost";
		
		if (!($this->_ct = mssql_connect ($parts['host'], $this->_profil->getUser (), $this->_profil->getPassword (), true))) {
			throw new CopixDBException ('connection echoué');
		}
		if (!mssql_select_db ($parts['dbname'], $this->_ct)) {
			throw new CopixDBException ('base non trouvée');
		}
	}
	
	/**
     * Analyse la requête pour qu'elle passe sans encombre dans le driver MySQL
     * @param	string	$pQueryString	la requête à lancer
     * @param 	array	$pParameters	les paramètres de la requête
     * @param 	int		$pOffset		l'offset à partir duquel on veut récupérer les résultats
     * @param 	int 	$pCount			le nombre de lignes que l'on souhaites récupérer depuis cette requête
	 */
	protected function _parseQuery ($pQueryString, &$pParameters = array (), $pOffset = null, $pCount = null) {
		$toReturn = parent::_parseQuery ($pQueryString, $pParameters, $pOffset, $pCount);
		
		//only for select query
		if ($toReturn['isSelect'] && ($pOffset !== null || $pCount !== null)){
			$toReturn['query'] = $this->_parseLimit ($toReturn['query'], $pOffset, $pCount);
			$toReturn['count'] = true;//au minimum, on poura faire un fetch all une fois les enregistrements obtenus
			$toReturn['offset'] = true;
		}
		
		if (! $toReturn ['isSelect']){
			$toReturn['isSelect'] = (stripos (trim ($pQueryString), 'exec') === 0);       	
		}
		
		return $toReturn;
	}
	
	
	/**
	 * Fonction permettant de gérer les requêtes avec les options offset et count sous mssql.
	 * Mssql ne gérant pas nativement les requêtes avec des contraintes d'offset et de count, cette méthode
	 * permet de résoudre le problème tout en gardant un temps de réponse correct quelque soit l'offset spécifié.
	 *
	 * @param string $pSQL => requête SQL a exécuter
	 *        int $pOffset => numéro de l'enregistrement à partir duquel retourner les résultats. 0 = 1er ligne de la table
	 *        int $pCount => nombre de résultat à retourner
	 * @return string => requête MSSQL gérant le count et l'offset
	 *
	 * règles d'utilisation :
	 *    - la valeur 0 de l'offset correspond à la première entrée de la table (0=1er ligne, 1=2eme ligne, n=n+1 ligne...)
	 *    - lorsque vous spécifiez un offset vous êtes obligés de déclarer une clause order dans votre requête 
	 *      sinon le traitement renverra une exception. Si vous n'avez pas besoin de clause order, spécifiez 
	 *      quand même une clause order sur la clef primaire en ascendant.
	 *    - Si vous utilisez des alias "as" dans votre select différent du nom de colonne, il est impératif
	 *      que le nom spécifié dans la clause order corresponde à l'alias.
	 *    - Si vous utilisez offset et count pour gérer la pagination, la première page que vous affichez
	 *      doit préciser un offset, même s'il est égal à 0. Sinon vous avez de forts risques pour que des
	 *      données ne soient jamais affichées et d'autres soient présentes en première et deuxième page.
	 **/
	private function _parseLimit ($pSQL, $pOffset, $pCount){
		$pos = strpos(strtolower($pSQL), "select");
		$queryString = substr($pSQL, $pos + 6);
		
		
		if($pOffset!=null && $pOffset>0) {
			 // Code spécifique pour ne ramener que les données à partir de l'offset.
			 $strPosOrder = strpos(strtolower($queryString), 'order');
			 $strPosOrderBy = strpos(strtolower($queryString), 'by', $strPosOrder);
			 if($strPosOrderBy > 0) {					 	
			 		// Construction de la clause order
					$orderBy = substr($queryString, $strPosOrderBy + 2);		
					$queryString = substr($queryString, 0, $strPosOrder);		

					// on ajoute asc aux champs du order où le tri n'est pas spécifié.
					$arrayOrder = explode(',', $orderBy);
					foreach($arrayOrder as $key=>$orderItem) {
						if(strpos(strtolower($orderItem), 'asc')===false && strpos(strtolower($orderItem), 'desc')===false) {
							$arrayOrder[$key] = $orderItem.' ASC';
						}
					}
					$orderBy = implode(',', $arrayOrder);
					
					// Construction de la clause order inversée
					$inverseOrderBy = str_replace('asc', '_asc', strtolower($orderBy));
					$inverseOrderBy = str_replace('desc', 'asc', $inverseOrderBy);
					$inverseOrderBy = str_replace('_asc', 'desc', $inverseOrderBy);
					
					// Calcul du nombre d'entrée de la base
					$pNbRows = 0;
					$strPosFrom = strpos(strtolower($queryString), 'from');
					if($strPosFrom > 0) {
						$queryCount = 'select count(*) as total '.substr($queryString, $strPosFrom);
						$countResult = $this->doQuery($queryCount);
						if($countResult!=null && count($countResult)>0) {
							$pNbRows = $countResult[0]->total;
						}
					} 
					// Détermination de l'offset en fonction du nombre d'élément et du count
					if($pOffset>$pNbRows) $pOffset = $pNbRows;
					if($pCount==null) {
						$pTotal = $pNbRows;
						$pCount = $pNbRows - $pOffset;	
					} else {
						$pTotal = $pCount+$pOffset;
					}
					if($pTotal>$pNbRows) {
							$pCount = $pCount - ($pTotal - $pNbRows);
					}
					
					// Préparation de la requete MSSQL
					$queryString = 'select * from ('.
					                             'select top '.$pCount.' * from ('.
					                             'select top '.$pTotal.' '.$queryString.' order by '.$orderBy.') '.
					                             'as t1 order by '.$inverseOrderBy.') '.
					                             'as t2 order by '.$orderBy;
					
					return $queryString;
			} else {
					throw new CopixDBException ('ERREUR REQUETE ( '.$pSQL.') : La clause "order" est manquante dans la requête MSSQL. L\'utilisation d\'un offset dans une requête nécessite obligatoirement l\'ajout d\'un "order by".');
			}
		}
		
		// Détermination du nombre de résultats à retourner.
		if ($pCount === null){
			$pTotal = $this->_getMaxCount ();
		}else{
				$pTotal = $pCount+$pOffset;
		}

		$queryString = "select top ".($pTotal).' '.$queryString;
		
		return $queryString;         
	}
	
	
	/**
	 * Quote un élément 
	 **/
	public function quote ($pString, $pCheckNull = true){
	if ($pCheckNull){
			return (is_null ($pString) ? 'NULL' : "'".str_replace("'","''", $pString)."'");
		}else{
			return "'".str_replace("'","''", $pString)."'";
		}
	}
	
	
	/**
	 * récupère la liste des champs pour une base donnée.
	 * @todo
	 * @return   array    $tab[NomDuChamp] = obj avec prop (tye, length, lengthVar, notnull)
	 **/
   public function getFieldList ($tableName){
      $results = array ();
		
		
		$sql_get_fields  = "SELECT DISTINCT syscolumns.name as Field, systypes.name as type, syscolumns.length as length, "; 
		$sql_get_fields .= "                syscolumns.isnullable as isnull, sysobjects2.name fktable, syscolumns2.name as fkfieldname ";
		$sql_get_fields .= "FROM systypes,sysobjects,syscolumns ";
		$sql_get_fields .= "	LEFT JOIN sysforeignkeys ";
		$sql_get_fields .= "		LEFT JOIN syscolumns syscolumns2 ";
		$sql_get_fields .= "			LEFT JOIN sysobjects sysobjects2 ";
		$sql_get_fields .= "			ON sysobjects2.id = syscolumns2.id ";
		$sql_get_fields .= "		ON sysforeignkeys.rkeyid = syscolumns2.id ";
		$sql_get_fields .= "		AND sysforeignkeys.rkey = syscolumns2.colId ";
		$sql_get_fields .= "	ON sysforeignkeys.fkeyid = syscolumns.id ";
		$sql_get_fields .= "	AND sysforeignkeys.fkey = syscolumns.colId ";
		$sql_get_fields .= "WHERE sysobjects.id = syscolumns.id ";
		$sql_get_fields .= "AND syscolumns.xtype=systypes.xusertype "; 
		$sql_get_fields .= "AND syscolumns.xtype = systypes.xtype ";
		$sql_get_fields .= "AND sysobjects.name='" . $tableName ."' ";		

		$lines = $this->doQuery ($sql_get_fields);
		foreach ($lines as $result_line) {
			$p_result_line = new StdClass ();
			$p_result_line->type            = $result_line->type;
			$p_result_line->primary         = 0;
			$p_result_line->isAutoIncrement = 0;

			if( preg_mach("/identity/" , $p_result_line->type ) )  {
				 $p_result_line->isAutoIncrement = 1;
			}

			$p_result_line->length	= $result_line->length;
			$p_result_line->notnull   = (!$result_line->isnull);
			
			$p_result_line->fktable   	= $result_line->fktable ;
			$p_result_line->fkfieldname	= $result_line->fkfieldname ;

			$results[$result_line->Field] = $p_result_line ;
		}
		$rs = $this->doQuery("exec sp_pkeys '".$tableName."'");
		foreach ($rs as $get_primary_key) {
			$keysArray = array_keys($results);
			foreach($keysArray as $key_var) {
				if($key_var == $get_primary_key->COLUMN_NAME){
					$results[$key_var]->primary = 1;
				}
			}
		}
		return $results;
	}
	
	/**
	 * Retourne la liste des tables
	 * 
	 * @return string[]
	 */
	public function getTableList () {
		$results = array ();
		$lines = $this->doQuery ('select name from sysobjects where type = \'U\' order by name');
		foreach ($lines as $line){
			$results[] = $line->name;
		}
		return $results;
	}
	
	
	
	/**
     * Exécution d'une requête de base de données
     * @param   string  $pQueryString   la requête à exécuter
     * @param   string  $pParameters    les paramètres à donner à la requête
     * @param   int     $pOffset        la ligne à partir de laquelle on veut récupérer les donénes
     * @param   int     $pCount         le nombre d'enregistrements que l'on souhaite récupérer à partir de l'offset
	 * @return array
	 */
	public function doQuery ($pQueryString, $pParams = array (), $pOffset = null, $pCount = null) {
		$resultsOfQueryParsing = $this->_parseQuery ($pQueryString, $pParams, $pOffset, $pCount);
		$pQueryString = $resultsOfQueryParsing['query'];

		$extras = array ('binds' => $pParams);
		_log ($pQueryString, 'query', CopixLog::INFORMATION, $extras);
		
		// Création du statement
		$stmt = $this->_prepareStatement ($pQueryString);
		
		//On trie le tableau de paramètre en fonction de la taille (workaround pour éviter les conflits de binds)
		$pParams = $this->_sortParams ($pParams);

		//Association des paramètres
		foreach ($pParams as $name => $param) {
			if (is_array ($param)) {
				$param = isset ($param['value']) ? $param['value'] : null;
			}
			if (($this->_bindByName ($stmt, $name, $param)) == false ) {
				throw new CopixDBException ("Cannot bind ['$name'] in Query ['".$stmt->query."']");
			}
		}
		
		//on exécute la requête
		$result = mssql_query ($stmt->query, $this->_ct);
		if (!$result) {
			throw new CopixDBException ("Query Error [" . utf8_encode (mssql_get_last_message ()) . "] - ['" . $stmt->query . "']");
		}

		//retourne les résultats.
		if ($resultsOfQueryParsing['isSelect']) {
			$results = array ();
			while ($o = mssql_fetch_object ($result)) {
				$results[] = $o;
			}
		} else {
			$results = mssql_rows_affected ($this->_ct);
		}
		return $results;
	}
	
	
	/**
     * Exécution d'une requête de base de données
	 * 
     * @param   string  $pQueryString   la requête à exécuter
     * @param   string  $pParameters    les paramètres à donner à la requête
     * @param   int     $pOffset        la ligne à partir de laquelle on veut récupérer les donénes
     * @param   int     $pCount         le nombre d'enregistrements que l'on souhaite récupérer à partir de l'offset
	 * @return array
	 * @TODO implémenter les iterateurs avec ce driver mysql
	 */
	public function iDoQuery ($pQueryString, $pParams = array (), $pOffset = null, $pCount = null) {
		return $this->doQuery ($pQueryString, $pParams, $pOffset, $pCount);
	}
	
	
	/**
	 * Prépare la requête pour execution
	 * 
	 * @param string $pQuery Requête
	 * @return stdClass 
	 */
	private function _prepareStatement ($pQuery) {
		$statement = new stdClass ();
		$statement->query = $pQuery;
		return $statement;
	}
	
	
	/**
	 * Remplace les paramètres de la requète par leur valeur
	 * 
	 * @param stdClass $pStmt Statement
	 * @param string $pName Nom du champ
	 * @param mixed $pValue Valeur
	 * @return boolean
	 */
	private function _bindByName ($pStmt, $pName, $pValue) {
		$oldquery = $pStmt->query;
		if ($pName[0] !== ':') {
			$pName = ':' . $pName;
		}

		if ($pValue === null) {
			$pStmt->query = str_replace ($pName, ' NULL ', $pStmt->query);
		} else {
			$pStmt->query = str_replace ($pName, '\'' . mysql_real_escape_string ($pValue) . '\'', $pStmt->query);
		}

		if ($oldquery == $pStmt->query) {
			return false;
		}
		return true;
	}
	
	
	/**
	 * Indique si le driver est disponible
	 * 
	 * @return boolean
	 */
	public static function isAvailable () {
		return function_exists ('mssql_connect');
	}

	/**
	 * Valide une transaction en cours sur la connection
	 */
	public function commit () {
		$this->doQuery ("COMMIT TRAN");
	}
	
	/**
	 * Annule une transaction sur la connection
	 */
	public function rollback () {
		$this->doQuery ("ROLLBACK TRAN");
	}
	
	/**
	 * Demarre une transaction
	 */
	public function begin () {
		$this->doQuery ("BEGIN TRAN");
	}

	/**
	 * Retourne le dernier identifiant généré (à partir d'une séquence)
	 * 
	 * @return int 
	 */
	public function lastId ($pFromSequence = null) {
		$id = false;
		$res = mssql_query('SELECT @@identity AS id', $this->_ct);
		if ($row = mssql_fetch_row($res)) {
			$id = trim($row[0]);
		}
		mssql_free_result($res);
		return $id;
	}
	
	/**
	 * Tri les paramètres 
	 * 
	 * @param array $pParams
	 * @return array
	 */
	private function _sortParams ($pParams) {
		ksort ($pParams);
		return array_reverse ($pParams);
	}
}