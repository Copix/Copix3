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
				$pluginPath = false;
				if (file_exists (CopixUrl::getResourcePath ('js/mootools/plugins/'.$pluginName.'.js'))){
					$pluginPath = _resource ('js/mootools/plugins/'.$pluginName.'.js');
				}
				if (file_exists (CopixUrl::getResourcePath ('js/mootools/plugins/'.$pluginName.'.js.php'))){
					$pluginPath = _resource ('js/mootools/plugins/'.$pluginName.'.js.php'); 
				}
				if ($pluginPath === false){
					throw new CopixException ('[Mootools] Plugin '.$pluginName.' not found in '.$pluginPath);
				}else{
					CopixHTMLHeader::addJSLink ($pluginPath);
				}

				if (file_exists (CopixUrl::getResourcePath ('js/mootools/css/'.$pluginName.'.css'))){
					CopixHtmlHeader::addCssLink (_resource ('js/mootools/css/'.$pluginName.'.css'));
				}
			}
		}
	}
}
?>