<?php
/**
* @package		copix
* @subpackage	dao
* @author		Croës Gérald , Jouanneau Laurent
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Classe de base pour la génération de DAO
* @package copix
* @subpackage dao
*/
class CopixDAOGenerator {
	/**
	 * La définition à utiliser pour la génération
	 */
	protected $_definition = null;

	/**
	* constructor
	* @param string $DAOid identifiant de la DAO à générer
	*/
	public function __construct($pDefinition) {
		$this->_definition = $pDefinition;
	}

	/**
	 * Retourne le code PHP pour la DAO et classe Record
	 * @return string
	 */
	function getPHPCode() {
		return '<?php ' . $this->getPHPCode4DAORecord() . $this->getPHPCode4DAO() . "\n?>";
	}

	/**
	 * préparation d'une liste de champ dans un tableau $array[':nomPropriete'] = $prefixField->nomPropriete;
	 */
	private function _prepareValuesForNewDB($fieldList, $prefixfield = '') {
		$config = CopixConfig::instance ();
		$driverName = $config->copixdb_getProfile ($this->_definition->getConnectionName());
		$driverName = $driverName->getDatabase ();

		$values = $fields = array ();
		foreach ((array) $fieldList as $fieldName => $field) {
			if ($field->type == 'version') {
				$values[':' . $fieldName] = '(intval($' . $prefixfield . $fieldName . ') + 1)';
				$formatted[':' . $fieldName] = ':' . $fieldName;
			} elseif ($field->type == 'datetime' && $driverName == 'oci') {
				$values[':'.$fieldName] = '$' . $prefixfield . $fieldName;
				$formatted[':'.$fieldName] = "to_date (:".$fieldName.", \\'YYYYMMDDHH24MISS\\')";
			} elseif (($driverName == 'mysql' || $driverName == 'sqlite') && in_array ($field->type, array ('date', 'datetime', 'time'))){
				//MySQL et SQLite gèrent les entrées sous le même format
				switch ($field->type){
					case 'datetime';
						$values[':'.$fieldName] = 'CopixDateTime::yyyymmddhhiissToFormat ($' . $prefixfield . $fieldName.", 'Y-m-d H:i:s')";
						$formatted[':'.$fieldName] = ":".$fieldName;
						break;
					case 'date':
						$values[':'.$fieldName] = 'CopixDateTime::yyyymmddToFormat ($' . $prefixfield . $fieldName.", 'Y-m-d')";
						$formatted[':'.$fieldName] = ":".$fieldName;
						break;
					case 'time':
						$values[':'.$fieldName] = 'CopixDateTime::hhiissToFormat ($' . $prefixfield . $fieldName.", 'H:i:s')";
						$formatted[':'.$fieldName] = ":".$fieldName;
						break;
				}
			} else {
				$values[':' . $fieldName] = '$' . $prefixfield . $fieldName;
				$formatted[':' . $fieldName] = ':' . $fieldName;
			}
			$fields[$fieldName] = $field->fieldName;
		}
		return array (
			$fields,
			$values, 
			$formatted
		);
	}
	
	/**
	 * Formatte les champs avec un début (start) une fin (end) et un sparateur (beetween)
 	*   eg info == name
	*   echo $field->name
	* @param string   $pFieldProperty    la propriété de l'objet que l'on utilise pour l'écriture $using
	* @param string   $pPrefix    ajouter avant
	* @param string   $pPostfix      ajouter aprs
	* @param string   $pSeparator sparateur
	* @param array    $pFields   les champs  crire, si null alors on utilise tous les champs     
	*/
	function _writeFieldsInfoWith ($pFieldProperty, $pPrefix = '', $pPostfix = '', $pSeparator = '', $pFields = null) {
	    if ($pFields === null) {
	        //Si aucun champ n'est donn, on utilise les champs de la dfinition
			$pFields = $this->_definition->getProperties();
		}

		$result = array ();
		foreach ($pFields as $id => $field) {
			$result[] = $pPrefix . $field-> $pFieldProperty . $pPostfix;
		}

		return implode ($pSeparator, $result);
	}

	/**
	* Compilation de l'objet record
	* @return string
	*/
	function getPHPCode4DAORecord() {
		$result = '';

		//--Vars
		$classVars = array ();
		$classMethods = array ();

		if ($this->_definition->getUserDefinedDAORecordName() !== null) {
			$result .= ' Copix::RequireOnce (\'' . $this->_definition->getPHPClassFilePath() . '\');' . "\n";
			$extends = " extends " . $this->_definition->getUserDefinedDAORecordName();
			$classVars = (array) get_class_vars($this->_definition->getUserDefinedDAORecordName());
			$classMethods = (array) get_class_methods($this->_definition->getUserDefinedDAORecordName());
		} else {
			$extends = '';
		}
		$result .= "\nclass " . CopixDAOFactory::getDAORecordName ($this->_definition->getDAOId()) . $extends . ' implements ICopixDAORecord' . "{\n";

		//DAORecord fields (not in user's DAO)
		//building the tab for the required properties.
		$usingFields = array ();
		$classVarsList = array_keys($classVars);
		foreach ($this->_definition->getProperties() as $id => $field) {
			if (!in_array($field->name, $classVarsList)) {
				$usingFields[$id] = $field;
			}
		}

		//declaration of properties.
		$result .= $this->_writeFieldsInfoWith ('name', ' var $', " = null;\n", '', $usingFields);

		//InitFromDBObject
		$methodName = in_array ('initFromDBObject', $classMethods) ? '_compiled_initFromDBObject' : 'initFromDBObject';
		$result .= ' public function ' . $methodName . ' ($dbRecord){' . "\n";
		foreach ($this->_definition->getProperties() as $field) {
			$result .= ' if (is_array ($dbRecord)){' . "\n";
			$result .= ' $this->' . $field->name . '= $dbRecord[\'' . $field->name . "'];\n";
			$result .= '}else{' . "\n";
			$result .= ' $this->' . $field->name . '= $dbRecord->' . $field->name . ";\n";
			$result .= '}';
		}
		$result .= ' return $this;';
		$result .= ' }'."\n";;
		$result .= $this->_generatePHP4GetDAOId ();
		$result .= "}\n";
		return $result;
	}
	
	/**
	 * Compresse le nom de variable passé en paramètres à 30 caractères. 
	 * S'assure grâce à une table interne de l'unicité des noms. 
	 */
	private function _getVariableName ($pFieldName){
		static $fields = array ();
		if (isset ($fields[$pFieldName])){
			return $fields[$pFieldName];
		}
		
		$result = null;
		$try = 0;
		//tant que nous ne sommes pas arrivé à générer une variable qui n'existe pas déja 
		while ($result !== null && isset ($fields[$result])){
			$result = $this->_compressVariableName ($pFieldName, $try++);
		}
		return $fields[$pFieldName] = $result; 
	}
	
	/**
	 * Numéro d'essais de génération
	 *
	 * @param unknown_type $pName
	 * @param unknown_type $tryNum
	 */
	private function _packVariableName ($pName, $pTryNum = 0, $pNumChars = 30){
		$final = $pName . ($pTryNum === 0 ? '' : $pTryNum); 
		if (strlen ($final) <= $pNumChars){
			return $final;
		}
		return substr ($final, 0, -1*strlen ($final)-$pNumChars);
	}
	/**
	* Génération du code PHP pour le DAO
	* @return string
	*/
	function getPHPCode4DAO() {
		$result = '';
		if ($this->_definition->getUserDefinedDAOName() !== null) {
			//includes immediatly in case we put this in session (to be able to deserialize).
			$result .= '  Copix::RequireOnce  (\'' . $this->_definition->getPHPClassFilePath() . '\');' . "\n";
			$extends = ' extends ' . $this->_definition->getUserDefinedDAOName();
		} else {
			$extends = '';
		}
		$result .= "\nclass " . CopixDAOFactory :: getDAOName($this->_definition->getDaoId()) . $extends .  ' implements ICopixDAO '. " { \n";
		$result .= '   var $_table=\'' . $this->_definition->getPrimaryTableRealName() . '\';' . "\n";
		if (($connectionName = $this->_definition->getConnectionName()) === null) {
			$result .= '   var $_connectionName=null;' . "\n";
		} else {
			$result .= '   var $_connectionName=\'' . $connectionName . '\';' . "\n";
		}
		$result .= '   var $_selectQuery;' . "\n";
		$result .= $this->_generatePHP4DAOConstructor();
		$result .= $this->_generatePHP4Check();
		$result .= $this->_generatePHP4Get();
		$result .= $this->_generatePHP4FindAll(false);
		$result .= $this->_generatePHP4FindBy(false);
		$result .= $this->_generatePHP4FindAll(true);
		$result .= $this->_generatePHP4FindBy(true);
		
		$result .= $this->_generatePHP4Insert();
		$result .= $this->_generatePHP4Update();
		$result .= $this->_generatePHP4Delete();
		$result .= $this->_generatePHP4DeleteBy();
        $result .= $this->_generatePHP4CountBy();
		$result .= $this->_generatePHP4DefinedMethods();
		$result .= $this->_generatePHP4DAODescribeField();
		$result .= $this->_generatePHP4GetDAOId ();
		
		$result .= 'private function _dirtyClearNullValuesForSelectQueries ($array){
$toReturn = array ();
foreach ($array as $key=>$value){
if ($value !== "___COPIX___DELETE___ME___FROM____DAO___QUERIES___"){
   $toReturn[$key] = $value;
}
 }
return $toReturn;
}';
		$result .= "}\n"; //Fin de la classe
		return $result;
	}

	/**
	* Génération de la clause from pour les requêtes de sélection (jointures entre les tables)
	* @return array array[0] = From (avec le mot clef from inclus) et array[1] = Where (avec le mot clef where inclus, vide si aucune condition)
	*/
	function getFromClause () {
		$config = CopixConfig :: instance ();
		$driverName = $config->copixdb_getProfile ($this->_definition->getConnectionName());
		$driverName = $driverName->getDatabase ();

		$ptable = $this->_definition->getPrimaryTable ();
		
		if ($ptable['name'] != $ptable['tablename']) {
			$sqlFrom = $ptable['tablename'] .' '.$ptable['name'];
		} else {
			$sqlFrom = $ptable['tablename'];
		}

		$sqlWhere = '';
		foreach ($this->_definition->getJoins () as $tablename => $arJoin) {
			$fromPassed = false;
			foreach ($arJoin as $join) {
				if ($tablename != $ptable['name']) {
					$table = $this->_definition->getTable ($tablename);
					if ($table['name'] != $table['tablename']) {
						$sqltable = $table['tablename'] .' '.  $table['name'];
					} else {
						$sqltable = $table['tablename'];
					}

					//car particulier des bases oracle
					if ($driverName == 'oci') {
						if ($join['join'] == 'left') {
							$fieldjoin = $ptable['name'] . '.' . $join['pfield'] . '=' . $table['name'] . '.' . $join['ffield'] . '(+)';
						}
						elseif ($join['join'] == 'right') {
							$fieldjoin = $ptable['name'] . '.' . $join['pfield'] . '(+)=' . $table['name'] . '.' . $join['ffield'];
						} else {
							$fieldjoin = $ptable['name'] . '.' . $join['pfield'] . '=' . $table['name'] . '.' . $join['ffield'];
						}
						if (!$fromPassed) {
							$sqlFrom .= ', ' . $sqltable;
							$fromPassed = true;
						}
						$sqlWhere .= ' AND ' . $fieldjoin;
					} elseif ($driverName == 'pgsql') {
						$fieldjoin = $ptable['name'] . '.' . $join['pfield'] . '=' . $table['name'] . '.' . $join['ffield'];
						if ($join['join'] == 'left') {
							$sqlFrom .= ' LEFT JOIN ' . $sqltable . ' ON (' . $fieldjoin . ')';
						}
						elseif ($join['join'] == 'right') {
							$sqlFrom .= ' RIGHT JOIN ' . $sqltable . ' ON (' . $fieldjoin . ')';
						} else {
							if (!$fromPassed) {
								$sqlFrom .= ' JOIN ' . $sqltable . ' ON (' . $fieldjoin . ')';
								$fromPassed = true;
							} else {
							    $sqlWhere .= ' AND ' . $fieldjoin;
							}
						}
					} else {
						$fieldjoin = $ptable['name'] . '.' . $join['pfield'] . '=' . $table['name'] . '.' . $join['ffield'];
						if ($join['join'] == 'left') {
							$sqlFrom .= ' LEFT JOIN ' . $sqltable . ' ON (' . $fieldjoin . ')';
						}
						elseif ($join['join'] == 'right') {
							$sqlFrom .= ' RIGHT JOIN ' . $sqltable . ' ON (' . $fieldjoin . ')';
						} else {
							if (!$fromPassed) {
								$sqlFrom .= ' JOIN ' . $sqltable;
								$fromPassed = true;
							}
						    $sqlWhere .= ' AND ' . $fieldjoin;
						}
					}
				}
			}
		}
		$sqlWhere = ($sqlWhere != '') ? ' WHERE ' . substr ($sqlWhere, 4) : '';
		return array (
			' FROM ' . $sqlFrom,
			$sqlWhere
		);
	}

	/**
	* build SELECT clause for all SELECT queries
	*/
	function getSelectClause () {
		$result = array ();

		$config = CopixConfig :: instance ();
		$driverName = $config->copixdb_getProfile ($this->_definition->getConnectionName ());
		$driverName = $driverName->getDatabase ();

		foreach ($this->_definition->getProperties () as $id => $prop) {
			$table = $prop->table . '.';

			if ($prop->selectMotif == '%s') {
				if ($prop->fieldName != $prop->name) {
					//in oracle we must escape name
					if ($driverName == 'oci') {
						if ($prop->type == 'datetime'){
							$result[] = "to_char(".$table.$prop->fieldName.", \\'YYYYMMDDHH24MISS\\')" . ' "' . $prop->name . '"';
						}else{
							$result[] = $table . $prop->fieldName . ' "' . $prop->name . '"';
						}
					} elseif ($driverName == 'mssql') {
						if ($prop->type == 'varchardate') {
							$result[] = 'convert(varchar, ' . $table . $prop->fieldName . ', 121) as ' . $prop->name;
						} elseif ($prop->type == 'numeric' || $prop->type == 'bigautoincrement' || $prop->type == 'autoincrement') {
								$result[] = 'convert(varchar, ' . $table . $prop->fieldName . ') as ' . $prop->name;
						} else {
							$result[] = $table . $prop->fieldName . ' ' . $prop->name;
						}
					} elseif (($driverName == 'mysql') && in_array ($prop->type, array ('date', 'datetime', 'time'))) {
						if ($prop->type == 'date'){
							$result[] = "DATE_FORMAT(".$table . $prop->fieldName.", \\'%Y%m%d\\') " . $prop->name;	
						}elseif ($prop->type == 'time'){
							$result[] = "DATE_FORMAT(".$table . $prop->fieldName.", \\'%H%i%s\\') " . $prop->name;
						}elseif ($prop->type == 'datetime'){
							$result[] = "DATE_FORMAT(".$table . $prop->fieldName.", \\'%Y%m%d%H%i%s\\') " . $prop->name;							
						}
					} elseif (($driverName == 'sqlite') && in_array ($prop->type, array ('date', 'datetime', 'time'))){
						if ($prop->type == 'date'){
							$result[] = "strftime(\\'%Y%m%d\\', ".$table . $prop->fieldName.") " . $prop->name;	
						}elseif ($prop->type == 'time'){
							$result[] = "strftime(\\'%H%M%S\\', ".$table . $prop->fieldName.") " . $prop->name;
						}elseif ($prop->type == 'datetime'){
							$result[] = "strftime(\\'%Y%m%d%H%M%S\\', ".$table . $prop->fieldName.") " . $prop->name;							
						}
					} else if ($driverName == 'pgsql') {
						$result[] = $table . $prop->fieldName . ' AS ' . $prop->name;
					} else {
						$result[] = $table . $prop->fieldName . ' ' . $prop->name;
					}
				} else {
					if ($driverName == 'mssql' && ($prop->type == 'numeric' || $prop->type == 'bigautoincrement' || $prop->type == 'autoincrement')) {
						$result[] = 'convert(varchar, ' . $table . $prop->fieldName . ') as ' . $prop->fieldName;
					} elseif ($driverName == 'sqlite') {
						$result[] = $table . $prop->fieldName . ' ' . $prop->name;
					} elseif ($driverName == 'oci' && $prop->type == 'datetime') {
						$result[] = "to_char(".$table.$prop->fieldName.", \\'YYYYMMDDHH24MISS\\')" . ' "' . $prop->fieldName . '"';
					} elseif (($driverName == 'mysql') && in_array ($prop->type, array ('date', 'datetime', 'time'))) {
						if ($prop->type == 'date'){
							$result[] = "DATE_FORMAT(".$table . $prop->fieldName.", \\'%Y%m%d\\') ".$prop->fieldName;	
						}elseif ($prop->type == 'time'){
							$result[] = "DATE_FORMAT(".$table . $prop->fieldName.", \\'%H%i%s\\') ".$prop->fieldName;
						}elseif ($prop->type == 'datetime'){
							$result[] = "DATE_FORMAT(".$table . $prop->fieldName.", \\'%Y%m%d%H%i%s\\') ".$prop->fieldName;							
						}
					} elseif (($driverName == 'sqlite') && in_array ($prop->type, array ('date', 'datetime', 'time'))){
						if ($prop->type == 'date'){
							$result[] = "strftime(\\'%Y%m%d\\', ".$table . $prop->fieldName.") " . $prop->fieldName;	
						}elseif ($prop->type == 'time'){
							$result[] = "strftime(\\'%H%M%S\\', ".$table . $prop->fieldName.") " . $prop->fieldName;
						}elseif ($prop->type == 'datetime'){
							$result[] = "strftime(\\'%Y%m%d%H%M%S\\', ".$table . $prop->fieldName.") " . $prop->fieldName;							
						}
					} else {
						$result[] = $table . $prop->fieldName;
					}
				}
			} else {
				$result[] = sprintf($prop->selectMotif, $table . $prop->fieldName) . ' ' . $prop->name;
			}

		}
		return 'SELECT ' . (implode(', ', $result));
	}

	/**
	 * Génération des conditions supplémentaires dans l'optique ou l'on va utiliser PDO
	 * @return array
	 */
	function _buildConditionsForNewDB(& $fields, $prefix = '', $forSelect = false) {
		$array = array ();
		$sqlCondition = array ();
		foreach ($fields as $field) {
			$fieldValue = '$' . $prefix . $field->name;
			$array[$fieldVar = trim (':' . $field->table.'_'.$field->name)] = '($' . $prefix . $field->name.' === null ? "___COPIX___DELETE___ME___FROM____DAO___QUERIES___" : $' . $prefix . $field->name.')';
            $sqlCondition[] = ($forSelect ? $field->table.'.' : '').$field->fieldName . " '.($fieldValue === null ? 'IS' : '=').'  '.($fieldValue === null ? 'NULL' : '$fieldVar').' ";
		}
		return array ($array, implode (' AND ', $sqlCondition));
	}

	/**
	* Get autoincrement PK field
	*/
	function _getAutoIncrementField($using = null) {
		$result = array ();
		if ($using === null) {
			//if no fields are provided, using _userDefinition's as default.
			$using = $this->_definition->getProperties();
		}

		$config = CopixConfig :: instance();
		$driverName = $config->copixdb_getProfile($this->_definition->getConnectionName());
		$driverName = $driverName->getDatabase();

		foreach ($using as $id => $field) {
			if ($field->type == 'autoincrement' || $field->type == 'bigautoincrement') {
				if ($driverName == "pgsql" && ! strlen($field->sequenceName)) {
					$field->sequenceName = $this->_definition->getPrimaryTableRealName() . "_" . $field->name . "_seq";
				}
				return $field;
			}
		}
		return null;
	}

	/**
	 * Création d'une chaine de caractre  partir d'un tableau afin de l'insrer dans l'appel  une requte.
	 * 
	 * exemple 
	 * 
	 *  tableau source : $array[':champ'] = '$field->champ', $array[':champ2'] = '$field->champ2'  
	 * gnration obtenue :
	 *  array (':champ'=>$field->champ, ':champ2'=>$field->champ2)
	 * 
	 * @param $arraySource array Le tableau des lments que l'on souhaite transformer 
	 * @return string
	 *   
	 */
	private function _makeArrayParamsForQuery($pArraySource) {
		$finalString = '$this->_dirtyClearNullValuesForSelectQueries (array (';
		$first = true;
		foreach ($pArraySource as $paramName => $paramStringValue) {
			if (!$first) {
				$finalString .= ', ';
			}
			$finalString .= "'" . $paramName . "'=>" . $paramStringValue;
			$first = false;
		}
		return $finalString . '))';
	}

	/**
	 * Gnration du code PHP pour la fonction de vrification 
	 * @return string
	 */
	private function _generatePHP4Check() {
		$methodName = in_array('check', $this->_definition->getUserDAOClassMethods()) ? '_compiled_check' : 'check';
		$result = ' public function ' . $methodName . ' ($pRecord){' . "\n";
		$result .= '  $errorObject = new CopixErrorObject ();' . "\n";
		foreach ($this->_definition->getProperties() as $id => $field) {
			//if required, add the test.
			if ($field->required && ($field->type != 'autoincrement' && $field->type != 'bigautoincrement')) {
				$result .= '  if ($pRecord->' . $field->name . ' === null){' . "\n";
				$result .= '    $errorObject->addError (\'' . $field->name . '\', _i18n (\'copix:dao.errors.required\',';
				if ($field->captionI18N !== null) {
					$result .= '_i18n (\'' . $field->captionI18N . '\')';
				} else {
					$result .= '\'' . str_replace("'", "\'", $field->caption) . '\'';
				}
				$result .= "));\n  }\n";
			}
			//if a regexp is given, check it....
			if ($field->regExp !== null) {
				$result .= '  if (strlen ($pRecord->' . $field->name . ') > 0){' . "\n";
				$result .= '   if (preg_match (\'' . $field->regExp . '\', $pRecord->' . $field->name . ') === 0){' . "\n";
				$result .= '      $errorObject->addError (\'' . $field->name . '\', _i18n (\'copix:dao.errors.format\',';
				if ($field->captionI18N !== null) {
					$result .= '_i18n (\'' . $field->captionI18N . '\')';
				} else {
					$result .= '\'' . str_replace("'", "\'", $field->caption) . '\'';
				}
				$result .= "));\n  }\n";
				$result .= "  }\n";
			}

			//if a maxlength is given
			if ($field->maxlength !== null && (!in_array($field->type, array (
					'date',
					'varchardate',
					'time',
					'varchartime',
					'datetime'
				)))) {
				$result .= '  if (strlen ($pRecord->' . $field->name . ') > ' . intval ($field->maxlength) . '){' . "\n";
				$result .= '      $errorObject->addError (\'' . $field->name . '\', _i18n (\'copix:dao.errors.sizeLimit\',array(';
				if ($field->captionI18N !== null) {
					$result .= '_i18n (\'' . $field->captionI18N . '\')';
				} else {
					$result .= '\'' . str_replace ("'", "\'", $field->caption) . '\'';
				}
				$result .= ', ' . intval ($field->maxlength);
				$result .= ")));\n";
				$result .= '  }' . "\n";
			}

			//if int or numeric, will check if it is really a numeric.
			if (in_array($field->type, array (
					'numeric',
					'int',
					'integer'
				))) {
				$result .= '  if (strlen ($pRecord->' . $field->name . ') > 0){' . "\n";
				$result .= '   if (! is_numeric ($pRecord->' . $field->name . ')){' . "\n";
				$result .= '      $errorObject->addError (\'' . $field->name . '\', _i18n (\'copix:dao.errors.numeric\',';
				if ($field->captionI18N !== null) {
					$result .= '_i18n (\'' . $field->captionI18N . '\')';
				} else {
					$result .= '\'' . str_replace("'", "\'", $field->caption) . '\'';
				}
				$result .= "));\n  }\n";
				$result .= '  }' . "\n";
			}

			//if date, will check if the format is correct
			if (in_array($field->type, array (
					'date',
					'varchardate'
				))) {
				$result .= '     if (CopixDateTime::yyyymmddToDate ($pRecord->' . $field->name . ') === false){' . "\n";
				$result .= '        $errorObject->addError (\'' . $field->name . '\', _i18n (\'copix:dao.errors.date\',';
				if ($field->captionI18N !== null) {
					$result .= '_i18n (\'' . $field->captionI18N . '\')';
				} else {
					$result .= '\'' . str_replace("'", "\'", $field->caption) . '\'';
				}
				$result .= "));\n  }\n";
			}
			//if time, will check if the format is correct
			if (in_array($field->type, array (
					'time',
					'varchartime'
				))) {
				$result .= '     if (CopixDateTime::hhiissToTime ($pRecord->' . $field->name . ') === false){' . "\n";
				$result .= '        $errorObject->addError (\'' . $field->name . '\', _i18n (\'copix:dao.errors.time\',';
				if ($field->captionI18N !== null) {
					$result .= '_i18n (\'' . $field->captionI18N . '\')';
				} else {
					$result .= '\'' . str_replace("'", "\'", $field->caption) . '\'';
				}
				$result .= "));\n  }\n";
			}
			if (in_array ($field->type, array('datetime'))) {
				$result .= '	if (CopixDateTime::yyyymmddhhiisstodatetime ($pRecord->'.$field->name.') === false){'."\n";
				$result .= '		$errorObject->addError(\'' . $field->name . '\', _i18n (\'copix:dao.errors.yyyymmddhhiiss\',array(';
				if ($field->captionI18N !== null) {
					$result .= '_i18n (\'' . $field->captionI18N . '\')';
				} else {
					$result .= '\'' . str_replace("'", "\'", $field->caption) . '\'';
				}
				$result .= ', $pRecord->' . $field->name . ')';
				$result .= "));\n  }\n";
			}
		}
		$result .= '  return $errorObject->isError () ? $errorObject->asArray () : true;' . "\n";
		$result .= " }\n";

		return $result;
	}

	/**
	 * Génération de la méthode GET pour les DAO.
	 * @return string
	 */
	private function _generatePHP4Get() {
		list ($sqlFromClause, $sqlWhereClause) = $this->getFromClause();
		$sqlSelectClause = $this->getSelectClause();
		$pkFields = $this->_definition->getPropertiesBy ('PkFields');

		//Selection, get.
		$methodName = in_array('get', $this->_definition->getUserDAOClassMethods()) ? '_compiled_get' : 'get';
		$result = ' public function ' . $methodName . ' (' . $this->_writeFieldsInfoWith('name', '$', '', ',', $pkFields) . '){' . "\n";
		$result .= '    $ct = CopixDB::getConnection ($this->_connectionName);' . "\n"; // oblig pour les $ct->quote
		$result .= '    $query = $this->_selectQuery .\'';

		//condition on the PK
		list ($arSqlCondition, $sqlCondition) = $this->_buildConditionsForNewDB ($pkFields, '', true);
		$glueCondition = ($sqlWhereClause != '' ? ' AND ' : ' WHERE ');

		if ($sqlCondition != '') {
			$sqlCondition = ($sqlCondition == '' ? '' : $glueCondition) . $sqlCondition;
		}
		$result .= $sqlCondition;
		$result .= "';\n"; // ends the query
		$result .= '    $results = new CopixDAORecordIterator ($ct->doQuery ($query, ' . $this->_makeArrayParamsForQuery($arSqlCondition) . '), $this->getDAOId ());' . "\n";
		$result .= '    if (isset ($results[0])){return $results[0]; }else{return false; }' . "\n";
		$result .= " }\n";

		return $result;
	}

	/**
	 * Génération de la fonction PHP pour la récupération de tous les enregistrements
	 * @return string
	 */
	private function _generatePHP4FindAll ($pIterator) {
		if ($pIterator){
			$methodBaseName = 'iFindAll';
			$queryMethod = 'iDoQuery';
		}else{
			$methodBaseName = 'findAll';
			$queryMethod = 'doQuery';
		}

		//Selection, findAll.
		$methodName = in_array($methodBaseName, $this->_definition->getUserDAOClassMethods()) ? '_compiled_'.$methodBaseName : $methodBaseName;
		$result = ' public function ' . $methodName . ' (){' . "\n";
		$result .= '    return new CopixDAORecordIterator (CopixDB::getConnection ($this->_connectionName)->'.$queryMethod.' ($this->_selectQuery), $this->getDAOId ());' . "\n";
		$result .= " }\n";
		return $result;
	}

	/**
	 * Génération de la méthode insertion dpour les DAO
	 * @return string
	 */
	private function _generatePHP4Insert() {
		$methodName = in_array('insert', $this->_definition->getUserDAOClassMethods()) ? '_compiled_insert' : 'insert';
		$result = ' public function ' . $methodName . ' ($object, $pUseId = false){' . "\n";
		$result .= '   if (is_array ($object)){' . "\n";
		$result .= '      $tmpRecord = _record (\'' . $this->_definition->getDAOId() . '\');' . "\n";
		$result .= '      $tmpRecord->initFromDBObject ($object);' . "\n";
		$result .= '      $object = $tmpRecord;' . "\n";
		$result .= '   }' . "\n";
		$result .= '   if (($checkResult = $this->check ($object)) !== true){
		           throw new CopixDAOCheckException ($checkResult, $object);
		        }';
		$result .= '    $ct = CopixDB::getConnection ($this->_connectionName);' . "\n";

		$config = CopixConfig :: instance();
		$driverName = $config->copixdb_getProfile($this->_definition->getConnectionName());
		$driverName = $driverName->getDatabase();

		$pkai = $this->_getAutoIncrementField();
		if ($useSequence = (in_array ($driverName, array ('oci')) && ($pkai !== null) && ($pkai->sequenceName != ''))) {
			$result .= 'if (! $pUseId){';
			$result .= '     $object->' . $pkai->name . '= $ct->lastId(\'' . $pkai->sequenceName . '\');' . "\n";
			$result .= '}';
		}

		$fieldsNoAuto = $this->_definition->getPropertiesBy ('PrimaryFieldsExcludeAutoIncrement');
		$fields = $this->_definition->getPropertiesBy ('All');
		
		if ($pkai !== null && !$useSequence) {
			$result .= 'if (($object->' . $pkai->name .' !== null) && $pUseId){';
			$result .= '    $query = \'INSERT INTO ' . $this->_definition->getPrimaryTableRealName () . ' (';
			list ($fields, $values, $formatted) = $this->_prepareValuesForNewDB ($fields, 'object->');
			$result .= implode(',', $fields);
			$result .= ') VALUES (';
			$result .= implode(', ', array_values ($formatted));
			$result .= ")';\n";
			$result .= '   $toReturn = $ct->doQuery ($query, ' . $this->_makeArrayParamsForQuery($values) . ');' . "\n";
			$result .= '}else{';
			$result .= '    $query = \'INSERT INTO ' . $this->_definition->getPrimaryTableRealName () . ' (';
			list ($fieldsNoAuto, $values, $formatted) = $this->_prepareValuesForNewDB ($fieldsNoAuto, 'object->');
			$result .= implode(',', $fieldsNoAuto);
			$result .= ') VALUES (';
			$result .= implode(', ', array_values ($formatted));
			$result .= ")';\n";
			$result .= '   $toReturn = $ct->doQuery ($query, ' . $this->_makeArrayParamsForQuery($values) . ');' . "\n";
			$result .= '}';
		}else{
			$result .= '    $query = \'INSERT INTO ' . $this->_definition->getPrimaryTableRealName () . ' (';
			list ($fields, $values, $formatted) = $this->_prepareValuesForNewDB ($fields, 'object->');
			$result .= implode(',', $fields);
			$result .= ') VALUES (';
			$result .= implode(', ', array_values ($formatted));
			$result .= ")';\n";
			$result .= '   $toReturn = $ct->doQuery ($query, ' . $this->_makeArrayParamsForQuery($values) . ');' . "\n";
		}

		//return lastid after inserting for mysql
		if ($pkai !== null) {
			if (($driverName == 'mysql') || ($driverName == 'mssql') || ($driverName == 'sqlite')) {
				$result .= 'if (! $pUseId){';
				$result .= '	$object->' . $pkai->name . '= $ct->lastId();' . "\n";
				$result .= '}';
			}
		}

		$result .= '    return $toReturn;' . "\n}\n";
		return $result;
	}

	/**
	 * Génération de la méthode update pour les DAO
	 * @return string
	 */
	private function _generatePHP4Update() {
		$pkFields = $this->_definition->getPropertiesBy('PkFields');
		$versionFields = $this->_definition->getPropertiesBy('Version');
		$conditionFields = array_merge($pkFields, $versionFields);

		$methodName = in_array('update', $this->_definition->getUserDAOClassMethods()) ? '_compiled_update' : 'update';
		$result = ' public function ' . $methodName . ' ($object){' . "\n";
		$result .= '   if (is_array ($object)){' . "\n";
		$result .= '      $tmpRecord = _record (\'' . $this->_definition->getDAOId() . '\');' . "\n";
		$result .= '      $tmpRecord->initFromDBObject ($object);' . "\n";
		$result .= '      $object = $tmpRecord;' . "\n";
		$result .= '   }' . "\n";
		$result .= '   if (($checkResult = $this->check ($object)) !== true){
		           throw new CopixDAOCheckException ($checkResult, $object);
		        }';

		$result .= '    $ct = CopixDB::getConnection ($this->_connectionName);' . "\n";
		$result .= '    $query = \'UPDATE ' . $this->_definition->getPrimaryTableRealName() . ' SET ';
		list ($fields, $values, $formatted) = $this->_prepareValuesForNewDb($this->_definition->getPropertiesBy('PrimaryFieldsExcludePk'), 'object->');
		$sqlSet = '';

		$arSqlFields = array_values ($formatted);
		foreach (array_values ($fields) as $key => $fieldName) {
			$sqlSet .= ', ' . $fieldName . '= ' . $arSqlFields[$key];
		}
		$result .= substr($sqlSet, 1);

		//condition on the PK
//		$arSqlCondition = $this->_buildConditionsForNewDB($pkFields, 'object->');
		list ($arSqlCondition, $sqlCondition) = $this->_buildConditionsForNewDB($pkFields, 'object->');
//		$glueCondition = ($sqlWhereClause != '' ? ' AND ' : ' WHERE ');
		if ($sqlCondition != '') {
			$result .= ' where ' . $sqlCondition;
		}

		$result .= "';\n";
		$result .= '   $affectedRows = $ct->doQuery ($query, ' . $this->_makeArrayParamsForQuery(array_merge($values, $arSqlCondition)) . ');' . "\n";
		if (count($versionFields) > 0) {
			$result .= '   if ($affectedRows === 0){' . "\n";
			$result .= '      throw new CopixDAOVersionException ($object);' . "\n";
			$result .= '   }' . "\n";
		}
		foreach ($versionFields as $versionField) {
			$result .= '$object->' . $versionField->name . ' = $object->' . $versionField->name . ' + 1;' . "\n";
		}
		$result .= '   return $affectedRows;' . "\n";
		$result .= " }\n";
		return $result;
	}

	/**
	 * Supression d'un élément
	 * @return string
	 */
	private function _generatePHP4Delete() {
		$pkFields = $this->_definition->getPropertiesBy('PkFields');

		$methodName = in_array('delete', $this->_definition->getUserDAOClassMethods()) ? '_compiled_delete' : 'delete';
		$result = ' public function ' . $methodName . ' (' . $this->_writeFieldsInfoWith('name', '$', '', ',', $pkFields) . '){' . "\n";
		$result .= '    $ct = CopixDB::getConnection ($this->_connectionName);' . "\n";
		$result .= '    $query = \'DELETE FROM ' . $this->_definition->getPrimaryTableRealName() . ' where ';
/*
		$arSqlCondition = $this->_buildConditionsForNewDB ($pkFields);
		$sqlCondition = array ();
		foreach ($arSqlCondition as $fieldName => $fieldValue) {
			$sqlCondition[] = $this->_definition->getPrimaryTableRealName().'.'.substr($fieldName, 1) . " '.($fieldValue === null ? 'IS' : '=').' " . $fieldName;
		}
		$sqlCondition = implode (' AND ', $sqlCondition);
*/
		list ($arSqlCondition, $sqlCondition) = $this->_buildConditionsForNewDB($pkFields);
        $result .= $sqlCondition;

		$result .= "';\n"; //ends the query
		$result .= '   return $ct->doQuery ($query, '.$this->_makeArrayParamsForQuery ($arSqlCondition).');' . "\n";
		$result .= " }\n"; //ends delete function
		return $result;
	}
	
	/**
	 * Génération de la méthode deleteBy
	 * @return string 
	 */
	private function _generatePHP4DeleteBy() {
		list ($sqlFromClause, $sqlWhereClause) = $this->getFromClause();

		$methodName = in_array('deleteBy', $this->_definition->getUserDAOClassMethods()) ? '_compiled_deleteBy' : 'deleteBy';
		$result = ' public function ' . $methodName . ' ($searchParams){' . "\n";
		$result .= '    $ct = CopixDB::getConnection ($this->_connectionName);' . "\n";
		$result .= '    $query = \'DELETE FROM ' . $this->_definition->getPrimaryTableRealName(). '\';' . "\n";	
        $result .= '    $params = array ();';
		//les conditions du By de la mthode deleteBy.
		$result .= '    if (!$searchParams->isEmpty ()){' . "\n";
		$result .= '       $query .= \' WHERE \';' . "\n";

		//gnration des paramtres de la mthode explain
		$fieldsType = array ();
		$fieldsTranslation = array ();

		foreach ($this->_definition->getProperties() as $name => $field) {
			$fieldsTranslation[] = '\'' . $field->name . '\'=>array(\'' . $field->fieldName . '\', \'' . $field->type . '\',\'' . $field->table . '\',\'' . str_replace("'", "\\'", $field->selectMotif) . '\')';
		}
		$fieldsTranslation = '         array(' . implode(', ', $fieldsTranslation) . ')';

		//fin de la requete
		$result .= '      list ($querySql, $params) = $searchParams->explainSQL (' . "\n" . $fieldsTranslation . ');' . "\n";
		$result .= '      $query .= $querySql;' . "\n";
		$result .= "    }\n";
		$result .= '    return $ct->doQuery ($query, $params);' . "\n";
		$result .= " }\n";
		return $result;
	}

	/**
	 * Génération de la méthode countBy
	 * @return string 
	 */
	private function _generatePHP4CountBy() {
		list ($sqlFromClause, $sqlWhereClause) = $this->getFromClause();

		$methodName = in_array('countBy', $this->_definition->getUserDAOClassMethods()) ? '_compiled_countBy' : 'countBy';
		$result = ' public function ' . $methodName . ' ($searchParams){' . "\n";
		
		$result .= '    $ct = CopixDB::getConnection ($this->_connectionName);' . "\n";
		$result .= '    $query = \'SELECT COUNT(*) AS "COUNT" '.$sqlFromClause . $sqlWhereClause. '\';' . "\n";;
        $result .= '    $params = array ();';
		//les conditions du By de la mthode deleteBy.
		$result .= '    if (!$searchParams->isEmpty ()){' . "\n";
		$result .= '       $query .= \'' . ($sqlWhereClause != '' ? ' AND ' : ' WHERE ') . '\';' . "\n";

		//gnration des paramtres de la mthode explain
		$fieldsType = array ();
		$fieldsTranslation = array ();

		foreach ($this->_definition->getProperties() as $name => $field) {
			$fieldsTranslation[] = '\'' . $field->name . '\'=>array(\'' . $field->fieldName . '\', \'' . $field->type . '\',\'' . $field->table . '\',\'' . str_replace("'", "\\'", $field->selectMotif) . '\')';
		}
		$fieldsTranslation = '         array(' . implode(', ', $fieldsTranslation) . ')';

		//fin de la requete
		$result .= '      list ($querySql, $params) = $searchParams->explainSQL (' . "\n" . $fieldsTranslation . ');' . "\n";
		$result .= '      $query .= $querySql;' . "\n";
		$result .= "    }\n";
		$result .= '    $result = $ct->doQuery ($query, $params);' . "\n";
		$result .= '    return $result[0]->COUNT;';
		$result .= " }\n";
		return $result;
	}	
	
	/**
	 * Génération de la méthode findBy
	 * @return string 
	 */
	private function _generatePHP4FindBy($pIterator) {
		if ($pIterator){
			$methodBaseName = 'iFindBy';
			$queryMethod = 'iDoQuery';
		}else{
			$methodBaseName = 'findBy';
			$queryMethod = 'doQuery';
		}
		
		list ($sqlFromClause, $sqlWhereClause) = $this->getFromClause();

		$methodName = in_array($methodBaseName, $this->_definition->getUserDAOClassMethods()) ? '_compiled_'.$methodBaseName : $methodBaseName;
		$result = ' public function ' . $methodName . ' ($searchParams, $joins=array()){' . "\n";
		$result .= '    $ct = CopixDB::getConnection ($this->_connectionName);' . "\n";
		$result .= '    $query = $this->_selectQuery;' . "\n";
		$result .= '    $params = array ();'. "\n\n";
		
		//generation de jointure
	    $result.= <<<JOIN
	    if(count(\$joins)){
	        \$query = preg_replace('/SELECT (.*?) FROM /','SELECT * FROM ',\$query);
		    foreach(\$joins as \$table=>\$join){
     	        \$query.=' LEFT JOIN '.\$table;
        	    \$query.=' ON '.\$join[0].\$join[1].\$join[2].' ';
            }
        }
JOIN;
		//les conditions du By de la mthode findBy.
		$result .= '    if (!$searchParams->isEmpty ()){' . "\n";
		$result .= '       $query .= \'' . ($sqlWhereClause != '' ? ' AND ' : ' WHERE ') . '\';' . "\n";

		//gnration des paramtres de la mthode explain
		$fieldsType = array ();
		$fieldsTranslation = array ();

		foreach ($this->_definition->getProperties() as $name => $field) {
			$fieldsTranslation[] = '\'' . $field->name . '\'=>array(\'' . $field->fieldName . '\', \'' . $field->type . '\',\'' . $field->table . '\',\'' . str_replace("'", "\\'", $field->selectMotif) . '\')';
		}
		$fieldsTranslation = '         array(' . implode(', ', $fieldsTranslation) . ')';

		//fin de la requete
		$result .= '      list ($querySql, $params) = $searchParams->explainSQL (' . "\n" . $fieldsTranslation . ', $ct);' . "\n";
		$result .= '      $query .= $querySql;' . "\n";
		$result .= "    }\n";

		$result .= '    if(count($joins)) return $ct->'.$queryMethod.' ($query, $params, $searchParams->getOffset (), $searchParams->getCount ());'."\n";
		$result .= '    return new CopixDAORecordIterator ($ct->doQuery ($query, $params, $searchParams->getOffset (), $searchParams->getCount ()), $this->getDAOId ());' . "\n";
		$result .= " }\n";
		return $result;
	}

	/**
	 * Génération du code des méthodes personnalisées.
	 * @return string 
	 */
	function _generatePHP4DefinedMethods() {
		list ($sqlFromClause, $sqlWhereClause) = $this->getFromClause();

		$result = '';
		// autres méthodes personnaliss
		$allField = array ();
		foreach ($this->_definition->getPropertiesBy ('All') as $field) {
			$allField[$field->name] = array (
				$field->fieldName,
				$field->type,
				$field->table,
				str_replace("'",
				"\\'",
				$field->selectMotif
			));
		}
		$primaryFields = array ();
		foreach ($this->_definition->getPropertiesBy('PrimaryTable') as $field) { // pour delete
			$primaryFields[$field->name] = array (
				$field->fieldName,
				$field->type,
				'',
				str_replace("'",
				"\\'",
				$field->selectMotif
			));
		}
		$ct = null;

		foreach ($this->_definition->getMethods() as $name => $method) {
			$result .= ' function ' . $method->name . ' (';
			$mparam = implode(', $', $method->getParameters());
			if ($mparam != '') {
				$result .= '$' . $mparam;
			}
			$result .= "){\n";
			$result .= '    $ct = CopixDB::getConnection ($this->_connectionName);' . "\n";
			$limit = '';

			switch ($method->type) {
				case 'delete' :
					$result .= '    $query = \'DELETE FROM ' . $this->_definition->getPrimaryTableRealName() . ' \'';
					$glueCondition = ' WHERE ';
					break;
				case 'update' :
					$result .= '    $query = \'UPDATE ' . $this->_definition->getPrimaryTableRealName() . ' SET ';
					$updatefields = $this->_definition->getPropertiesBy('PrimaryFieldsExcludePk');
					$sqlSet = '';
					foreach ($method->_values as $propname => $value) {
						$sqlSet .= ', ' . $updatefields[$propname]->fieldName . '= ' . $value;
					}
					$result .= substr($sqlSet, 1) . ' \'';

					$glueCondition = ' WHERE ';
					break;
				case 'selectfirst' :
				case 'select' :
				default :
					$result .= '    $query = $this->_selectQuery';
					$glueCondition = ($sqlWhereClause != '' ? ' AND ' : ' WHERE ');
					if ($method->getLimit() !== null) {
						$limit = 'array (), ' . $method->_limit['offset'] . ', ' . $method->_limit['count'];
					}

					break;
			}

			if ($method->getSearchParams() !== null) {
				if ($method->type == 'delete' || $method->type == 'update') {
					$sqlCondition = trim($method->getSearchParams()->explainPHPSQL($primaryFields, $ct));
				} else {
					$sqlCondition = trim($method->getSearchParams()->explainPHPSQL($allField, $ct));
				}

				if (trim($sqlCondition) != '') {
					$result .= '.\'' . $glueCondition . $sqlCondition . "';\n";
				} else {
					$result .= ";\n";
				}
			} else {
				$result .= ";\n";
			}

			switch ($method->type) {
				case 'delete' :
				case 'update' :
					$result .= '    return $ct->doQuery ($query);' . "\n";
					break;
				case 'selectfirst' :
					$result .= '    $results = new CopixDAORecordIterator ($ct->doQuery ($query), $this->getDAOId ());';
					$result .= '    if (isset ($results[0])){return $results[0];}else{return false;}';
					break;
				case 'select' :
				default :
					$result .= '    return new CopixDAORecordIterator ($ct->doQuery ($query' . $limit . '), $this->getDAOId ());' . "\n";
			}
			$result .= " }\n";
		}
		return $result;
	}

	/**
	 * Génération du code PHP pour le constructeur du DAO
	 * @return string
	 */
	function _generatePHP4DAOConstructor() {
		list ($sqlFromClause, $sqlWhereClause) = $this->getFromClause();
		$sqlSelectClause = $this->getSelectClause();
		
		if ($this->_definition->getUserDefinedDAOName() !== null) {
			$result = ' public function __construct ($pConnectionName = null) {' . "\n";
			if (in_array(strtolower(CopixDAOFactory :: getDAOName($this->_definition->getDAOId(), false)), $this->_definition->getUserDAOClassMethods()) or in_array('__construct', $this->_definition->getUserDAOClassMethods())) {
				$result .= '  parent::__construct ();' . "\n";
			}
			//ne remplace que si on spécifie une connexion à utiliser
			$result .= '  if ($pConnectionName != null) $this->_connectionName = $pConnectionName;';
			$result .= '  $this->_selectQuery =\'' . $sqlSelectClause . $sqlFromClause . $sqlWhereClause . '\';' . "\n";
			$result .= " }\n";
		} else {
			$result = ' public function __construct ($pConnectionName = null) {' . "\n";
			//ne remplace que si on spécifie une connexion à utiliser
			$result .= '  if ($pConnectionName != null) $this->_connectionName = $pConnectionName;';
			$result .= '  $this->_selectQuery =\'' . $sqlSelectClause . $sqlFromClause . $sqlWhereClause . '\';' . "\n";
			$result .= ' }' . "\n";
		}
		return $result;
	}

	/**
	 * Génération du code PHP pour la fonction de description des champs
	 * @return array
	 */
	private function _generatePHP4DAODescribeField() {
	    $fields = $this->_definition->getPropertiesBy('All');
	    
	    $result = "public function getFieldsDescription() {\n";
	    $generator = new CopixPHPGenerator ();
	    $result .= $generator->getVariableDeclaration ('$fields',$fields);
	    $result .= "return \$fields;\n";
	    $result .= '}';
	    
	    return $result;
	}
	
	/**
	 * Fonction qui retourne l'identifiant du DAO
	 * @return string
	 */
	private function _generatePHP4GetDAOId (){
	    $daoId = $this->_definition->getDAOId ();

	    $result = "public function getDAOId () {\n";
	    $generator = new CopixPHPGenerator ();
	    $result .= $generator->getVariableDeclaration ('$daoId', $daoId);
	    $result .= "return \$daoId;\n";
	    $result .= '}';

	    return $result;
	}
}
?>