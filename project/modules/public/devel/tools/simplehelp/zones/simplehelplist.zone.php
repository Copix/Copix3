<?php
/**
* @package		simplehelp
* @author		Audrey Vassal
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @licence		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Enables the user to define the main model
*/
class ZoneSimpleHelpList extends CopixZone {
	function _createContent (& $toReturn){
	        
		$tpl = & new CopixTpl ();
		
		$daoAide = _ioDao ('simplehelp');
		
		$arAides = $daoAide->findAll();
		
		$tpl->assign ('arAides', $arAides);
		
		$toReturn = $tpl->fetch ('simplehelp.list.tpl');
		return true;
	}
}
?>
