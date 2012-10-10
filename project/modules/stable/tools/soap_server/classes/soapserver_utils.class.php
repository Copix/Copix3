<?php
/**
 * @package		tools
 * @subpackage	soap_server
 * @author		Croës Gérald
 * @copyright	2001-2009 CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe utilitaire pour les services web
 * @package tools
 * @subpackage soap_server
 */
class SoapServer_Utils {
	/**
	 * Récupération des classes disponibles dans les modules installés
	 *
	 * @return array
	 */
	public function findAvailableClasses (){
		$arReturn  = array();
		
		//Parcours des modules installés seulement
		foreach (CopixModule::getList(true) as $module) {
			$moduleInformations = CopixModule::getInformations ($module);
			$moduleClassesPath  = CopixModule::getPath ($module).COPIX_CLASSES_DIR;

			$moduleInformations->services = array ();
			foreach (CopixFile::search ('*.class.php', $moduleClassesPath, false) as $className){
				$moduleInformations->services[] = substr ($className, strlen ($moduleClassesPath), -10);
			}

			if (count ($moduleInformations->services )){
				$arReturn[] = $moduleInformations;
			}
		}
		return $arReturn;
	}
	
	/**
	 * Récupération des classes définies dans le fichier donné
	 *
	 * @param string $pFileName l'identifiant de la classe ou trouver les classes définies
	 */
	public function findClassesIn ($pClassId){
		//Les classes actuellement connues		 
		$arBefore = get_declared_classes ();
		//inclusion du fichier ou rechercher les classes
		_classInclude ($pClassId);
		//Les classes connues après inclusion du fichier
		$arAfter = get_declared_classes ();

		//On enlève du tableau des classes actuellement connues les classes 
		// connues avant l'inclusion du fichier ainsi que les classes prises en charge par 
		// l'autoload
		$arClass = array ();
		foreach (array_diff ($arAfter, $arBefore) as $className){
			if (! CopixAutoloader::canAutoLoad ($className)){
				$arClass[] = $className;				
			}
		}

		//Tri du tableau et retour des résutlats
		sort ($arClass);
		return $arClass;
	}
}