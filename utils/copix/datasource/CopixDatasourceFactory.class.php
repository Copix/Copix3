<?php
/**
 * @package		copix
 * @subpackage	datasource
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Interface pour les sources de données
 * 
 * @package		copix
 * @subpackage	datasource
 */
interface ICopixDataSource {
	/**
	 * Constructeur
	 * 
	 * @params array $pParams Paramètres
	 */
	public function __construct ($pParams);

	/**
	 * Ajoute une condition
	 *
	 * @param string $pField Nom du champ du dao sur lequel on ajoute la condition
	 * @param string $pCond Condition à appliquer (=, !=, <>, <, > like, ...)
	 * @param mixed $pValue Valeur de recherche (inutile de quotter les chaines)
	 * @return CopixDAOSearchparams
	 */
	public function addCondition ($pField, $pCond, $pValue);
	
	/**
	 * Effectue une recherche
	 */
	public function find ();
	
	/**
	 * Sauvegarde le record $pRecord
	 *
	 * @param ICopixDAORecord $pRecord
	 */
	public function insert ($pRecord);
	
	/**
	 * Sauvegarde les modifications apportées sur $pRecord
	 *
	 * @param ICopixDAORecord $pRecord
	 */
	public function update ($pRecord);
	
	/**
	 * Retourne un enregistrement selon $pId
	 *
	 * @param mixed pId
	 * @return ICopixDAORecord
	 */
	public function get ();
}

/**
 * Exception pour les datasource
 * 
 * @package		copix
 * @subpackage	datasource
 */
class CopixDatasourceException extends CopixException {	
}

/**
 * Factory pour les sources de données
 * 
 * @package		copix
 * @subpackage	datasource
 */
class CopixDatasourceFactory {	
	/**
	 * Retourne une source de données
	 * 
	 * @param string $pType Type de source de données (exemple : dao)
	 * @param array $pParams Paramètres pour la création du datasource
	 * @return mixed
	 */
	public static function get ($pType, $pParams) {
		switch ($pType) {
			case 'dao':
				return new CopixDaoDatasource ($pParams);

			default:
				CopixClassesFactory::fileInclude ($pType);
				$arDatasource = explode ('|', $pType);
				return new $arDatasource[1] ($pParams);
		}
	}
}