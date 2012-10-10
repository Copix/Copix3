<?php
/**
 * @package 	copix
 * @subpackage	smarty_plugins
 * @author		Croës Gérald
 * @copyright	2001-2006 CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Plugin smarty type fonction
 * Purpose: Génère une valeur JSON.
 *
 * Input:    assign=variable où la sortie sera assignée
 *           data=valeur à convertir
 *           data_*=le pr
 *           * = tout autre paramètre sera passé encodé
 *
 * @param array $params les paramètres passés au tag
 * @param Smarty $me l'objet Smarty en cours d'exécution
 */
function smarty_function_json ($params, &$me) {
	
	// Paramètre 'assign'
	if(isset($params['assign'])) {
		$assign = $params['assign'];
		unset($params['assign']);
	} else {
		$assign = null;
	}
	
	// Paramètre 'data'
	if(isset($params['data'])) {
		$data = $params['data'];
		unset($params['data']);
	} else {
		$data = array();
	}
	
	// Paramètres 'data_*' et '*'
	foreach($params as $k => $v) {
		if(stripos($k, 'data_') === 0) {
			$data[substr($k, 5)] = $v;
		} else {
			$data[$k] = $v;
		}
	}
	
	// Génération
	$toReturn = CopixJSON::encode($data); 
	
	// Assigne ou retourne
	if($assign) {
		$me->assign($assign, $toReturn);
		return '';
	} else {
		return $toReturn;
	}
}