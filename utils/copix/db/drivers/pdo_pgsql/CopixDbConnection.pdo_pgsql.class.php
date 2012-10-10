<?php
/**
 * @package		copix
 * @author		Croës Gérald
 * @copyright	2001-2006 CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de connexion à une base Postgres en utilisant les drivers PDO
 * @package		copix
 * @subpackage	db
 */
class CopixDBConnectionPDO_PgSQL extends CopixDBPDOConnection {
	/**
	 * Analyse la requête pour qu'elle passe sans encombre dans le driver MySQL
	 * @param	string	$pQueryString	la requête à lancer
	 * @param 	array	$pParameters	les paramètres de la requête
	 * @param 	int		$pOffset		l'offset à partir duquel on veut récupérer les résultats
	 * @param 	int 	$pCount			le nombre de lignes que l'on souhaites récupérer depuis cette requête	
	 */
	protected function _parseQuery ($pQueryString, $pParameters = array (), $pOffset = null, $pCount = null){
		$toReturn = parent::_parseQuery ($pQueryString, $pParameters, $pOffset, $pCount);
		//only for select query
		if ($toReturn['isSelect'] && ($pOffset !== null || $pCount !== null)){
			$pos = stripos($toReturn['query'], "select");

			if ($pCount === null){
				$pCount = $this->_getMaxCount ();
			}

			$pOffset = intval ($pOffset);
			$pCount  = intval ($pCount);            
			
			$toReturn['query'] = $toReturn['query']." LIMIT $pCount OFFSET $pOffset";;
			$toReturn['offset'] = true;
			$toReturn['count']  = true;
		}

		return $toReturn;
	}
  
	/**
	 * Retourne la liste des tables (en minuscule) connues de la base (en fonction de l'utilisateur)
	 * @return   array	liste des noms de table
	 */
	function getTableList (){
		$results   = $this->doQuery ("SELECT tablename FROM pg_tables WHERE tablename NOT LIKE 'pg_%' AND tablename NOT LIKE 'sql_%' ORDER BY tablename");

		if (count($results)==0) {
			return array();
		}

		$fieldName = array_keys (get_object_vars($results[0]));
		$fieldName = $fieldName[0];

		$toReturn = array ();

		foreach ($results as $table){
			$toReturn[] = strtolower ($table->$fieldName);
		}
		return $toReturn;
	}

	/**
	 * récupère la liste des champs pour une table nommée
	 * @param		string	$pTableName	le nom de la table dont on veut récupérer les champs
	 * @return	array	$tab[NomDuChamp] = obj avec prop (tye, length, lengthVar, notnull)
	 */
	public function getFieldList ($pTableName){

		$results = array ();
		$arIdx = array ();

		// Requête de récupération des Index
		$sql = "SELECT c2.relname AS indname, i.indisprimary, i.indisunique, i.indisclustered,
			pg_catalog.pg_get_indexdef(i.indexrelid, 0, true) AS inddef
			FROM pg_catalog.pg_class c, pg_catalog.pg_class c2, pg_catalog.pg_index i
			WHERE c.relname = '{$pTableName}' AND pg_catalog.pg_table_is_visible(c.oid) 
			AND c.oid = i.indrelid AND i.indexrelid = c2.oid
		";
		$result = $this->doQuery ($sql);
		foreach ($result as $key => $val) {
			if(preg_match ('/btree \((.*?)\)/', $val->inddef, $matches)){
				$arIdx[] = $matches[1];
			}
		}

		$sql_get_fields = "SELECT
        a.attname as Field, t.typname as type, a.attlen as length, a.atttypmod,
        case when a.attnotnull  then 1 else 0 end as notnull,
        a.atthasdef,
        (SELECT adsrc FROM pg_attrdef adef WHERE a.attrelid=adef.adrelid AND a.attnum=adef.adnum) AS adsrc
        FROM
            pg_attribute a,
            pg_class c,
            pg_type t
        WHERE
          c.relname = '{$pTableName}' AND a.attnum > 0 AND a.attrelid = c.oid AND a.atttypid = t.oid
        ORDER BY a.attnum";
		$result = $this->doQuery ($sql_get_fields);

		$toReturn=array();

		foreach ($result as $key => $val) {
			$fieldDescription = new CopixDBFieldDescription ($val->field);
			$fieldDescription->notnull = (bool) $val->notnull;
			$fieldDescription->type = preg_replace ('/(\D*)\d*/','\\1',$val->type);

			if (in_array($val->field,$arIdx)) {
				$fieldDescription->pk = true;
			}

			if ($val->type == 'text') {
				$fieldDescription->type = 'string';
			}
			
			if ($val->type == 'timestamp') {
				$fieldDescription->type = 'datetime';
			}
			
			// if(preg_match('/nextval\(\'(.*?)\.'.$pTableName.'_'.$val->field.'_seq\'::regclass\)/', $val->adsrc)){
			if(preg_match ('/'.$pTableName.'_'.$val->field.'_seq/', $val->adsrc)){
				$fieldDescription->type = 'autoincrement';
			} else {
				$fieldDescription->auto = false;
			}

			if($val->length < 0) {
				$fieldDescription->length = '';
			} else {
				$fieldDescription->length = $val->length;
			}

			$toReturn[$val->field] = $fieldDescription;
		}

		return $toReturn;
	}

	/**
	 * Indique si le driver est disponible
	 * @return bool
	 */
	public static function isAvailable (){
		if (!class_exists ('PDO')){
			return false;
		}
		return in_array ('pgsql', PDO::getAvailableDrivers ());
	}



}
?>