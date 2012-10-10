<?php
/**
 * @package		copix
 * @subpackage	taglib
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link			http://www.copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe qui permet d'inclure les librairies Mootools facilement dans vos templates
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagMootools extends CopixTemplateTag {
	/**
	 * Déclare la fonction getHTTPObject () dans l'en tête HTML.
	 * @param mixed $pParams aucun paramètre attendu ici.
	 */
	public function process ($pContent=null){
		$pParams = $this->getParams ();

		$pluginList = null;

		// Il est possible de demander la liste de plugins soit 
		// avec le paramètre plugins, soit avec le paramètre plugin
		if (isset ($pParams['plugins'])){
			$pParams['plugin'] = $pParams['plugins'];			
		}

		//On regarde si l'on souhaite mettre en place le fichier de compatibilité
		if (!isset ($pParams['compatibility'])){
			$pParams['compatibility'] = CopixConfig::instance ()->mootools_compatibility_version;
		}
		
		//Si on a demandé des plugins, on les liste
		if (isset ($pParams['plugin'])){
			$pluginList = $pParams['plugin'];
			if (! is_array ($pluginList)) {
				$pluginList = explode (';', $pluginList);
			}
		}

		CopixHTMLHeader::addJSFramework($pluginList, $pParams['compatibility']);
	}
}