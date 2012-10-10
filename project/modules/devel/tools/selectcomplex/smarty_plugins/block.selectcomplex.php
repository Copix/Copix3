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
function smarty_block_selectcomplex ($pParams, $pContent, &$me, $first) {
	if (!$first) {
		$pParams ['options']      = (isset ($pParams ['options']))      ? $pParams ['options']       : array ();
		$pParams ['alternatives'] = (isset ($pParams ['alternatives'])) ? $pParams ['alternatives'] : array ();
		$pParams ['selectedView'] = (isset ($pParams ['selectedView'])) ? $pParams ['selectedView']  : array ();
		
		
		if ($pContent = trim ($pContent)) {
			
			while (($posD = strpos ($pContent, '{selectcomplexoption}'))  !== false &&
			       ($posF = strpos ($pContent, '{/selectcomplexoption}')) !== false) {
				
				//Récuperation de la valeur
				$Raw = substr($pContent, ($posD+21), ($posF-$posD-21));
				$posDValue = strpos ($Raw, '{selectcomplexoptionvalue}');
				$posFValue = strpos ($Raw, '{/selectcomplexoptionvalue}');
				$value = substr($Raw, ($posDValue+26), ($posFValue-$posDValue-26));
				$Raw = substr($Raw, $posFValue+27);
				
				$posDValue = strpos ($Raw, '{selectcomplexoptionvalue}');
				
				//Récupère l'aternative si il y en a une
				$posDValue = strpos ($Raw, '{selectcomplexoptionalternative}');
				$posFValue = strpos ($Raw, '{/selectcomplexoptionalternative}');
				if ($posDValue !== false && $posFValue !== false) {
					$alternative = substr($Raw, ($posDValue+32), ($posFValue-$posDValue-32));
					$pParams ['alternatives'][$value] = $alternative;
					$Raw = substr($Raw, $posFValue+33);
				}
				
				//Récupère la vue de selection
				$posDValue = strpos ($Raw, '{selectcomplexoptionselectedview}');
				if ($posDValue !== false) {
					
					$pParams ['selectedView'][$value] = substr($Raw, $posDValue+33);
					$Raw = substr($Raw, 0, $posDValue);
				}
				
				// Affection de l'options
				$pParams ['options'][$value] = $Raw;
				
				// Passage a l'option suivante
				$pContent = substr($pContent, $posF+22);
			}
			
		}
		
		// Supprime $options si il est vide pour lever l'exception du parametre
		if (empty($pParams ['options'])) {
			unset ($pParams ['options']);
		}
		
		if (isset ($pParams['assign'])){
			$me->assign ($pParams['assign'], _tag ('selectcomplex|selectcomplex', $pParams));
		}
		return _tag ('selectcomplex|selectcomplex', $pParams);
	}
	return '';
}