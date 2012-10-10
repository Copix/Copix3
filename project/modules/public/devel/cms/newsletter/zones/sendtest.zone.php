<?php
/**
* @package	cms
* @subpackage newsletter
* @author	Bertrand Yan
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package	cms
* @subpackage newsletter
* ZoneSendTest
*/
class ZoneSendTest extends CopixZone {
	function _createContent (&$toReturn) {
		$tpl = & new CopixTpl ();

		Copixcontext::push('cms');
		$tpl->assign ('newsletter', ServicesCMSPage::getOnline($this->getParam ('id')));
		Copixcontext::pop();

		$tpl->assign ('error' , $this->getParam ('error'));
		$tpl->assign ('online', $this->getParam ('online'));
		$toReturn = $tpl->fetch ('send.test.tpl');
		return true;
	}
}
?>