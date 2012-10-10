<?php
/**
 * @package copix
 * @subpackage smarty_plugins
 * @author Salleyron Julien
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Plugin smarty type fonction pour encapsuler tous les appels aux tag copix
 * <code>
 *  {copixtag type="select" selected=$value values=$values}
 * </code>
 * 
 * @see CopixTpl::tag
 */
function smarty_function_copixtag ($pParams, &$pMe) {
	$toReturn = '';
	if (strpos ($pParams['type'], '|') !== false || is_readable (COPIX_PATH . 'taglib/' . strtolower ($pParams['type']) . '.templatetag.php')) {
		// on ne passe pas le paramètre type au tag, sinon, CopixTemplateTag::validateParams fera une erreur "Paramètre inconnu"
		$tagParams = $pParams;
		unset ($tagParams['type']);
		$toReturn = _tag ($pParams['type'], $tagParams);
	}
	$assign = (isset ($pParams['assign'])) ? $pParams['assign'] : null;
	if (strlen ($assign) > 0) {
		$pMe->assign ($assign, $toReturn);
		return '';
	} else {
		return $toReturn;
	}
}