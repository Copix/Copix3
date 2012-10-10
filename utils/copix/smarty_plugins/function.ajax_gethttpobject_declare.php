<?php
/**
 * @package 	copix
 * @subpackage	smarty_plugins
* @author  		Croes Gérald
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Plugin smarty type fonction
* Fonction pour déclarer en JS une méthode pour récupérer un element "Ajax" 
* <code>
*  {ajax_gethttpobject_declare}
* </code>
* @see CopixTagLibAjaxGetHttpObject
*/
function smarty_function_ajax_gethttpobject_declare($params, &$me) {
	Copix::RequireOnce (COPIX_PATH.'taglib/CopixTagLibAjaxGetHttpObject.class.php');
    CopixTagLibAjaxGetHttpObject::doDeclare ();
}