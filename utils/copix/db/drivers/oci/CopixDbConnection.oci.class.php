<?php
/**
* @package		copix
* @subpackage	db
* @author		Croès Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
require_once (COPIX_PATH.'db/drivers/oci/CopixDBOciResultSetIterator.class.php');

/**
 * Classe de connexion à oracle en utilisant drivers OCI
 * 
 * @package		copix
 * @subpackage	db
 */
class CopixDBConnectionOCI extends CopixDBConnection {
	/**
	 * Identifiant de connexion à la base Oracle.
	 */
	private $_ct = false;
	
	/**
	 * Mode de requêtage (autocommit ou non)
	 * @var const
	 */
	private $_autoCommitMode = null;
	
	/**
	 * Constante pour demander l'autocommit
	 */
	const OCI_AUTO_COMMIT = 1;
	
	/**
	 * Constante pour ne pas utiliser l'autocommit 
	 */
	const OCI_NO_AUTO_COMMIT = 0;

   /**
    * Construction de la connexion
    * @todo prendre en charge les options spécifiques au driver
    */
   public function __construct ($pProfil){
		parent::__construct ($pProfil);
		$this->_autoCommitMode = self::OCI_AUTO_COMMIT; 

		$parts = $this->_profil->getConnectionStringParts ();
		$funcName = $this->_profil->getOption (CopixDBProfile::PERSISTENT) ? 'oci_connect' : 'oci_pconnect';
		if (($this->_ct = $funcName ($this->_profil->getUser (), $this->_profil->getPassword (), $parts['dbname'], isset ($parts['charset']) ? $parts['charset'] : null)) === false){
    		throw new CopixDBException ('Impossible de se connecter');
    	}
   }

   /**
    * Analyse la requète pour qu'elle passe sans encombre dans le driver PDO_OCI
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
    * 
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
     * @return	array	la liste des tables
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
     * @param	string	$pTableName	Le nom de la table dont on souhaite obtenir la description
     */
    public function getFieldList ($pTableName) {
    	$toReturn = array ();

        $arType = array('LONG'=>'float','NUMBER'=>'float', 'CHAR'=>'varchar', 'VARCHAR2'=>'varchar', 'NVARCHAR2'=>'varchar', 'NCHAR'=>'varchar', 'CLOC'=>'varchar', 'NCLOB'=>'varchar', 'BLOB'=>'blob', 'DATE'=>'datetime');
 
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
			if (isset($arType[strtoupper($val->col_type)])) {
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
     * Exécution d'une requête de base de données  en utilisant les iterateurs pour les retours
     * 
     * @param	string 	$pQueryString	la requête à exécuter
     * @param 	string	$pParameters	les paramètres à donner à la requête
     * @param 	int		$pOffset		la ligne à partir de laquelle on veut récupérer les donénes
     * @param	int 	$pCount 		le nombre d'enregistrements que l'on souhaite récupérer à partir de l'offset
     * @return  CopixDBOciResultSetIterator / int
     */
    public function iDoQuery ($pQueryString, $pParams = array (), $pOffset = null, $pCount = null){
   		CopixLog::log ($pQueryString.var_export ($pParams, true), 'query', CopixLog::INFORMATION);

   		$resultsOfQueryParsing = $this->_parseQuery ($pQueryString, $pParams, $pOffset, $pCount);
        $pQueryString = $resultsOfQueryParsing['query'];
        
    	//Préparation de la requête
    	$stmt = ociparse ($this->_ct, $pQueryString);
    	if ($stmt === false){
    		throw new CopixDBException ($pQueryString);
    	}

    	//On analyse les paramètres
        $arVariablesName = array ();
        $arVariables = array ();
        foreach ($pParams as $name=>$param){
            $variableName = substr ($name, 1);
        	if (! is_array ($param)){
               $$variableName = $param;
               if (!OCI_Bind_By_Name ($stmt, $name, $$variableName, -1)){
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
           	   	$$variableName = oci_new_cursor ($this->_ct);
           	   }
           	   
           	   if (($arVariables[$variableName]['type'] === CopixDBQueryParam::DB_LOB) 
           	        || $arVariables[$variableName]['type'] === CopixDBQueryParam::DB_BLOB
           	        || $arVariables[$variableName]['type'] === CopixDBQueryParam::DB_CLOB){
		       	   	$$variableName = oci_new_descriptor ($this->_ct, OCI_D_LOB);
           	   }           	   

           	   if (! OCI_Bind_By_Name ($stmt, $name, $$variableName, $arVariables[$variableName]['maxlength'], $this->_convertProcedureParam ($arVariables[$variableName]['type']))){
                  oci_free_statement ($stmt);
                  throw new CopixDBException ("Bind ['$name'] - [".$$variableName."] taille [".$arVariables[$variableName]['maxlength']."] type [".$this->_convertProcedureParam ($arVariables[$variableName]['type'])."]");
               }
          	}
        }

        //on exécute la requête
        if (! oci_execute($stmt, $this->_getCommitMode ())){
            $statementErrors = oci_error ($stmt);
            oci_free_statement ($stmt);
        	throw new CopixDBException ($pQueryString.' - '.var_export ($statementErrors, true).' - '.var_export ($arVariables, true));
        }

        //retourne les résultats.
		if ($resultsOfQueryParsing['isSelect']){
		    $results = new CopixDBOCIResultSetIterator ($stmt, null, $pQueryString);
		}else{
			$results = oci_num_rows ($stmt);
			oci_free_statement ($stmt);
		}
        return $results;
    }
    
    /**
     * Exécution d'une requête de base de données en utilisant
     * 
     * @param	string 	$pQueryString	la requête à exécuter
     * @param 	string	$pParameters	les paramètres à donner à la requête
     * @param 	int		$pOffset		la ligne à partir de laquelle on veut récupérer les donénes
     * @param	int 	$pCount 		le nombre d'enregistrements que l'on souhaite récupérer à partir de l'offset
     * @return  array
     */
    public function doQuery ($pQueryString, $pParams = array (), $pOffset = null, $pCount = null){
   		CopixLog::log ($pQueryString.var_export ($pParams, true), 'query', CopixLog::INFORMATION);

   		$resultsOfQueryParsing = $this->_parseQuery ($pQueryString, $pParams, $pOffset, $pCount);
        $pQueryString = $resultsOfQueryParsing['query'];

    	//Préparation de la requête
    	$stmt = ociparse ($this->_ct, $pQueryString);
    	if ($stmt === false){
    		throw new CopixDBException ($pQueryString);
    	}

    	//On analyse les paramètres
        $arVariablesName = array ();
        $arVariables = array ();
        foreach ($pParams as $name=>$param){
            $variableName = substr ($name, 1);
        	if (! is_array ($param)){
               $$variableName = $param;
               if (!OCI_Bind_By_Name ($stmt, $name, $$variableName, -1)){
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
           	   	$$variableName = oci_new_cursor ($this->_ct);
           	   }
               if (! OCI_Bind_By_Name ($stmt, $name, $$variableName, $arVariables[$variableName]['maxlength'], $this->_convertProcedureParam ($arVariables[$variableName]['type']))){
                  oci_free_statement ($stmt);
                  throw new CopixDBException ("Bind ['$name'] - [".$$variableName."] taille [".$arVariables[$variableName]['maxlength']."] type [".$this->_convertProcedureParam ($arVariables[$variableName]['type'])."]");
               }
          	}
        }

        //on exécute la requête
        if (! oci_execute($stmt, OCI_DEFAULT)){
            $statementErrors = oci_error ($stmt);
            oci_free_statement ($stmt);
        	throw new CopixDBException ($pQueryString.' - '.var_export ($statementErrors, true).' - '.var_export ($arVariables, true));
        }
        
        //Mise à jour des champs de type lob
        foreach ($arVariables as $name=>$value){
        	//Si c'est un curseur 
        	if (($value['type'] === CopixDBQueryParam::DB_LOB) 
        		|| ($value['type'] === CopixDBQueryParam::DB_BLOB)
        		|| ($value['type'] === CopixDBQueryParam::DB_CLOB)){
        			$$name->save ($value);
        		}
        }
       
        //retourne les résultats.
		if ($resultsOfQueryParsing['isSelect']){
			$results = array ();
	        while ($o = oci_fetch_array ($stmt, OCI_ASSOC+OCI_RETURN_LOBS+OCI_RETURN_NULLS)){
	        	$results[] = $this->_getCases ($o, $pQueryString);
	        }
		}else{
			$results = oci_num_rows ($stmt);
		}
		
		//On commit si le mode est autocommit 
		if ($this->_autoCommitMode == self::OCI_AUTO_COMMIT){
			oci_commit ($this->_ct);
		}
		
		//Libération des lobs
        foreach ($arVariables as $name=>$value){
        	//Si c'est un curseur 
        	if (($value['type'] === CopixDBQueryParam::DB_LOB) 
        		|| ($value['type'] === CopixDBQueryParam::DB_BLOB)
        		|| ($value['type'] === CopixDBQueryParam::DB_CLOB)){
        			oci_free_descriptor ($$name);
        		}
        }

        oci_free_statement ($stmt);
        return $results;
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

    /**
     * Récupère le mode à transmettre au driver OCI
     * @return const
     */
    private function _getCommitMode (){
    	if ($this->_autoCommitMode == self::OCI_AUTO_COMMIT){
    		return OCI_COMMIT_ON_SUCCESS;
    	}
    	return OCI_DEFAULT;
    }

    /**
     * Lance une procédure stockées sur la connextion courante
     * @param string $pProcedure la procédure a lancer
     * @param array $pParams un tableau de paramètre à donner à la procédure
     *  le tableau est de la forme $pParams['nom'] = array ('type'=>, 'length'), 'in'=>, ''
     * @return array un tableau de résultat avec array['results'] = résultats, 
     *    array['params']['nomParam'] = valeur
     */   
    public function iDoProcedure ($pProcedure, $pParams){
   		CopixLog::log ($pProcedure.var_export ($pParams, true), 'query', CopixLog::INFORMATION);
   		
   		$this->doQuery ("ALTER SESSION SET NLS_NUMERIC_CHARACTERS='.,'");
   		$this->doQuery ("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY'");
   		
    	//Préparation de la requête
    	$stmt = ociparse ($this->_ct, $pProcedure);
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
           	   	$$variableName = oci_new_cursor ($this->_ct);
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
            oci_rollback ($this->_ct);
        	throw new CopixDBException ('[CopixDB] Impossible d\'exécuter la procédure '.$pProcedure.' - '.var_dump ($statementErrors).' avec les variables '.var_dump ($arVariables));
        }

        //analyse des résultats
        foreach ($arVariables as $name=>$value){
        	//Si c'est un curseur 
        	if ($value['type'] === CopixDBQueryParam::DB_CURSOR){
        		if (!@ociexecute ($$name)){
        		    oci_free_statement ($$name);
                    oci_free_statement ($stmt);
                    oci_rollback ($this->_ct);
        			throw new CopixDBException ("Impossible de récupèrer l'ensemble de résultat de la variable $name");;
        		}
        		$toReturn[':'.$name] = new CopixDBOCIResultSetIterator($$name, $stmt, null);
        	}else{
               $toReturn[':'.$name] = $$name;
        	}
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
   		
   		$this->doQuery ("ALTER SESSION SET NLS_NUMERIC_CHARACTERS='.,'");
   		$this->doQuery ("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY'");
   		
    	//Préparation de la requête
    	$stmt = ociparse ($this->_ct, $pProcedure);
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
           	   	$$variableName = oci_new_cursor ($this->_ct);
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
            oci_rollback ($this->_ct);
        	throw new CopixDBException ('[CopixDB] Impossible d\'exécuter la procédure '.$pProcedure.' - '.var_dump ($statementErrors).' avec les variables '.var_dump ($arVariables));
        }

        //analyse des résultats
        foreach ($arVariables as $name=>$value){
        	//Si c'est un curseur 
        	if ($value['type'] === CopixDBQueryParam::DB_CURSOR){
        		if (!@ociexecute ($$name)){
        		    oci_free_statement ($$name);
                    oci_free_statement ($stmt);
                    oci_rollback ($this->_ct);
        			throw new CopixDBException ("Impossible de récupèrer l'ensemble de résultat de la variable $name");;
        		}
        		$toReturn[':'.$name] = array ();
        		while ($r = oci_fetch_array ($$name, OCI_ASSOC+OCI_RETURN_LOBS+OCI_RETURN_NULLS)){
                   $toReturn[':'.$name][] = $this->_getCases ($r);        			
        		}
        		oci_free_statement ($$name);
        	}else{
               $toReturn[':'.$name] = $$name;
        	}
        }
        
        oci_commit($this->_ct);
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
    		case CopixDBQueryParam::DB_BLOB : return OCI_B_BLOB;
    		case CopixDBQueryParam::DB_CLOB : return OCI_B_CLOB;
    		case CopixDBQueryParam::DB_LOB : return OCI_B_BLOB;
    		default: return null;
    		
    	}
    }

    /**
     * Indique si le driver est disponible
     * @return bool
     */
    public static function isAvailable (){
    	if (!function_exists ('oci_pconnect')){
    		return false;
    	}
    	return true;
    }

    /**
     * Valide une transaction en cours sur la connection
     */
    public function commit (){
		if (! oci_commit ($this->_ct)){
			throw new CopixDBException ('Impossible de commiter la transaction');
		}
    }

    /**
     * Annule une transaction sur la connection 
     */
    public function rollback (){
    	if (! oci_rollback ($this->_ct)){
    		throw new CopixDBException ('Impossible de rollback la transaction');
    	}
    }
    
    /**
     * Demarre une transaction sur la connection donnée
     */
    public function begin (){
    	$this->_autoCommitMode = self::OCI_NO_AUTO_COMMIT;
    }

    /**
     * retourne le dernier identifiant généré (à partir d'une séquence)
     * @return int 
     */
    public function lastId ($pFromSequence = null){
		$result     = $this->doQuery ('select '.$pFromSequence.'.nextVal from dual');
		return $result[0]->nextVal;
    }
}
?>