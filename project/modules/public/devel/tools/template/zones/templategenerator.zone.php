<?php
/**
* @package	template
* @author	Croes Gérald, see copix.org for other contributors.
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class ZoneTemplateGenerator extends CopixZone {
	function _createContent (& $toReturn) {
		$addPossibilities = array ();
		$element = null;
		//on récupère la liste des éléments que l'on peut ajouter à l'élément sélectionné.
		if ($this->getParam ('elementId', null) !== null){
			$addPossibilities = $this->_params['editor']->getAddPossibilitiesForElementById ($this->_params['elementId']);
			$element          = $this->_params['editor']->getTemplateElementById ($this->_params['elementId']);
		}

		//assignation au template
		$tpl = & new CopixTpl ();
		$tpl->assign ('template',  $this->_params['editor']->getRoot ());
		$tpl->assign ('editId',    $this->_params['editId']);
		$tpl->assign ('elementId', $this->getParam ('elementId'));
		$tpl->assign ('arAddPossibilities', $addPossibilities);
		$tpl->assign ('element',   $element);

		$toReturn = $tpl->fetch ('templategenerator.show.ptpl');		
		return true;
	}
}
?>