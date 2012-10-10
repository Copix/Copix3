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
* Structure stockant les paramètres d'une condition
* @package copix
* @subpackage dao
*/
class CopixDAOSearchParamsCondition {
    /**
    * La condition parente
    */
    var $parent = null;

    /**
    * Les conditions
    */
    var $conditions = array ();

    /**
    * les sous groupes
    */
    var $group = array ();

    /**
    * Le type de groupe (AND / OR)
    */
    var $kind;

    /**
     * Construction du groupe
     * @param	CopixDAOSearchParamsCondition	$parent	Le groupe parent
     * @param	string	$kind	or ou and, si le groupe est régie par un or ou and			
     */
    function __construct ($parent, $kind){
        if (strtolower (get_class ($parent)) == strtolower ('copixdaosearchparamscondition')){
            $this->parent = $parent;
        }
        $this->kind   = $kind;
    }
    
    /**
     * Indique si le groupe de condition est vide
     */
    public function isEmpty (){
    	$toReturn = true;
    	foreach ($this->conditions as $condition){
    		if (array_key_exists ('sql', $condition) && (strlen ($condition['sql']) > 0)){
    			return false;
    		}elseif (array_key_exists ('value', $condition)){
    			if (is_array ($condition['value'])){
    				if (count ($condition['value'])){
    					return false;
    				}
    			}else{
    				return false;
    			}
    		}
    	}
    	foreach ($this->group as $group){
    		if (! $group->isEmpty ()){
    			return false;
    		}
    	}
    	return $toReturn;
    } 
}

/**
* Gestion des critères de recherche
* Permet d'effectuer des recherches dans un DAO précis, en indiquant les critères
* Voir la méthode findBy des objets DAO générés
* @package copix
* @subpackage dao
*/
class CopixDAOSearchParams {
    /**
    * Un objet de type CopixDAOSearchParamConditions
    */
    var $condition;

    /**
     * Les groupes de condition demandés 
     * @var array
     */
    var $groupby = array ();
    
    /**
    * L'ordre de tri
    */
    var $order = array ();

    /**
    * La liste des champsà récupérer
    */
    var $fields = array ();

    /**
    * La condition en cours de parcours
    */
    var $_currentCondition = null;
    
    /**
     * L'offset à partir duquel on souhaites récupérer les enregistrements.
     * Ce paramètre n'est pas pris en compte dans explainSQL mais sert juste de transport d'info
     * Null si rien demandé.
     * @var int  
     */
    private $_offset = null;
    
    /**
     * Le nombre d'enregistrements que l'on souhaite ramener. 
     * Ce paramètre n'est pas pris en compte dans explainSQL mais sert juste de transport d'info
     * Null si rien demandé.
     * @var int  
     */
     private $_count = null;
    
    /**
     * Variable interne qui nous sert à nous assurer d'avoir des noms uniques pour les variables bindées.
     * Ceci est pratique lorsque les DAOSearchConditions disposent de plusieurs valeurs possibles sur un même champs
     * @var int
     */
    private static $_countParam = 0;
    
    /**
     * Tableau des variables utilisées 
     * @var array
     */
    private $_assigned_variables = array ();

    /**
    * Constructeur
    * $param string $type  
    */
    public function __construct ($type = 'AND'){
        $this->condition         = new CopixDAOSearchParamsCondition ($this, $type);
        $this->_currentCondition = $this->condition;
    }
    
    /**
     * Compresse une variable à X caractères à partir de son nom d'origine et s'assure de l'unicité de son utilisation 
     * @param	string	$pVariableName	Le nom de la variable d'origine
     * @param	int		$pTryNum		Le numéro d'essais de génération
     */
    private function _compressVariable ($pVariableName, $pTryNum = 0){
    	if (isset ($this->_assigned_variables[$pVariableName])){
    		return $this->_assigned_variables[$pVariableName];
    	}
    	
    	$suffix =  ($pTryNum === 0) ? '' : $pTryNum;
    	$pTryNum++; 
   	
    	//Compression du nom de la variable, ajout du numéro d'essais
    	$result = CopixFormatter::getReduced ($pVariableName, 30 - strlen ($suffix));
    	$result .= $suffix;

    	//On vérifie que le nom trouvé ne corresponds pas déjà à une variable existante. 
    	if (in_array ($result, $this->_assigned_variables)){
    		return $this->_compressVariable ($pVariableName, $pTryNum);
    	}
    	return $this->_assigned_variables[$pVariableName] = $result;
    }
    

    /**
    * Définition des tris sur les champs
    * @param mixed tableau qui contient la liste des champs par lesquels on souhaite trier
    *   array ($nomChamp, array ($nomChamp, 'ordre'), array ($nomChamp2, 'ordre2'));
    * @return CopixDAOSearchparams ($this) pour permettre l'imbrication des appels
    */
    public function orderBy (){
        $args = func_get_args();
        foreach ($args as $arg){
            if (is_array ($arg)) {
                $this->order[$arg[0]] = $arg[1];
            }else{
                $this->order[$arg] = 'ASC';
            }
        }
        return $this;
    }
    
    /**
    * Définition le groupage de champs
    * @param string $fieldname,$fieldname2,$fieldname3...
    * @return CopixDAOSearchparams ($this) pour permettre l'imbrication des appels
    */
    public function groupBy(){
    	$args = func_get_args();
        foreach ($args as $arg){
            $this->groupby[] = $arg;
        }
        return $this;
    }

    /**
    * Indique si la condition est vide
    * @return boolean
    */
    public function isEmpty (){
    	return $this->condition->isEmpty ()
    		&&  (count ($this->groupby) == 0)
    	 	&& (count ($this->order) == 0);
    }
    
    /**
     * Définit l'offset et le nombre d'enregistrements que l'on souhaites récupérer
     * @param	int	$pOffset l'offset à partir duquel on souhaite récupérer les enregistrements
     * @param	int	$pCount	Le nombre d'enregistrement que l'on souhaite récupérer
     * @return CopixDAOSearchParam
     */
    public function setLimit ($pOffset, $pCount){
    	return $this->setOffset ($pOffset)->setCount ($pCount);
    }
    
    /**
     * Définit l'offset à partir duquel on souhaite récupérer les enregistrements
     * @param	int	$pOffset l'offset à partir duquel on souhaite récupérer les enregistrements
     * @return CopixDAOSearchParam	
     */
    public function setOffset ($pOffset){
    	$this->_offset = $pOffset;
    	return $this;
    }
    
    /**
     * Définit le nombre d'enregistremnet que l'on souhaites récupérer 
     * @param	int	$pCount	Le nombre d'enregistrement que l'on souhaite récupérer
     * @return CopixDAOSearchParam
     */
    public function setCount ($pCount){
    	$this->_count = $pCount;
    	return $this;
    }
    
    /**
     * Récupère le nombre d'enregistrements que l'on souhaite récupérer au maximum
     * @return int
     */
    public function getCount (){
    	return $this->_count;
    }
    
    /**
     * Récupère le numéro d'enregistrement à partir duquel on souhaite afficher les résultats.
     * @return int
     */
    public function getOffset (){
    	return $this->_offset;
    }

    /**
    * Démarre un groupe de condition
    * @param string $pKind le type de groupe que l'on souhaite démarrer
    * @return CopixDAOSearchparams ($this) pour permettre l'imbrication des appels
    */
    public function startGroup ($pKind = 'AND'){
        $this->_currentCondition->group[] = new CopixDAOSearchParamsCondition ($this->_currentCondition, $pKind);
        $this->_currentCondition = $this->_currentCondition->group[count ($this->_currentCondition->group)-1];
        return $this;
    }

    /**
    * Termine un groupe de condition
    * @return CopixDAOSearchparams ($this) pour permettre l'imbrication des appels
    */
    public function endGroup (){
        if ($this->_currentCondition->parent !== null){
            $this->_currentCondition = $this->_currentCondition->parent;
        }
        return $this;
    }

    /**
    * Ajoute une condition
    * @param string $fieldId le nom du champ de la dao sur lequel on ajoute la condition
    * @param string $pCondition la condition à appliquer (=, !=, <>, <, > like, ...)
    * @param mixed $pValue La valeur de recherche (inutile de quotter les chaines)
    * @param string $pKind Si la condition est effectuée en ET ou OU logique (AND / OR)
    * @return CopixDAOSearchparams ($this) pour permettre l'imbrication des appels
    */
    public function addCondition ($pFieldId, $pCondition, $pValue, $pKind = 'and'){
    	//On supporte la condition "!=" pour "<>"
    	if ($pCondition == '!='){
    		$pCondition = '<>';
    	}
        $this->_currentCondition->conditions[] = array ('field_id'=>$pFieldId, 'value'=>$pValue, 'condition'=>$pCondition, 'kind'=>$pKind);
        return $this;
    }
    
    /**
     * Permet de rajouter directement du SQL dans la recherche
     * @param	string	$pSQL	le SQL à intégrer dans la requête
     * @param	array	$pParams	tableau de paramètres relatifs à la chaine
     * @return	CopixDAOSearchParams	($this) pour permettre l'imbrication des appels
     */
    public function addSql ($pSql, $pParams = array (), $pKind = 'and'){
    	$this->_currentCondition->conditions[] = array ('sql'=>$pSql, 'params'=>$pParams, 'kind'=>$pKind);
    	return $this;    	
    }

    /**
    * Transforme le jeu de condition en une chaine SQL
    *
    * @param	 array $fields  tableau des champs à traiter 'nomDePropriete'=>array(0=>'nomeDuChampEnBase' , 1=>'typeDuChamp', 2=>'nomDeLaTable (alias)')
    * @param	 CopixDBConnection	$pConnection la connection utilisée pour la requête dont on demande la génération
    * @return string la partie where de la requête SQL
    */
    public function explainSQL ($fields, $pConnection = null){
        // génération de la clause where
        list ($sql, $params) = $this->_explainSQLCondition ($this->condition, false, $fields, $pConnection);
        $desc  = false;
        $order = array ();
        
        $groupSQL = '';
        $firstGroup = true;
        foreach ($this->groupby as $name){        	
            if (! $firstGroup) {
                $groupSQL .= ', ';
            }
            $firstGroup = false;
            $groupSQL .= $fields[$name][2].'.'.$fields[$name][0];
        }

        if (strlen ($groupSQL) > 0) { 
	        if (trim ($sql) == ''){
	        	$sql = ' 1=1 ';
	        }
	        $sql .= ' GROUP BY '.$groupSQL;
        }
        
        $firstOrder = true;
        $orderSQL = '';
        foreach ($this->order as $name=>$direction){
            if (! $firstOrder) {
                $orderSQL .= ', ';
            }
            $firstOrder = false;
            $orderSQL .= $fields[$name][2].'.'.$fields[$name][0].' '.$direction;
        }

        if (strlen ($orderSQL) > 0) {
            if (trim ($sql) == ''){
              $sql = ' 1=1 ';
            }
            $sql .= ' ORDER BY '.$orderSQL;
        }
        return array ($sql, $params);
    }
	
	/**
	 * retourne la valeur de la variable en fonction de son type, de sa valeur et du type de driver
	 *
	 * @param string	$pType
	 * @param mixed		$pValue
	 * @param string $pDriverName
	 * @return mixed
	 */
    private function _variableValue ($pType, $pValue, $pDriverName) {
    	if ($pDriverName == 'mysql' || $pDriverName == 'sqlite'){
    		//Mysql et Sqlite gèrent les mêmes formats d'entrée pour les dates / datetime / time
        	switch ($pType){
            	case 'date':
					return CopixDateTime::yyyymmddToFormat ($pValue, 'Y-m-d H:i:s');	                				
              	case 'datetime':
					return CopixDateTime::yyyymmddhhiissToFormat ($pValue, 'Y-m-d H:i:s');	                				
              	case 'time':
					return CopixDateTime::hhiissToFormat ($pValue, 'Y-m-d H:i:s');	                				
            }
        }
        return $pValue;
    }	                	
    
    /**
     * Exprime les conditions en SQL
     * @param $pConditions les conditions à mettre dans le SQL
     * @param boolean	$pExplainKind si l'on souhaite expliquer le and / or
     * @param array	$pFields tableau des champs sur lesquels travailler
     *    $pFields[nomPropriete][0] == nom champ
     *    $pFields[nomPropriete][1] == type champ
     *    $pFields[nomPropriete][2] == table
     * @param	CopixDBConnection	$pConnection	La connection qui corresponds à l'emplacement ou l'on souhaite lancer la requête
     * @return string SQL  
     */
    private function _explainSQLCondition ($pConditions, $pExplainKind, $pFields, $pConnection = null){
        $r = ' ';
        $fieldsForQueryParams = array ();

        //direct conditions for the group
        $first = true;
        foreach ($pConditions->conditions as $conditionDescription){
        	//Si c'est une valeur sous la forme d'un tableau mais qu'aucune valeur 
			//n'est indiquée, on passe à la suivante.
         	if (array_key_exists ('value', $conditionDescription) && is_array ($conditionDescription['value']) && 
	              count ($conditionDescription['value']) == 0){
    	       	continue;
            }
            
            //Si c'est une forme SQL et que la chaine est vide, on passe à la condition suivante.
            if (array_key_exists ('sql', $conditionDescription) && (strlen ($conditionDescription['sql']) === 0)){
            	continue;
            }
            
			//Si ce n'est pas le premier passage, il faut ajouter le mot clef relatif à la condition
            if (! $first){
	            $r .= ' '.$conditionDescription['kind'].' ';
            }
            
            //Nous ne sommes plus dans le premier passage.
            $first = false;
            
            if (isset ($conditionDescription['sql'])){
            	//C'est une condition SQL rajoutée à la main.

				//on remplace les noms de paramètres pour s'assurer de l'unicité.
				//On parcours dans le sens inverse de l'ordre alpha pour éviter de remplacer
				//des portions de nom de variable (ex :a avant :ab)
            	krsort ($conditionDescription['params']);
				foreach ($conditionDescription['params'] as $paramName=>$paramValue){
					$conditionDescription['sql'] = str_replace ($paramName, ($newParamName = ($paramName.self::$_countParam)), $conditionDescription['sql']);
					self::$_countParam++;
					//ajout du paramètre dans le tableau des paramètres
					$fieldsForQueryParams[$newParamName] = $paramValue;
				}

				//Ajout de la chaine SQL traitée.
            	$r .= ' '.$conditionDescription['sql'].' ';
            }else{
            	//C'est une condition gérée par addCondition.
	            $prefix = $pFields[$conditionDescription['field_id']][2].'.'.$pFields[$conditionDescription['field_id']][0];
	            $prefixNoCondition = $prefix;
	            $prefix.=' '.$conditionDescription['condition'].' ';
	            
	            if (!is_array ($conditionDescription['value'])){
 					$variableName = ':'.$this->_compressVariable ($pFields[$conditionDescription['field_id']][0].'_'.$pFields[$conditionDescription['field_id']][2].'_'.self::$_countParam);

 					if (($conditionDescription['value'] === null) && ($conditionDescription['condition'] == '=')){
	                   $r .= $prefixNoCondition.' IS NULL';
	                }elseif (($conditionDescription['value'] === null) && ($conditionDescription['condition'] == '<>')){
	                   $r .= $prefixNoCondition.' IS NOT NULL';
	                } else {
	                	$fieldsForQueryParams[$variableName] = $this->_variableValue ($pFields[$conditionDescription['field_id']][1], $conditionDescription['value'], $pConnection ? $pConnection->getProfile ()->getDriverName () : null);
	                	if (($pConnection !== null && $pConnection->getProfile ()->getDriverName () == 'oci') && in_array ($pFields[$conditionDescription['field_id']][1], array ('datetime', 'date', 'time'))) {
	                		if ($pFields[$conditionDescription['field_id']][1] == 'datetime'){
								$r .= 'to_char('.$prefixNoCondition.', \'YYYYMMDDHH24MISS\') '.$conditionDescription['condition'].' '.$variableName;
	                		}elseif ($pFields[$conditionDescription['field_id']][1] == 'date'){
	                			$r .= 'to_char('.$prefixNoCondition.', \'YYYYMMDD\') '.$conditionDescription['condition'].' '.$variableName;
	                		}elseif ($pFields[$conditionDescription['field_id']][1] == 'time'){
	                			$r .= 'to_char('.$prefixNoCondition.', \'HH24MISS\') '.$conditionDescription['condition'].' '.$variableName;
	                		}
	                	}else{
	                		$methodDebut = '';
	            			$methodFin = '';
	                		if (isset ($pFields[$conditionDescription['field_id']][4])){
			            		$methodDebut = $pFields[$conditionDescription['field_id']][4].'(';
			            		$methodFin = ')';
	                		}
	                		$r .= $prefix.$methodDebut.$variableName.$methodFin;
	                	}
	                    self::$_countParam++;
	                }
	            }else{
	                if (count ($conditionDescription['value'])){
	                    $r .= ' ( ';
	                    $firstCV = true;
	                    foreach ($conditionDescription['value'] as $conditionValue){
 							$variableName = ':'.$this->_compressVariable ($pFields[$conditionDescription['field_id']][0].'_'.$pFields[$conditionDescription['field_id']][2].'_'.self::$_countParam);
	                    	if (!$firstCV){
	                            $r .= ' or ';
	                        }
			                if (($conditionValue === null) && ($conditionDescription['condition'] == '=')){
			                   $r .= $prefixNoCondition.' IS NULL';
			                }elseif (($conditionValue === null) && ($conditionDescription['condition'] == '<>')){
			                   $r .= $prefixNoCondition.' IS NOT NULL';
			                } else {
			                	if (($pConnection !== null && $pConnection->getProfile ()->getDriverName () == 'oci') && in_array ($pFields[$conditionDescription['field_id']][1], array ('datetime', 'date', 'time'))){
									if ($pFields[$conditionDescription['field_id']][1] == 'datetime'){
			                			$r .= 'to_char('.$prefixNoCondition.', \'YYYYMMDDHH24MISS\') '.$conditionDescription['condition'].' '.$variableName;
									}elseif ($pFields[$conditionDescription['field_id']][1] == 'date'){
										$r .= 'to_char('.$prefixNoCondition.', \'YYYYMMDD\') '.$conditionDescription['condition'].' '.$variableName;
									}elseif ($pFields[$conditionDescription['field_id']][1] == 'time'){
										$r .= 'to_char('.$prefixNoCondition.', \'HH24MISS\') '.$conditionDescription['condition'].' '.$variableName;
									}
			                	}else{
			                		$methodDebut = '';
			            			$methodFin = '';
			                		if (isset ($pFields[$conditionDescription['field_id']][4])){
					            		$methodDebut = $pFields[$conditionDescription['field_id']][4].'(';
					            		$methodFin = ')';
			                		}
			                		$r .= $prefix.$methodDebut.$variableName.$methodFin;
			                	}
			                	$fieldsForQueryParams[$variableName] = $this->_variableValue ($pFields[$conditionDescription['field_id']][1], $conditionValue, $pConnection ? $pConnection->getProfile ()->getDriverName () : null);
//			                	$fieldsForQueryParams[$variableName] = $conditionValue;
			                }
	                        $firstCV = false;
	                        self::$_countParam++;
	                    }
	                    $r .= ' ) ';
	                }
	            }
            }
        }
        
        //sub conditions
        foreach ($pConditions->group as $conditionDetail){
        	list ($sql, $fields) = $this->_explainSQLCondition ($conditionDetail, !$first, $pFields, $pConnection);
        	$r .= $sql;
        	$fieldsForQueryParams = array_merge ($fieldsForQueryParams, $fields);
        	if (!$conditionDetail->isEmpty ()){
            	$first = false;
        	}
        }
        
        //adds parenthesis around the sql if needed (non empty)
        if (strlen (trim ($r)) > 0){
            $r = ($pExplainKind ? ' '.$pConditions->kind.' ' : '') .'('.$r.')';
        }

        return array ($r, $fieldsForQueryParams);
    }
    
    /**
    * explain in SQL (only the where part of the query)
    * @param array $fields  array of elements 'name'=>array(0=>'fieldname' , 1=>'type', 2=>'table')
    * @return string  where clause
    * @todo vérifier s'il n'y a pas mieux pour faire ça 
    */
    public function explainPHPSQL ($fields, $ct){
        $sql = $this->_explainPHPSQLCondition ($this->condition, $fields, $ct, true);

        $order = array ();
        foreach ($this->order as $name => $way){
            if (isset($fields[$name])){
               $order[] = $fields[$name][2].'.'.$fields[$name][0].' '.$way;
            }
        }
        if(count ($order) > 0){
            if(trim($sql) =='') {
				$sql .= ' 1=1 ';
			}
            $sql .= ' ORDER BY '.implode (', ', $order);
        }
        return $sql;
    }

    /**
    * explain in SQL a single level of ConditionGroup
    * @param array $fields  array of elements 'name'=>array(0=>'fieldname' , 1=>'type', 2=>'table', 3=>'motif')
    * @param array $condition array of associative array representing conditions. 'fieldname'=>array ('fieldId'=>, 'value'=>, 'condition'=>, 'php'=>)
    * @todo vérifier s'il n'y a pas mieux pour ça
    */
    private function _explainPHPSQLCondition ($condition, & $fields, $ct, $principal=false){
        $r = ' ';

        //direct conditions for the group
        $first = true;
        foreach ($condition->conditions as $condDesc){
            if (!$first){
                $r .= ' '.$condition->kind.' ';
            }
            $first = false;

            $property=$fields[$condDesc['field_id']];

            if(isset($property[2]) && $property[2] != ''){
               $prefix = $property[2].'.'.$property[0];
            }else{
               $prefix = $property[0];
            }

            if(isset($property[3]) && $property[3] != '' &&$property[3] != '%s'){
                $prefix=sprintf($property[3], $prefix);
            }

            $prefixNoCondition = $prefix;
            $prefix.=' '.$condDesc['condition'].' '; // ' ' pour les like..

                if (!is_array ($condDesc['value'])){
                    if ($condDesc['condition'] == '='){//handles equality of "NULL" values.
                       $r .= $prefixNoCondition.'\'.('.$condDesc['value'].'===null ? \' IS \' : \' = \').\''.$this->_preparePHPValue($condDesc['value'],$property[1]);
                    }elseif ($condDesc['condition'] == '<>'){
                       $r .= $prefixNoCondition.'\'.('.$condDesc['value'].'===null ? \' IS NOT \' : \' <> \').\''.$this->_preparePHPValue($condDesc['value'],$property[1]);
                    }else{
                        $r .= $prefix.$this->_preparePHPValue($condDesc['value'],$property[1]);
                    }
                }else{
                    $r .= ' ( ';
                    $firstCV = true;
                    foreach ($condDesc['value'] as $conditionValue){
                        if (!$firstCV){
                            $r .= ' or ';
                        }
                        if ($condDesc['condition'] == '='){//handles equality of "NULL" values in the PHP generation.
                           $r .= $prefixNoCondition.'\'.('.$conditionValue.'===null ? \' IS \' : \' = \').\''.$this->_preparePHPValue($conditionValue,$property[1]);
                        } elseif ($condDesc['condition'] == '<>') {
                           $r .= $prefixNoCondition.'\'.('.$conditionValue.'===null ? \' IS NOT \' : \' <> \').\''.$this->_preparePHPValue($conditionValue,$property[1]);
                        } else {
                        	$r.=$prefix.$this->_preparePHPValue($conditionValue,$property[1]);
                        }
                        $firstCV = false;
                    }
                    $r .= ' ) ';
                }
        }
        //sub conditions
        foreach ($condition->group as $conditionDetail){
            if (!$first){
                $r .= ' '.$condition->kind.' ';
            }
            $r .= $this->_explainPHPSQLCondition ($conditionDetail, $fields, $ct);
            $first=false;
        }

        //adds parenthesis around the sql if needed (non empty)
        if (strlen (trim ($r)) > 0 && !$principal){
            $r = '('.$r.')';
        }
        return $r;
    }
    
    /**
    * prepare a string ready to be included in a PHP script
    * we assume that if the value is "NULL", all things has been take care of
    *   before the call of this method
    * The method generates something like (including quotes) '.some PHP code.'
    *   (we do break "simple quoted strings")
    * @todo supprimer et remplacer par le système de bind
    */
    public function _preparePHPValue ($value, $fieldType){
        switch (strtolower ($fieldType)){
            case 'int':
            case 'integer':
            case 'autoincrement':
            /*            if(is_numeric($value))
            $value=intval($value);
            else
            */
            $value= '\'.('.$value.' === null ? \'NULL\' : intval('.$value.')).\'';
            break;
            case 'double':
            case 'float':
            /*            if(is_numeric($value))
            $value=doubleval($value);
            else
            */
            $value= '\'.('.$value.' === null ? \'NULL\' : doubleval('.$value.')).\'';
            break;
            case 'numeric'://usefull for bigint and stuff
            case 'bigautoincrement':
            if(!is_numeric($value)){
                 $value='\'.('.$value.' === null ? \'NULL\' : (is_numeric ('.$value.') ? '.$value.' : intval('.$value.'))) .\'';
            }
            break;
            default:
            $value ='\'. $ct->quote ('.$value.').\'';
        }
        return $value;
    }    
}
?>