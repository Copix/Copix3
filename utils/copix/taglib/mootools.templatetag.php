<?php
/**
 * @package copix
 * @subpackage taglib
 * @author Gérald Croës
 * @copyright CopixTeam
 * @link http://www.copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Tag qui permet d'inclure les librairies Mootools facilement dans vos templates
 *
 * @package copix
 * @subpackage taglib
 */
class TemplateTagMootools extends CopixTemplateTag {
	/**
	 * Ajoute Mootools
	 * 
	 * @param array $pParams Paramètres : array ('plugins' => array ('myPlugin'), 'plugin' => 'myPlugin;myOtherPLugin')
	 * @param mixed $pContent Contenu
	 */
	public function process ($pParams, $pContent = null) {
		$pluginList = null;
		
		// Charge les plugins demandés
		if (isset ($pParams['plugins'])) {
			$pParams['plugin'] = $pParams['plugins'];			
		}
		if (isset ($pParams['plugin'])) {
			$pluginList = $pParams['plugin'];
			if (!is_array ($pluginList)) {
				$pluginList = explode (';', $pluginList);
			}
		}
		
		CopixHTMLHeader::addJSFramework ($pluginList);
	}
}