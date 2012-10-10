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
 * Classe de connexion à MySQL en utilisant le driver mysql
 * @package copix
 * @subpackage db
 */
class CopixDBConnectionMySQL extends CopixDBConnection {
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
		
		if (!($this->_ct = mysql_connect ($parts['host'], $this->_profil->getUser (), $this->_profil->getPassword (), true))) {
			throw new CopixDBException (mysql_error (), 0, $parts);
		}
		if (!mysql_select_db ($parts['dbname'], $this->_ct)) {
			throw new CopixDBException (mysql_error ($this->_ct), 0, $parts);
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
		if ($toReturn['isSelect'] && ($pOffset !== null || $pCount !== null)) {
			$pos = stripos ($toReturn['query'], 'select');

			if ($pCount === null) {
				$pCount = $this->_getMaxCount ();
			}
			
			$pOffset = intval ($pOffset);
			$pCount = intval ($pCount);

			$toReturn['query'] = $toReturn['query'] . ' LIMIT ' . $pOffset . ', ' . $pCount . ';';
			$toReturn['offset'] = true;
			$toReturn['count'] = true;
		}

		if (!$toReturn ['isSelect']) {
			$toReturn['isSelect'] = (stripos (trim ($pQueryString), 'SHOW') === 0) || (stripos (trim ($pQueryString), 'DESCRIBE') === 0);
		}
		
		return $toReturn;
	}
	
	/**
	 * Retourne la liste des tables
	 * 
	 * @return string[]
	 */
	public function getTableList ($pFullDetail = false) {
        $results   = $this->doQuery ('SHOW TABLES');
        if (count ($results) == 0) {
            return array();
        }
        $fieldName = array_keys (get_object_vars ($results[0]));
        $fieldName = $fieldName[0];
        $toReturn = array ();
        foreach ($results as $table){
            $toReturn[$table->$fieldName] = $pFullDetail ? _rppo (array ('name'=>$table->$fieldName)) : $table->$fieldName;
        }
        if ($pFullDetail){
    		$connectionStrings = $this->getProfile ()->getConnectionStringParts ();
    		$tables = array_keys ($toReturn);	
    		foreach ($tables as $tableName){
	        	$fullInformations = $this->doQuery ("
					SELECT c.table_schema, u.referenced_table_name, u.referenced_column_name, u.table_schema, u.table_name, u.column_name
 						FROM information_schema.table_constraints AS c
						JOIN information_schema.key_column_usage AS u
						USING ( constraint_schema, constraint_name )
					WHERE c.constraint_type = 'FOREIGN KEY'
						AND u.referenced_table_schema = :database_name
						AND u.table_name = :table_name
						ORDER BY c.table_schema, u.referenced_table_name, u.referenced_column_name, u.table_schema, u.table_name, u.column_name", array (':database_name'=>$connectionStrings['dbname'], ':table_name'=>$tableName));
	        	foreach ($fullInformations as $information){
	        		$toReturn[$tableName]['FOREIGN KEY'][] = $information;
	        	}
    		}
    		foreach ($tables as $tableName){
    			$toReturn[$tableName]['FIELDS'] = $this->getFieldList ($tableName);    			
    		}
    		foreach ($tables as $tableName){
    			//On concatène car le bind ne semble pas fonctionner pour ce genre de requêtes.
    			//on ne craint pas le sql injection du fait que les résultats sont alimentés par la base elle même.
    			$toReturn[$tableName]['INDEX'] = $this->doQuery ('SHOW INDEX FROM `'.$tableName.'`');
    		}    		
        }
        return $toReturn;		
	}

	/**
	 * Retourne des informations sur les champs de la table
	 * 
	 * @stdclass[]
	 */
	public function getFieldList ($pTableName) {
		$sql = 'DESCRIBE ' . $pTableName;
		$result = $this->doQuery ($sql);
		$toReturn = array ();

		foreach ($result as $key => $val) {
			// @todo : remplacer la StdClass par CopixDBFieldDescription
			// $dbFields = new CopixDBFieldDescription();
			$field = new StdClass ();
			$field->name = $val->Field;
			$type = $val->Type;
			$field->notnull = (bool) ($val->Null != 'YES');
			$field->defaultValue = $val->Default;
			$field->primary = (strtolower ($val->Key) == 'pri');
			$field->isAutoIncrement = strtolower ($val->Extra) == 'auto_increment';

			if (preg_match ('@^(set|enum)\((.+)\)$@i', $type, $tmp)) {
				$type = $tmp[1];
				$length = substr (preg_replace ('"([^,])\'\'"', '\\1\\\'', ',' . $tmp[2]), 1);
			} else {
				$length = $type;
				$type = chop (preg_replace ('@\\(.*\\)@i', '', $type));
				if (!empty($type)) {
					if (strpos ($length, 'unsigned') !== false) {
						$length = substr ($length, strpos ($length, '(') + 1);
						$length = str_replace (') unsigned', '', trim ($length));
					} else {
						$length = preg_replace ("@^$type\(@i", '', $length);
						$length = preg_replace ('@\)$@i', '', trim ($length));
					}
				}
				if ($length == $type) {
					$length = '';
				}
			}

			$field->type = $type;
			$field->length = $length;
			$field->caption = $field->name;
			$field->required = ($val->Null != 'YES') ? 'yes' : 'no';

			$arType = array (
				'bool'=>'int', 'int' => 'int', 'tinyint' => 'int', 'smallint' => 'int', 'mediumint' => 'int', 'bigint' => 'numeric', 'int unsigned' => 'int',
				'tinyint unsigned' => 'int', 'smallint unsigned' => 'int', 'mediumint unsigned' => 'int', 'bigint unsigned' => 'numeric',
				'double' => 'float', 'decimal' => 'float', 'decimal unsigned' => 'float', 'float' => 'float', 'float unsigned' => 'float', 'numeric' => 'float', 'real' => 'float',
				'char' => 'varchar', 'tinyblob' => 'string', 'blob' => 'string', 'tinytext' => 'varchar', 'text' => 'string', 'mediumblob' => 'string',
				'mediumtext' => 'varchar', 'longblob' => 'string', 'longtext' => 'varchar', 'date' => 'date', 'datetime' => 'datetime', 'time' => 'time',
				'varchar' => 'varchar', 'timestamp' => 'datetime'
			);
			if (isset ($arType[$field->type])) {
				$field->type = $arType[$field->type];
			} else {
				throw new CopixDBException ("Le type $field->type n'est pas reconnu");
			}

			if ($field->isAutoIncrement && $field->type == 'int') {
				$field->type = 'autoincrement';
			}
			if ($field->isAutoIncrement && $field->type == 'numeric') {
				$field->type = 'bigautoincrement';
			}

			if ($field->length != ''){
            	// Traitement des champs decimal
            	$arLength = explode (',', $field->length);
            	$field->maxlength = 0;
            	foreach ($arLength  as $length) {
            		$field->maxlength += $length;
			}
                $field->maxlength += (count ($arLength)-1);
            }
			$field->sequence = '';
			$field->pk = (strtolower($val->Key) == 'pri');
			$toReturn[$field->name] = $field;
		}
		return $toReturn;
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
		$result = mysql_query ($stmt->query, $this->_ct);
		if (!$result) {
			$extras = array ('query_str' => $stmt->query, 'query_params' => $pParams, 'query_error' => utf8_encode (mysql_error ($this->_ct)));
			throw new CopixDBException ("Query Error [" . utf8_encode (mysql_error ($this->_ct)) . "] - ['" . $stmt->query . "']", 0, $extras);
		}

		//retourne les résultats.
		if ($resultsOfQueryParsing['isSelect']) {
			$results = array ();
			while ($o = mysql_fetch_object ($result)) {
				$results[] = $o;
			}
		} else {
			$results = mysql_affected_rows ();
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
		return function_exists ('mysql_connect');
	}

	/**
	 * Valide une transaction en cours sur la connection
	 */
	public function commit () {
		mysql_query ('COMMIT', $this->_ct);
	}
	
	/**
	 * Annule une transaction sur la connection
	 */
	public function rollback () {
		mysql_query ('ROLLBACK', $this->_ct);
	}
	
	/**
	 * Demarre une transaction
	 */
	public function begin () {
		mysql_query ('BEGIN', $this->_ct);
	}

	/**
	 * Retourne le dernier identifiant généré (à partir d'une séquence)
	 * 
	 * @return int 
	 */
	public function lastId ($pFromSequence = null) {
		return mysql_insert_id ($this->_ct);
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