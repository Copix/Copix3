<?php
/**
 * @package		copix
 * @subpackage	taglib
 * @author		Goulven Champenois
 * @copyright	CopixTeam
 * @link			http://www.copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe qui permet d'inclure les librairies Mootools facilement dans vos templates
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagjQuery extends CopixTemplateTag {
	public function process ($pContent = null) {
		$pParams = _Ppo ($this->getParams ());
		$pluginList = null;
		//Si on a demandé des plugins, on les liste
		if ($pParams->plugin){
			$pluginList = $pParams->plugin;
			if (! is_array ($pluginList)) {
				$pluginList = explode (';', $pluginList);
			}
		}
		CopixHTMLHeader::addJQuery ($pluginList, $pParams->version);
	}
}