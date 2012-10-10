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

		// Récupère les n° des colonnes des champs de la clef primaire
		$sql = "SELECT i.indkey FROM pg_catalog.pg_class c, pg_catalog.pg_index i WHERE c.oid = i.indrelid AND i.indisprimary AND c.relname=:relname";
		$result = $this->doQuery ($sql, array(':relname' => $pTableName));
		$pkAttrIndex = array();
		if($result && count($result) == 1) {
			foreach(split(' ', $result[0]->indkey) as $index) {
				$pkAttrIndex[] = (int)$index; 
			}
		}

		$sql_get_fields = "SELECT
        a.attname as Field, a.attnum as num, t.typname as type, a.attlen as length, a.atttypmod,
        case when a.attnotnull  then 1 else 0 end as notnull,
        a.atthasdef,
        pg_get_expr(d.adbin, c.oid) as adsrc
        FROM
            pg_attribute a
            join pg_class c on (c.oid = a.attrelid)
            join pg_type t on (t.oid = a.atttypid)
            left join pg_attrdef d on (d.adrelid = c.oid and d.adnum = a.attnum)
        WHERE
          c.relname = '{$pTableName}' AND a.attnum > 0
        ORDER BY a.attnum";
		$result = $this->doQuery ($sql_get_fields);

		$toReturn=array();

		foreach ($result as $key => $val) {
			$fieldDescription = new CopixDBFieldDescription ($val->field);
			$fieldDescription->notnull = (bool) $val->notnull;
			$fieldDescription->type = preg_replace ('/(\D*)\d*/','\\1',$val->type);
			$fieldDescription->pk = in_array($val->num, $pkAttrIndex);

			if ($val->type == 'text') {
					$fieldDescription->type = 'string';
			}
			
			if ($val->type == 'timestamp') {
					$fieldDescription->type = 'datetime';
			}
			
			if($fieldDescription->pk && $val->atthasdef && preg_match("/nextval\('([^']+)'(::regclass)?\)/i", $val->adsrc, $parts)) {
				$fieldDescription->type = 'autoincrement';
				$fieldDescription->auto = true;
				$fieldDescription->sequence = $parts[1];
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