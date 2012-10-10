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
require_once (COPIX_PATH.'db/drivers/oci/CopixDBOciResultSetIterator.class.php');

/**
 * Classe de connexion à oracle en utilisant PDO & les drivers OCI
 * @package		copix
 * @subpackage	db
 */
class CopixDBConnectionPDO_OCI extends CopixDBPDOConnection {
   /**
    * Construction de l'objet PDO
    * @todo prendre en charge les options spécifiques au driver
    */
   public function __construct ($pProfil){
      parent::__construct ($pProfil);
   }

   /**
    * Analyse la requète pour qu'elle passe sans encombre dans le driver MSSQL
    */
   protected function _parseQuery ($pQueryString, $pParameters, $pOffset, $pCount){
   	  $toReturn = parent::_parseQuery ($pQueryString, $pParameters, $pOffset, $pCount);

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
        
        $arType = array('LONG'=>'float','NUMBER'=>'float','CHAR'=>'varchar','VARCHAR2'=>'varchar','NVARCHAR2'=>'varchar','NCHAR'=>'varchar','CLOC'=>'varchar','NCLOB'=>'varchar','BLOB'=>'varchar','DATE'=>'datetime');
 
        $query = "SELECT   a.column_name AS name, " .
                 "         decode (a.nullable, 'Y', 0, 'N', 1) AS not_null, " .
                 "         a.data_type AS col_type, " .
                 "         a.data_length AS col_size, " .
                 "         a.data_default AS default_val " .
                 "FROM     user_tab_columns a ".
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
   		CopixLog::log ($pProcedure.var_export ($pParams, true), 'query', CopixLog::INFORMATION);

    	//Connexion
    	$parts = $this->_profil->getConnectionStringParts ();

    	$ct = oci_connect ($this->_profil->getUser (), $this->_profil->getPassword (), $parts['dbname']);

    	if ($ct === false){
    		throw new CopixDBException ('Impossible de se connecter');
    	}
   		CopixLog::log ($pProcedure.var_export ($pParams, true), 'query', CopixLog::INFORMATION);

   		$stmt = ociparse ($ct, "ALTER SESSION SET NLS_NUMERIC_CHARACTERS='.,'");
        ociexecute ($stmt);	   		

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
               if (!oci_bind_by_name ($stmt, $name, $$variableName, -1)){
                  throw new CopixDBException ("Bind ['$name'] - [".$$variableName."] taille [".$arVariables[$variableName]['maxlength']."] type [".$this->_convertProcedureParam ($arVariables[$variableName]['type'])."]");
               }
               $arVariables[$variableName]['type'] = 'AUTO';        		
               $arVariables[$variableName]['value'] = $param;        		
        	}else{
        		if (!isset ($$variableName)){
        	       $$variableName = isset ($param['value']) ? $param['value'] : null;        			
        		}
        	   $arVariables[$variableName] = $param;

        	   if (! isset ($arVariables[$variableName]['type'])){
        	   	$arVariables[$variableName]['type'] = CopixDBQueryParam::DB_AUTO; 
        	   }
        	   if (! isset ($arVariables[$variableName]['maxlength'])){
        	   	$arVariables[$variableName]['maxlength'] = -1;
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
    	if (!class_exists ('PDO')){
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
		CopixLog::log ($resultsOfQueryParsing['query'].var_export ($pParameters, true), "query", CopixLog::INFORMATION);

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
   	  if ($pQuery !== null){
   	  	  if (($pos = strrpos (strtoupper ($pQuery), ' FROM ')) !== false){
   	  	  	$query = substr ($pQuery, 0, $pos);
   	  	  }else{
   	  	  	$query = $pQuery;
   	  	  }
   	  	  
	      $final = array ();
	      foreach ($pRow as $key=>$name){
	         if (($pos = strrpos (strtoupper ($query), strtoupper ($key)))===false) {
	            $final[$key] = $name;
	         }else{
	            $final[substr ($query, $pos, strlen ($key))] = $name;
	         }
	      }
	      return (object) $final;
   	  }
   	  return (object) $pRow;
   }
}
?>