<?php
/**
 * @package 	copix
 * @subpackage 	smarty_plugins
 * @author		Salleyron Julien
 * @copyright	2000-2006 CopixTeam
 * @link			http://www.copix.org
 * @license 		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Voir selectcomplex.templatetag.php
 */
function smarty_block_selectcomplexoption ($pParams, $pContent, &$me, $first) {
	if (!$first) {
		$value = (isset ($pParams ['value'])) ? $pParams ['value'] : '';
		$alternative  = (isset ($pParams ['alternative'])) ? '{selectcomplexoptionalternative}'.$pParams ['value'].'{/selectcomplexoptionalternative}' : '';
		
		
		$pContent = '{selectcomplexoption}{selectcomplexoptionvalue}'.
		            $value.
		            '{/selectcomplexoptionvalue}'.
		            $alternative.
		            $pContent.
		            '{/selectcomplexoption}';
		
		if (isset ($pParams['assign'])){
			$me->assign ($pParams['assign'], $pContent);
		}
		return $pContent;
	}
}