<?php
/**
 * @package		copix
 * @subpackage 	db
 * @author		Croës Gérald
 * @copyright	2001-2006 CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de connexion à une base MySQL en utilisant les drivers PDO
 * @package		copix
 * @subpackage	db
 */
class CopixDBConnectionPDO_MySQL extends CopixDBPDOConnection {
    /**
     * Constructeur
     * @param	CopixDBProfile	$pProfil	le profil de connexion à utiliser pour se connecter à la base de donées.
     */
    public function __construct ($pProfil) {
        parent::__construct ($pProfil);
		$parts['charset'] = $this->_convertCharset (isset ($parts['charset']) ? $parts['charset'] : CopixI18N::getCharset ());
		$this->doQuery ('SET CHARACTER SET '.$parts['charset']);
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
   	 * Retourne la liste des tables (en minuscule) connues de la base (en fonction de l'utilisateur)
   	 * @return   array	liste des noms de table
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
     * récupère la liste des champs pour une table nommée
     * @param		string	$pTableName	le nom de la table dont on veut récupérer les champs
     * @return	array	$tab[NomDuChamp] = obj avec prop (tye, length, lengthVar, notnull)
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

            if (preg_match('/^(set|enum)\((.+)\)$/i', $type, $tmp)){
                $type   = $tmp[1];
                $length = substr(preg_replace('/([^,])\'\'/', '\\1\\\'', ',' . $tmp[2]), 1);
            } else {
                $length = $type;
                $type   = chop(preg_replace('/\\(.*\\)/i', '', $type));
                if (!empty($type)) {
                    if (strpos($length, 'unsigned') !== false) {
                    	$length = substr($length, strpos($length, '(') + 1);
                    	$length = str_replace(') unsigned', '', trim ($length));
                    } else {
                    	$length = preg_replace("/^$type\(/i", '', $length);
                    	$length = preg_replace('/\)$/i', '', trim ($length));
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
            $arType = array ('bool'=>'int', 'int'=>'int', 'tinyint'=>'int',  'smallint'=>'int', 'mediumint'=>'int', 'bigint'=>'numeric', 'int unsigned'=>'int', 'tinyint unsigned'=>'int','smallint unsigned'=>'int','mediumint unsigned'=>'int', 'bigint unsigned'=>'numeric',
            'double'=>'float', 'decimal'=>'float', 'float'=>'float', 'numeric'=>'float', 'real'=>'float', 'char'=>'varchar', 'tinyblob'=>'varchar',
            'blob'=>'varchar', 'tinytext'=>'varchar', 'text'=>'string', 'mediumblob'=>'varchar', 'mediumtext'=>'varchar', 'longblob'=>'varchar', 'longtext'=>'varchar',
            'date'=>'date', 'datetime'=>'datetime', 'time'=>'time',
            'varchar'=>'varchar', 'timestamp'=>'datetime');
            if (isset ($arType[$field->type])) {
                $field->type = $arType[$field->type];
            } else {
                throw new CopixException ('Unknow field : ' . $field);
            }

            if ($field->isAutoIncrement && $field->type == 'int') {
                $field->type = 'autoincrement';
            }
            
            if ($field->isAutoIncrement && $field->type == 'numeric'){
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
     * Valide une transaction en cours sur la connection
     */
    public function commit () {
        $this->doQuery ("COMMIT ");
    }

    /**
     * Annule une transcation sur la connection
     */
    public function rollback () {
        $this->doQuery ("ROLLBACK ");
    }

    /**
     * Indique si le driver est disponible
     * @return bool
     */
   	public static function isAvailable () {
   	    if (!class_exists ('PDO')){
   	        return false;
   	    }
   	    return in_array ('mysql', PDO::getAvailableDrivers ());
   	}
   	
	/**
	 * Converti un charset "standard" en charset supporté par MySql
	 * @param string $pCharset le nom du charset à utiliser
	 * @return string 
	 */
	private function _convertCharset ($pCharset){
		switch (strtolower ($pCharset)){
			case 'utf-8':
				return 'utf8';
		}
		return addslashes ($pCharset);
	}
}