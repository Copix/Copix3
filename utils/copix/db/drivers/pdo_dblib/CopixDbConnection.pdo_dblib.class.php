<?php
/**
* @package   copix
* @subpackage db
* @author   David Derigent
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package		copix
* @subpackage 	db
*/
class CopixDBConnectionPDO_Dblib extends CopixDBPDOConnection {
    protected $scriptComment = '/^\s*#/';
    protected $scriptEndOfQuery = '/\s*;\s*$/i';
  
   /**
    * Analyse la requête pour qu'elle passe sans encombre dans le driver MSSQL
    */
   protected function _parseQuery ($pQueryString, $pParameters, $pOffset, $pCount){
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
		*		 - lorsque vous spécifiez un offset vous êtes obligés de déclarer une clause order dans votre requête 
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
							throw new CopixDBConnectionException ('ERREUR REQUETE ( '.$pSQL.') : La clause "order" est manquante dans la requête MSSQL. L\'utilisation d\'un offset dans une requête nécessite obligatoirement l\'ajout d\'un "order by".');
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
    */
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
   */
   public function getFieldList ($tableName){
      $results = array ();
		
		/*
      $sql_get_fields  = 'SELECT DISTINCT ';
      $sql_get_fields .= "syscolumns.name as Field, systypes.name as type, syscolumns.length as length, syscolumns.isnullable as isnull";
      $sql_get_fields .= " FROM sysobjects,syscolumns,systypes WHERE ";
      $sql_get_fields .= " sysobjects.id = syscolumns.id AND syscolumns.xtype=systypes.xusertype AND ";
      $sql_get_fields .= " syscolumns.xtype = systypes.xtype AND sysobjects.name='" . $tableName ."'";
      	*/
      	
      $sql_get_fields  = "SELECT DISTINCT syscolumns.name as Field, systypes.name as type, syscolumns.length as length, "; 
      $sql_get_fields .= "		syscolumns.isnullable as isnull, sysobjects2.name fktable, syscolumns2.name as fkfieldname ";
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
          $p_result_line->type      = $result_line->type;
          $p_result_line->primary  = 0;
          $p_result_line->isAutoIncrement = 0;

          if( ereg("identity" , $p_result_line->type ) )  {
             $p_result_line->isAutoIncrement = 1;
          }

          $p_result_line->length    = $result_line->length;
          $p_result_line->notnull   = (!$result_line->isnull);
          
          $p_result_line->fktable   	= $result_line->fktable ;
          $p_result_line->fkfieldname	= $result_line->fkfieldname ;

          $results[$result_line->Field] = $p_result_line ;
      }

      //$rs = $this->dbQuery("exec sp_pkeys '".$tableName."'");
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
   * retourne la liste des tables
   * @return   array    $tab[] = $nomDeTable
   */
   function getTableList (){
      $results = array ();
      $lines = $this->doQuery ('select name from sysobjects where type = \'U\' order by name');
      foreach ($lines as $line){
         $results[] = $line->name;
      }
      return $results;
   }


    /**
     * Valide une transaction en cours sur la connection
     */
    public function commit (){
    	$this->doQuery ("COMMIT TRAN");
    }
    
    /**
     * Annule une transcation sur la connection 
     */
    public function rollback (){
    	$this->doQuery ("ROLLBACK TRAN");
    }
    
    /**
     * Demarre une transaction sur la connection donnée
     */
    public function begin (){
    	$this->doQuery ("BEGIN TRAN");
    } 
    // DDT ajout pour gérér l'auto-incrément sans passer par les entities
    /**
     * renvoie le plus grand identifiant d'une colonne (la clef primaire d'une table généralement
     * @param string fieldname nom de la colonne dont on veut récupérer l'enregistrement max
     * @param string $tableName nom de la table
     * @return int enregistrement max
     */
/* 
    function lastId ($fieldName, $tableName){
    	$rs = $this->doQuery ('SELECT MAX('.$fieldName.') as ID FROM '.$tableName);
      if (($rs !== null) && isset($rs[0])){
         $r = $rs[0];
         return $r->ID;
      }
      return 0;
   }
*/   
   /**
    * Indique si le driver est disponible
    * @return bool
    */
   public static function isAvailable (){
   	if (!class_exists ('PDO')){
   		return false;
   	}
   	return in_array ('dblib', PDO::getAvailableDrivers ());
   }
             
}
?>