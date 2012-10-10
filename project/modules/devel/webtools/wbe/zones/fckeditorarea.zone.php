<?php
/**
 * @package		webtools
 * @subpackage	wbe
 * @author		Favre Brice
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Affichage d'une zone éditable
 * @package		webtools
 * @subpackage	wbe
 */
class ZoneFckEditorArea extends CopixZone {
	function _createContent (& $toReturn){		
		$tpl = new CopixTpl ();
		$ppo = new CopixPPO ();
		$ppo->name = $this->getParam ('name', 'content');
		$ppo->cols = $this->getParam ('cols', '50');
		$ppo->rows = $this->getParam ('rows', '15');
		$ppo->content = $this->getParam ('content', '');
		$tpl->assign('ppo', $ppo);
		$toReturn = $tpl->fetch ('fckarea.php');
		return true;
	}
}
?>