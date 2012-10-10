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
 * Classe de connexion à oracle en utilisant drivers MySQl
 * 
 * @package     copix
 * @subpackage  db
 */
class CopixDBConnectionMySQL extends CopixDBConnection {
    /**
     * Identifiant de connexion à la base MySQL.
     */
    private $_ct = false;
    
    /**
     * Construction de la connexion
     * @todo prendre en charge les options spécifiques au driver
     * @param   CopixDBProfile  $pProfil    le profil de connexion à utiliser pour se connecter à la base de donées.
     */
    public function __construct ($pProfil){
        parent::__construct ($pProfil);
        
        $parts = $this->_profil->getConnectionStringParts ();
        
        $parts['host'] = isset($parts['host']) ? $parts['host'] : "localhost";  
       
        if (!($this->_ct = mysql_connect ($parts['host'], $this->_profil->getUser (), $this->_profil->getPassword (), true))) {
        	throw new CopixDBException (mysql_error ());    
        }
        if(!mysql_select_db ($parts['dbname'], $this->_ct)) {
            throw new CopixDBException (mysql_error ($this->_ct));
        }
    }
    
    /**
     * Analyse la requète pour qu'elle passe sans encombre dans le driver MySQL
     */
    protected function _parseQuery ($pQueryString, $pParameters, $pOffset, $pCount){
       $toReturn = parent::_parseQuery ($pQueryString, $pParameters, $pOffset, $pCount);
        //only for select query
        if ($toReturn['isSelect'] && ($pOffset !== null || $pCount !== null)){
            $pos = stripos($toReturn['query'], "select");

            if ($pCount === null){
                $pCount = $this->_getMaxCount ();
            }
            
            $pOffset = intval ($pOffset);
            $pCount  = intval ($pCount);            

            $toReturn['query'] = $toReturn['query']." LIMIT $pOffset, $pCount";;
            $toReturn['offset'] = true;
            $toReturn['count']  = true;
        }

        if (! $toReturn ['isSelect']){
            $toReturn['isSelect'] = (stripos (trim ($pQueryString), 'SHOW') === 0) || (stripos (trim ($pQueryString), 'DESCRIBE') === 0);
        }
        
        return $toReturn;
    }
    
    /**
     * La liste des tables
     */
    public function getTableList () {
        $results   = $this->doQuery ('SHOW TABLES');
        if (count ($results) == 0) {
            return array();
        }
        $fieldName = array_keys (get_object_vars ($results[0]));
        $fieldName = $fieldName[0];
        $toReturn = array ();
        foreach ($results as $table){
            $toReturn[] = $table->$fieldName;
        }
        return $toReturn;
    }
    
    /**
     * Description d'une table
     */
    public function getFieldList ($pTableName) {
        $sql = "DESCRIBE $pTableName";
        $result = $this->doQuery ($sql);
        $toReturn = array();

        foreach ($result as $key=>$val) {
            // @todo : remplacer la StdClass par CopixDBFieldDescription
            // $dbFields = new CopixDBFieldDescription();
            $field = new StdClass ();
            $field->name = $val->Field;
            $type = $val->Type;
            $field->notnull = (bool) ($val->Null != 'YES');
            $field->defaultValue = $val->Default;
            $field->primary = (strtolower ($val->Key) == 'pri');
            $field->isAutoIncrement = strtolower ($val->Extra) == 'auto_increment';

            if (eregi('^(set|enum)\((.+)\)$', $type, $tmp)){
                $type   = $tmp[1];
                $length = substr(ereg_replace('([^,])\'\'', '\\1\\\'', ',' . $tmp[2]), 1);
            } else {
                $length = $type;
                $type   = chop(eregi_replace('\\(.*\\)', '', $type));
                if (!empty($type)) {
                    if (strpos($length, 'unsigned') !== false) {
                        $length = substr($length, strpos($length, '(') + 1);
                        $length = str_replace(') unsigned', '', trim ($length));
                    } else {
                        $length = eregi_replace("^$type\(", '', $length);
                        $length = eregi_replace('\)$', '', trim ($length));
                    }                    
                }
                if ($length == $type) {
                    $length = '';
                }
            }

            $field->type     = $type;
            $field->length   = $length;
            $field->caption  = $field->name;
            $field->required = ($val->Null != 'YES') ? 'yes' : 'no';

            $arType = array ('int'=>'int', 'tinyint'=>'int', 'smallint'=>'int', 'mediumint'=>'int', 'bigint'=>'numeric', 'int unsigned'=>'int', 'smallint unsigned'=>'int','mediumint unsigned'=>'int',
            'double'=>'float', 'decimal'=>'float', 'float'=>'float', 'numeric'=>'float', 'real'=>'float', 'char'=>'varchar', 'tinyblob'=>'varchar',
            'blob'=>'varchar', 'tinytext'=>'varchar', 'text'=>'string', 'mediumblob'=>'varchar', 'mediumtext'=>'varchar', 'longblob'=>'varchar', 'longtext'=>'varchar',
            'date'=>'date', 'datetime'=>'datetime', 'time'=>'time',
            'varchar'=>'varchar');
            if (isset ($arType[$field->type])) {
                $field->type = $arType[$field->type];
            } else {
                throw new CopixDBException ("Le type $field->type n'est pas reconnu");
            }

            if ($field->isAutoIncrement && $field->type == 'int') {
                $field->type = 'autoincrement';
            }
            if ($field->isAutoIncrement && $field->type == 'numeric'){
                $field->type = 'bigautoincrement';
            }

            if ($field->length != ''){
                $field->maxlength = $field->length;
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
     * @return  array
     */
    public function doQuery ($pQueryString, $pParams = array (), $pOffset = null, $pCount = null){
        $resultsOfQueryParsing = $this->_parseQuery ($pQueryString, $pParams, $pOffset, $pCount);
        $pQueryString = $resultsOfQueryParsing['query'];
        
        _log ($pQueryString.var_export ($pParams, true), 'query', CopixLog::INFORMATION);
        
        // Création du statement
        $stmt = $this->_prepareStatement ($pQueryString);
        
        //On trie le tableau de paramètre en fonction de la taille (workaround pour éviter les conflits de binds)
        $pParams = $this->_sortParams ($pParams);

        //Association des paramètres
        foreach ($pParams as $name=>$param){
            if (is_array ($param)){
                $param = isset ($param['value']) ? $param['value'] : null;
            }
            if (($this->_bindByName ($stmt, $name, $param)) == false ){
	            throw new CopixDBException ("Cannot bind ['$name'] in Query ['".$stmt->query."']");
            }
        }
        
        //on exécute la requête
        $result = mysql_query ($stmt->query, $this->_ct);
        if (!$result) {
            throw new CopixDBException ("Query Error [".mysql_error ($this->_ct)."] - ['".$stmt->query."']");
        }

        //retourne les résultats.
        if ($resultsOfQueryParsing['isSelect']){
            $results = array ();
            while ($o = mysql_fetch_object ($result)){
                $results[] = $o;
            }
        }else{
            $results = mysql_affected_rows ();
        }
        return $results;
    }
    
    /**
     * Exécution d'une requête de base de données
     * @param   string  $pQueryString   la requête à exécuter
     * @param   string  $pParameters    les paramètres à donner à la requête
     * @param   int     $pOffset        la ligne à partir de laquelle on veut récupérer les donénes
     * @param   int     $pCount         le nombre d'enregistrements que l'on souhaite récupérer à partir de l'offset
     * @return  array
     * @TODO implémenter les iterateurs avec ce driver mysql
     */
    public function iDoQuery ($pQueryString, $pParams = array (), $pOffset = null, $pCount = null){
        return $this->doQuery ($pQueryString, $pParams, $pOffset, $pCount);
    }    
    
    /**
     * Prépare la requête pour execution
     * 
     * @param string La requête
     * 
     * @return stdClass 
     */
    private function _prepareStatement ($query) {
        $statement   = new stdClass ();
        $statement->query = $query;
        return $statement;
    }
    
    /**
     * Remplace les parametres de la requète par le nom
     */
    private function _bindByName ($stmt, $name, $value) {
        $oldquery = $stmt->query;
        if($name[0] !== ':') {
            $name = ':' . $name;
        }

        if ($value === null){
			$stmt->query = str_replace($name, ' NULL ', $stmt->query);        	
        }else{
        	$stmt->query = str_replace($name, '\''. mysql_real_escape_string($value) . '\'', $stmt->query);
        }

        if($oldquery == $stmt->query) {
            return false;
        }
        return true;
    }
    
    /**
     * Indique si le driver est disponible
     * @return bool
     */
    public static function isAvailable (){
        if (!function_exists ('mysql_connect')){
            return false;
        }
        return true;
    }

    /**
     * Valide une transaction en cours sur la connection
     */
    public function commit (){
        mysql_query ("COMMIT", $this->_ct);
    }
    
    /**
     * Annule une transaction sur la connection 
     */
    public function rollback (){
        mysql_query ("ROLLBACK", $this->_ct);
    }
    
    /**
     * Demarre une transaction sur la connection donnée
     */
    public function begin (){
        mysql_query ("BEGIN", $this->_ct);
    }

    /**
     * retourne le dernier identifiant généré (à partir d'une séquence)
     * @return int 
     */
    public function lastId ($pFromSequence = null){
        return mysql_insert_id ($this->_ct);
    }
    
    /**
	* Tri les paramètres 
	*/
    private function _sortParams ($pParams){
    	ksort ($pParams);
    	return array_reverse ($pParams);
    }
}
?>