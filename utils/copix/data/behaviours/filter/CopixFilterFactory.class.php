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
		//Pour respecter l'ancienne syntaxe ou les filtres de modules étaient appelés avec un | dans le nom
		if (strpos ($pName, '|') !== false){
			$className = CopixSelectorFactory::purge ($pName);
			$toReturn = new $className ($pParams);
		}else{
			//ce doit être un filtre copix
			$className = CopixSelectorFactory::purge ($pName);
			$className = 'CopixFilter'.$pName;
			$toReturn =  new $className ($pParams);			
		}

		//On vérifie que c'est bien un ICopixfilter
		if ($toReturn instanceof ICopixFilter){
			return $toReturn;
		}
		throw new CopixException (_i18n ('copix:copixfilter.notimplement', $pName));
	}
	
	/**
	 * Création d'un filtre composite
	 * 
	 * @return ICopixFilter
	 * @throws CopixException si le filtre n'existe pas ou s'il ne respecte pas l'interface ICopixFilter
	 */
	public static function createComposite (){
		$args = func_get_args ();
		return new CopixCompositeFilter ($args);
	} 
}