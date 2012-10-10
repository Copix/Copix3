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
	public function process ($pParams, $pContent=null){
		$basePath = CopixUrl::getRequestedBaseUrl ();
		CopixHTMLHeader::addJSLink (_resource ('js/mootools/mootools.js'));
		if (isset ($pParams['plugin'])){
			if (! is_array ($pParams['plugin'])){
				$pParams['plugin'] = explode (';', $pParams['plugin']);
			}

			foreach ($pParams['plugin'] as $pluginName){
				if (!file_exists (str_replace ($basePath, './', $pluginPath = _resource ('js/mootools/plugins/'.$pluginName.'.js.php'))) &&
					!file_exists (str_replace ($basePath, './', $pluginPath = _resource ('js/mootools/plugins/'.$pluginName.'.js')))
				){
					throw new CopixException ('[Mootools] Plugin '.$pluginName.' not found in '.$pluginPath);
				}
				CopixHTMLHeader::addJSLink ($pluginPath);
			}
		}
	}
}
?>