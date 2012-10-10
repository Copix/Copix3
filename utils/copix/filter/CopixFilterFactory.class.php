<?php
/**
 * @package		copix
 * @subpackage	filter
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link 		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Interface pour les filtres Copix
 * @package copix
 * @subpackage filter
 */
interface ICopixFilter {
	/**
	 * Construction avec un tableau de paramtèrs
	 *
	 * @param array $pParams
	 */
	public function __construct ($pParams = array ());
	/**
	 * Application du filtre sur $pValue sans modifier la donnée.
	 *
	 * @param mixed $pValue la valeur sur laquelle appliquer le filtre
	 * @return mixed la valeur $pValue sur laquelle le filtre a été appliqué
	 */
	public function get ($pValue);
	
	/**
	 * Application du filtre sur $pValue et modification de $pValue en conséquence
	 *
	 * @param mixed $pValue la valeur sur laquelle appliquer le filtre
	 * @return mixed la valeur $pValue sur laquelle le filtre a été appliqué 
	 */
	public function update (& $pValue); 
}

/**
 * Fabrique de filtres
 * @package copix
 * @subpackage core
 */
class CopixFilterFactory {
	/**
	 * Création d'un filtre
	 * @param string $pName    le nom du filtre à créer (peut correspondre à module|classe pour des filtres personnels)
	 * @param array  $pParams  Un tableau d'options a passer au filtre
	 * @return ICopixFilter
	 * @throws CopixException si le filtre n'existe pas ou s'il ne respecte pas l'interface ICopixFilter
	 */
	public static  function create ($pName, $pParams = array ()){
		$className = 'CopixFilter'.$pName;
		if (class_exists ($className, (strpos ($pName, '|') === false && strpos ($pName, ':') === false))){
			return new $className ($pParams);
		}
		$toReturn = _class ($pName, array ($pParams));
		if ($toReturn instanceof ICopixFilter){
			return $toReturn;
		}
		throw new CopixException (_i18n ('copix:copixfilter.notimplement', $pName));
	}
}