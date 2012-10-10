<?php
/**
 * @package 	copix
 * @subpackage 	smarty_plugins
 * @author		Gérald Croës
 * @copyright	2000-2006 CopixTeam
 * @link			http://www.copix.org
 * @license 		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Permet au concepteur de template d'ajouter des éléments censés apparaitre dans la partie
 * <head> du template HTML.
 *
 * Params:   kind: string (jsLink, cssLink, style, others, jsCode)
 *
 * @param		array	$params		tableau des paramètres passés à la balise
 * @param		string	$content	contenu du block
 * @param		Smarty	$smarty		pointeur sur l'élement smarty
 * @return		string
 *
 * <code>
 * {copixhtmlheader kind=JsCode}
 * var variable = "{$maVariableValue}";
 * {/copixhtmlheader}
 * </code>
 */
function smarty_block_copixhtmlheader ($params, $content, &$smarty) {
	if (is_null ($content)) {
		return;
	}

	//Si aucun type n'a été demandé, on utilise others par défaut.
	$kind = isset ($params['kind']) ? strtolower($params['kind']) : 'others';
	$key = isset ($params['key']) ? $params['key'] : null;
	$funcName = 'add'.$kind;

	switch($kind) {
		case 'jscode':
		case 'jsdomreadycode':
		case 'others':
			CopixHTMLHeader::$funcName ($content, $key);
			break;
				
		case 'jslink':
		case 'csslink':
			foreach (array_filter (array_map ('trim', explode ("\n", $content))) as $line){
				if (strlen (trim ($line)) > 0) {
					CopixHTMLHeader::$funcName ($line);
				}
			}
			break;

		case 'style':
			CopixHTMLHeader::$funcName ($content);
			break;
				
		default:
			$smarty->_trigger_fatal_error ("[plugin copixhtmlheader] unknow kind ".$params['kind'].", only jsLink, cssLink, style, others, jsCode, jsdomreadycode are available");
				
	}

	return '';
}