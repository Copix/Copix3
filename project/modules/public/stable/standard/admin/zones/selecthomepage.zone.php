<?php
/**
 * @package standard
 * @subpackage admin 
* 
* @author		Bertrand Yan, Croes Gérald
* @copyright	2001-2005 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Permet la sélection de la page d'accueil
 * @package standard
 * @subpackage admin 
* 
*/
class ZoneSelectHomePage extends CopixZone {
	function _createContent (& $toReturn){
		$tpl = new CopixTpl ();
		$tpl->assign ('homepageUrl', CopixConfig::get ('|homePage'));
		$toReturn = $tpl->fetch ('selecthomepage.form.tpl');
		return true;
	}
}
?>