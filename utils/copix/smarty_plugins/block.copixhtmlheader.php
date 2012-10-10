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
    if (!isset ($params['kind'])){
       $params['kind'] = 'others';
    }
    $params['kind'] = strtolower($params['kind']);
   
    //On vérifie que le type demandé est bien valide
    if (!in_array ($params['kind'], array ('others', 'jscode', 'jslink', 'csslink', 'style'))){
       $smarty->_trigger_fatal_error ("[plugin copixhtmlheader] unknow kind ".$params['kind'].", only jsLink, cssLink, style, others, jsCode are available");
    } 
    
    $funcName = 'add'.$params['kind'];
    if (in_array ($params['kind'], array ('jscode', 'others'))) {
        CopixHTMLHeader::$funcName ($content, isset ($params['key']) ? $params['key'] : null);
    }elseif (in_array ($params['kind'], array ('jslink', 'csslink'))) {
    	foreach (explode ("\n", $content) as $line){
        	CopixHTMLHeader::$funcName ($line);
    	}
    }else{
    	//il ne reste plus que style
        CopixHTMLHeader::$funcName ($content);
    }
    return '';
}
?>