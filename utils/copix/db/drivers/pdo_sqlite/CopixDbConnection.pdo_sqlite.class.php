<?php
/**
* @package		copix
* @author		Croes Gérald
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Classe de connexion aux bases SQLite en utilisant PDO
 * @package		copix
 * @subpackage	db
 */
class CopixDBConnectionPDO_SQLite extends CopixDBPDOConnection {
   /**
    * Analyse la requête pour qu'elle passe sans encombre dans le driver MSSQL
    */
   protected function _parseQuery ($pQueryString, $pParameters, $pOffset, $pCount){
   	  $toReturn = parent::_parseQuery ($pQueryString, $pParameters, $pOffset, $pCount);

      //only for select query
      if ($toReturn['isSelect'] && ($pOffset !== null || $pCount !== null)){
         $pos = stripos($toReturn['query'], "select");

         if ($pCount === null){
         	$pCount = $this->_getMaxCount ();
         }
        
        if ($pCount > 0) {
            $toReturn['query'] .= " LIMIT $pCount";
            if ($pOffset > 0) {
                $toReturn['query'] .= " OFFSET $pOffset";
            }
        }

         $toReturn['offset'] = true;
         $toReturn['count']  = true;
      }
      
      if (! $toReturn ['isSelect']){
      	$toReturn['isSelect'] = (stripos (trim ($pQueryString), 'PRAGMA') === 0);       	
      }
      return $toReturn;
   }
   
   
    /**
     * Retourne la liste des noms de table existantes (visibles de l'utilisateur)
     * @return array
     */
    public function getTableList () {
        $sql = "SELECT name FROM sqlite_master WHERE type='table' UNION ALL SELECT name FROM sqlite_temp_master WHERE type='table' ORDER BY name";
        $toReturn = array ();
        foreach ($this->doQuery ($sql) as $fields){
        	$toReturn[] = $fields->name;
        }
        return $toReturn;
    }

    /**
     * Retourne les champs de la table.
     * @param	string	$pTable	le nom de la table dont on veut récupérer les champs
     * @return	array
     */
    public function getFieldList ($pTable) {
        $sql = "PRAGMA table_info($pTable)";
        $result = $this->doQuery ($sql);
        $toReturn = array ();

        foreach ($result as $key => $val) {
            $field = new StdClass ();
            $field->name = $val->name;
            $temp_type = explode('(',$val->type);
            $field->type = strtolower(trim($temp_type[0]));
            $field->notnull = $val->notnull;
            $field->defaultValue = $val->dflt_value;
            $field->primary = (bool) $val->pk;
            $field->isAutoIncrement = ($field->primary && strtolower ($field->type) == 'integer');
            $field->caption = $val->name;
            $field->required=($val->notnull != '0') ? 'yes' : 'no';
            $arType = array('int'=>'int','tinyint'=>'int','smallint'=>'int','mediumint'=>'int','bigint'=>'numeric','integer'=>'int','double'=>'float','decimal'=>'float','float'=>'float','numeric'=>'float','real'=>'float','char'=>'varchar','tinyblob'=>'varchar','blob'=>'varchar','tinytext'=>'varchar','text'=>'string','mediumblob'=>'varchar','mediumtext'=>'varchar','longblob'=>'varchar','longtext'=>'varchar','date'=>'date','datetime'=>'datetime', 'time'=>'time', 'varchar'=>'varchar');
            if (isset($arType[strtolower($field->type)])) {
            	  $field->type= $arType[$field->type];
            } else {
            	  throw new CopixDBException ("Le type $field->type n'est pas reconnu");
            }
            if ($field->type == 'int' && $field->isAutoIncrement){
            	$field->type = 'autoincrement';
            }
            if (isset($temp_type[1])){
            	 $field->maxlength = substr($temp_type[1],0,-1);
            }
            $field->sequence='';
            $field->pk=$field->primary;
            $toReturn[$field->name] = $field;
        }
        return $toReturn;
    }
    
    /**
     * Indique si le driver est disponible
     * @return bool
     */
    static public function isAvailable (){
		if (!class_exists ('PDO')){
			return false;
		}
		return in_array ('sqlite', PDO::getAvailableDrivers ());
    } 
}
?>