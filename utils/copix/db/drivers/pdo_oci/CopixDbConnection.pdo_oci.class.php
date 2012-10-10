<?php
/**
 * @package		copix
 * @subpackage	db
 * @author		Croës Gérald
 * @copyright	2001-2006 CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @ignore
 */
require_once (COPIX_PATH.'db/drivers/oci/CopixDbOciResultSetIterator.class.php');

/**
 * Classe de connexion à oracle en utilisant PDO & les drivers OCI
 * @package		copix
 * @subpackage	db
 */
class CopixDBConnectionPDO_OCI extends CopixDBPDOConnection {

	/**
	 * Longueur par défaut des paramètres à lier.
	 *
	 * @var integer
	 **/
	private $_defaultBindLength = -1;

	/**
	 * Type par défaut des paramètres à lier.
	 *
	 * @var integer
	 **/
	private $_defaultBindType = CopixDBQueryParam::DB_AUTO;


	/**
	 * Construction de l'objet PDO
	 * @todo prendre en charge les options spécifiques au driver
	 */
	public function __construct ($pProfil){
		parent::__construct ($pProfil);

		// Récupère la longueur par défuat
		if(isset($parts['default_bind_length']) && is_numeric($parts['default_bind_length'])) {
			$this->_defaultBindLength = intval($parts['default_bind_length']);
		}

		// Récupère le type de paramètre par défaut
		switch(isset($parts['default_bind_type']) ? strtolower($parts['default_bind_type']) : null) {
			case 'db_string': case 'string':
				$this->_defaultBindType = CopixDBQueryParam::DB_STRING;
				break;
			case 'db_int': case 'int':
				$this->_defaultBindType = CopixDBQueryParam::DB_INT;
				break;
		}
	}

	/**
	 * Analyse la requète pour qu'elle passe sans encombre dans le driver PDO_OCI
	 */
	protected function _parseQuery ($pQueryString, &$pParameters = array (), $pOffset = null, $pCount = null){
		$toReturn = parent::_parseQuery ($pQueryString, $pParameters, $pOffset, $pCount);

		//si ce n'est pas un select, on vérifie de suite s'il y a un with en début
		if (! $toReturn['isSelect']){
			if (stripos (trim ($pQueryString), 'with') === 0){
				$toReturn['isSelect'] = $this->_isWith ($toReturn['query']);
			}
		}

		//only for select query
		if ($toReturn['isSelect'] && ($pOffset !== null || $pCount !== null)){
			$toReturn['query'] = $this->_parseLimit ($toReturn['query'], $pOffset, $pCount);
			$toReturn['count'] = true;
			$toReturn['offset'] = true;
		}

		if (! $toReturn ['isSelect']){
			$toReturn['isSelect'] = stripos (trim ($pQueryString), 'DESCRIBE') === 0;
		}

		return $toReturn;
	}
	
	/**
	 * Recherche dans un tableau de mots $pArWords, a partir de la position $pStart le mot $pWord (non échapé)
	 *
	 * @param string $pWord le mot à rechercher
	 * @param int $pStart ou démarrer dans la recherche 
	 * @param array $pArWords le tableau des mots ou rechercher
	 * @return la position de l'élément suivant, la position finale si non trouvé
	 */
	protected function _getNextCharInArray ($pWord, $pStart, $pArWords){
		$countArWords = count ($pArWords);
		for ($i = $pStart; $i < $countArWords; $i++){
			if (($pArWords[$i] === $pWord) && $pArWords[$i-1] !== '\\'){
				return $i;
			}
		}
		return $i;
	}

	/**
	 * recherche dans une requête si elle est de la forme WITH X as (select ), ... select
	 *
	 * @param string $pQueryString
	 * @return boolean
	 */
	protected function _isWith ($pQueryString){
		//On met la requête en minuscule pour la suite
		$pQueryString = strtolower ($pQueryString);
	
		//afin de pouvoir découper la chaine en mots, on rajoute des espaces entre tous les caractères spéciaux, puis on découpe la chaine avec les espaces
		$pQueryString = str_replace (array (',', '(', ')', "\n", "\r", "\t", '\\'), array (' , ', ' ( ', ' ) ', ' ', ' ', ' ', ' \\ '), $pQueryString);
		$pQueryString = preg_replace ('/[ ]+/', ' ', $pQueryString);
		$queryParts = explode (' ', $pQueryString);
		
		//on s'attend a avoir, en premier mot clef, un with
		$expect = "with";
		$countQueryParts = count ($queryParts);
		for ($i=0; $i < $countQueryParts; $i++){
			switch ($queryParts[$i]){
				case '\\':
					$i++;
					break;
				case "'":
					if ($expect !== "fieldName"){
						return false;
					}
					$i = $this->_getNextCharInArray ("'", $i, $queryParts);
					$expect = "as";
					break;
				case '"':
					if ($expect !== "fieldName"){
						return false;
					}
					$i = $this->_getNextCharInArray ('"', $i, $queryParts);
					$expect = "as";
					break;
				case 'as':
					if ($expect !== "as"){
						return false;
					}
					$expect = "(";
					break;
				case '(';
					if ($expect !== "("){
						return false;
					}
					$parenthesisCount = 1;
					while ($parenthesisCount >= 1 && $i < count ($queryParts)){
						$i++;
						if ($queryParts[$i] === '"'){
							$i = $this->_getNextCharInArray ('"', $i+1, $queryParts);
						}elseif ($queryParts[$i] === "'"){
							$i = $this->_getNextCharInArray ("'", $i+1, $queryParts);
						}elseif ($queryParts[$i] == ')'){
							$parenthesisCount--;
						}elseif ($queryParts[$i] == '('){
							$parenthesisCount++;
						}
					}
					$expect = "keyword";
					break;
	
				case 'select' :
					if ($expect !== "keyword"){
						return false;
					}
					return true;
	
				case ',':
					$expect = "fieldName";
					break;
	
				case 'with':
					$expect = "fieldName";
					break;
	
				default:
					if ($expect !== "fieldName"){
						return false;
					}
					$expect = "as";
			}
		}

		//Si on est arrivé jusqu'ici, ce n'est pas un select
		return false;
	}

	/**
	 * Récupère la requête pour avoir une limitation dans les résultats.
	 * @param string $pQuery la requête à limiter
	 * @param int $pOffset l'offset à partir duquel on souhaite récupèrer l'ensemble de résultat
	 * @param int $pCount le nombre d'élément que l'on souhaite récupèrer.
	 */
	function _parseLimit ($pQuery, $pOffset, $pCount){
		return "SELECT copixselect2.* FROM (SELECT rownum crownum, copixselect1.* FROM (".$pQuery.") copixselect1 ) copixselect2
                WHERE crownum BETWEEN ".$pOffset." AND ".($pOffset+$pCount);
	}

	/**
	 * La liste des tables
	 */
	public function getTableList () {
		$toReturn = array ();
		foreach ($this->doQuery ('SELECT table_name FROM all_tables') as $key=>$field){
			$toReturn[] = $field->table_name;
		}
		return $toReturn;
	}

	/**
	 * Description d'une table
	 */
	public function getFieldList ($pTableName) {
		$toReturn = array ();

		$arType = array('FLOAT'=>'float','LONG'=>'float','NUMBER'=>'float','CHAR'=>'varchar','VARCHAR2'=>'varchar','NVARCHAR2'=>'varchar','NCHAR'=>'varchar','CLOC'=>'varchar','NCLOB'=>'varchar','BLOB'=>'varchar','DATE'=>'datetime');

		$query = "SELECT   a.column_name AS name, " .
                 "         decode (a.nullable, 'Y', 0, 'N', 1) AS not_null, " .
                 "         a.data_type AS col_type, " .
                 "         a.data_length AS col_size, " .
                 "         a.data_default AS default_val " .
                 "FROM     all_tab_columns a ".
                 "WHERE    upper(a.table_name) = UPPER('$pTableName') " .
                 "ORDER BY a.column_id";

		$arResult=$this->doQuery($query);

		foreach ($arResult as $key => $val) {
			$field = new StdClass ();
			if (isset($arType[$val->col_type])) {
				$field->type = $arType[$val->col_type];
			} else {
				throw new CopixDBException("Le type $field->type n'est pas reconnu");
			}
			$field->maxlength = $val->col_size;
			$field->sequence='';
			$field->pk=false;

			$field->name    = $val->name;
			$field->caption = $val->name;
			$field->nonull  = $val->not_null;
			$field->length  = $val->col_size;
			$field->defaultValue = $val->default_val;

			$field->required = $val->not_null=='1' ? 'yes' : 'no';
			$toReturn[strtoupper($field->name)] = $field;
		}

		$primary = "select c.column_name from user_cons_columns c, user_constraints t	where upper(t.table_name) = UPPER('$pTableName') and t.constraint_type='P' and c.constraint_name=t.constraint_name order by c.position";
		$unique = "select c.column_name from user_cons_columns c, user_constraints t	where upper(t.table_name) = UPPER('$pTableName') and t.constraint_type='P' and c.constraint_name=t.constraint_name order by c.position";

		$arPrimary = $this->doQuery($primary);
		foreach($arPrimary as $field) {
			$toReturn[strtoupper($field->column_name)]->primary = 'yes';
			$toReturn[strtoupper($field->column_name)]->pk = true;
		}
		return $toReturn;
	}

	/**
	 * Lance une procédure stockées sur la connextion courante
	 * @param string $pProcedure la procédure a lancer
	 * @param array $pParams un tableau de paramètre à donner à la procédure
	 *  le tableau est de la forme $pParams['nom'] = array ('type'=>, 'length'), 'in'=>, ''
	 * @return array un tableau de résultat avec array['results'] = résultats,
	 *    array['params']['nomParam'] = valeur
	 */
	public function doProcedure ($pProcedure, $pParams){
		//Connexion
		$parts = $this->_profil->getConnectionStringParts ();

		$ct = oci_connect ($this->_profil->getUser (), $this->_profil->getPassword (), $parts['dbname']);

		if ($ct === false){
			throw new CopixDBException ('Impossible de se connecter');
		}

		$alterSession = 'ALTER SESSION SET NLS_NUMERIC_CHARACTERS=\'.,\'';
		$stmt = ociparse ($ct, $alterSession);
		ociexecute ($stmt);

		$extras = array ('binds' => array ());
		_log ($alterSession, 'query', CopixLog::INFORMATION, $extras);

		//Préparation de la requête
		$stmt = ociparse ($ct, $pProcedure);
		if ($stmt === false){
			throw new CopixDBException ($pProcedure);
		}
		//On analyse les paramètres
		$arVariablesName = array ();
		$arVariables = array ();
		foreach ($pParams as $name=>$param){
			$variableName = substr ($name, 1);
			if (! is_array ($param)){
				$$variableName = $param;
				if (!oci_bind_by_name ($stmt, $name, $$variableName, $this->_defaultBindLength)){
					throw new CopixDBException ("Bind ['$name'] - [".$$variableName."] taille [".$arVariables[$variableName]['maxlength']."] type [".$this->_convertProcedureParam ($arVariables[$variableName]['type'])."]");
				}
				$arVariables[$variableName]['type'] = $this->_defaultBindType;
				$arVariables[$variableName]['value'] = $param;
			}else{
				if (!isset ($$variableName)){
					$$variableName = isset ($param['value']) ? $param['value'] : null;
				}
				$arVariables[$variableName] = $param;

				if (! isset ($arVariables[$variableName]['type'])){
					$arVariables[$variableName]['type'] = $this->_defaultBindType;
				}
				if (! isset ($arVariables[$variableName]['maxlength'])){
					$arVariables[$variableName]['maxlength'] = $this->_defaultBindLength;
				}

				if ($arVariables[$variableName]['type'] === CopixDBQueryParam::DB_CURSOR){
					$$variableName = oci_new_cursor ($ct);
				}
				if (! oci_bind_by_name ($stmt, $name, $$variableName, $arVariables[$variableName]['maxlength'], $this->_convertProcedureParam ($arVariables[$variableName]['type']))){
					oci_free_statement ($stmt);
					throw new CopixDBException ("Bind ['$name'] - [".$$variableName."] taille [".$arVariables[$variableName]['maxlength']."] type [".$this->_convertProcedureParam ($arVariables[$variableName]['type'])."]");
				}
			}
		}

		//on exécute la requête
		if (! ociexecute ($stmt, OCI_DEFAULT)){
			$statementErrors = oci_error ($stmt);
			oci_free_statement ($stmt);
			oci_rollback ($ct);
			throw new CopixDBException ('[CopixDB] Impossible d\'exécuter la procédure '.$pProcedure.' - '.var_dump ($statementErrors).' avec les variables '.var_dump ($arVariables));
		}

		$extras = array ('binds' => $pParams);
		_log ($pProcedure, 'query', CopixLog::INFORMATION, $extras);

		//analyse des résultats
		foreach ($arVariables as $name=>$value){
			//Si c'est un curseur
			if ($value['type'] === CopixDBQueryParam::DB_CURSOR){
				if (!@ociexecute ($$name)){
					oci_free_statement ($$name);
					oci_free_statement ($stmt);
					oci_rollback ($ct);
					throw new CopixDBException ("Impossible de récupèrer l'ensemble de résultat de la variable $name");;
				}
				$toReturn[':'.$name] = array ();
				while ($r = oci_fetch_array ($$name)){
					$toReturn[':'.$name][] = $this->_getCases ($r);
				}
				oci_free_statement ($$name);
			}else{
				$toReturn[':'.$name] = $$name;
			}
		}

		oci_commit($ct);
		oci_free_statement ($stmt);
		return $toReturn;
	}

	/**
	 * Converti les constantes CopixDBQueryParam en constante du driver correspondant
	 * @param int $pParamType le type du paramètre (CopixDBQueryParam)
	 */
	function _convertProcedureParam ($pParamType) {
		switch ($pParamType){
			case CopixDBQueryParam::DB_CURSOR : return OCI_B_CURSOR;
			default: return null;
		}
	}

	/**
	 * Indique si le driver est disponible
	 * @return bool
	 */
	public static function isAvailable (){
		if (!class_exists ('PDO', false)){
			return false;
		}
		return in_array ('oci', PDO::getAvailableDrivers ());
	}

	/**
	 * retourne le dernier identifiant généré (à partir d'une séquence)
	 * @return int
	 */
	public function lastId ($pFromSequence = null){
		$result     = $this->doQuery ('select '.$pFromSequence.'.nextVal from dual');
		return $result[0]->nextVal;
	}

	/**
	 * Lancement d'une requête SQL
	 * @param	string	$pQueryString	la requête à lancer
	 * @param	array	$pParameters	tableau de paramètres
	 * @param	int		$pOffset		l'offset à partir duquel nous allons lire les résultats => Si null, pas d'offset
	 * @param	int		$pCount			le nombre d'élément que l'on souhaites récupérer depuis la base. Si null => le maximum
	 */
	public function doQuery ($pQueryString, $pParameters = array (), $pOffset = null, $pCount = null){
		$resultsOfQueryParsing = $this->_parseQuery ($pQueryString, $pParameters, $pOffset, $pCount);
		$extras = array ('binds' => $pParameters);
		_log ($resultsOfQueryParsing['query'], 'query', CopixLog::INFORMATION, $extras);

		if ($resultsOfQueryParsing['isSelect'] && ($resultsOfQueryParsing['offset'] === false || $resultsOfQueryParsing['count'] === false)){
			//Si nous sommes dans un select et que l'offset et le count ne sont pas gérés autoamtiquement, alors il nous faut un curseur "movable"
			//TODO: lorsque les curseurs movable seront supportés, mettre ça
			//         $stmt = $this->_pdo->prepare ($resultsOfQueryParsing['query'], array(PDO::ATTR_CURSOR, PDO::CURSOR_SCROLL));
			$stmt = $this->_pdo->prepare($resultsOfQueryParsing['query']);
		}else{
			$stmt = $this->_pdo->prepare($resultsOfQueryParsing['query']);
		}

		if (! $stmt){
			throw new CopixDBException ('Impossible de préparer la requête ['.$resultsOfQueryParsing['query'].']'.serialize ($pParameters).implode ('-', $this->_pdo->errorInfo ()));
		}

		if (! $stmt->execute ($pParameters)){
		}
		if (! $resultsOfQueryParsing['isSelect']){
			return $stmt->rowCount ();
		}

		@$stmt->setFetchMode (PDO::FETCH_ASSOC);
		if ($resultsOfQueryParsing['offset'] && $resultsOfQueryParsing['count']){
			$toReturn= array ();
			while ($row = $stmt->fetch ()){
				$row = $toReturn[] = $this->_getCases ($row, $resultsOfQueryParsing['query']);

			}
			return $toReturn;
		}else{
			$results = array ();

			//hack pour déplacer à l'offset donné.
			$row = true;
			$pLeft = $pCount;

			while (($pLeft > 0 || $pLeft === null) && ($row !== false)){
				if ($row = $stmt->fetch ()){
					if ($pOffset == 0){
						$results[] = $row;
						if ($pLeft !== null){
							$pLeft--;
						}
					}else{
						$pOffset--;
					}
				}
			}
			/*

			for ($toFetch = $pCount, $row = $stmt->fetch(PDO::FETCH_CLASS, PDO::FETCH_ORI_REL, $pOffset === null ? 0 : $pOffset);
			$row !== false && ($toFetch-- > 0 || $toFetch === null);
			$row = $stmt->fetch()){
			$results[] = $row;
			}
			*
			*/
			$stmt->closeCursor ();
			return $results;
		}
	}

	/**
	 * Récupère l'enregistrement avec les bonnes casses
	 * @param array	 $pRow	 l'enregistrement à récupérer
	 * @param string $pQuery la requête exécutée	
	 */
	private function _getCases ($pRow, $pQuery = null) {
		$cases = $this->_getFieldCases ($pRow, $pQuery);
		if ($cases === false){
			return (object) $pRow;
		}else{
			$final = array ();
			foreach ($pRow as $key=>$name){
				$final[$cases[$key]] = $name;
			}
		}
		return (object) $final;			
	}
	
	/**
	 * Extrait la casse des champs depuis un enregistrement résultat et une requête donnée
	 * 
	 * @param $pRow l'enregistrement exemple
	 * @param $pQuery la requête d'ou on extrait l'enregistrement
	 * @return array
	 */
	private function _getFieldCases ($pRow, $pQuery = null){
		static $queries = array ();
		if ($pQuery === null){
		   return false;
		}
		//si en cache, on le retourne
		if (array_key_exists ($pQuery, $queries)){
			return $queries[$pQuery];
		}
		
    	$query = str_replace (array ("\t", "\r", "\n"), array (' ', ' ', ' '), $pQuery);
		if (($pos = strrpos (strtoupper ($query), ' FROM ')) !== false){
			$query = substr ($query, 0, $pos);
		}
		//on calcul les clefs
		$final = array ();
		foreach ($pRow as $key=>$name){
			if (($pos = strrpos (strtoupper ($query), strtoupper ($key)))===false) {
				$final[$key] = $key;
			}else{
				$final[$key] = substr ($query, $pos, strlen ($key));
			}
		}
		
		//on regarde si les clefs sont identiques
		$identical = true;
		foreach ($final as $key=>$keyend){
			if ($key !== $keyend){
				$identical = false;				
			}
		}
		if ($identical){
			$queries[$pQuery] = false;			
		}else{
			$queries[$pQuery] = $final;
		}
		return $queries[$pQuery];
	}
}