<?php

/**
* @package	cms
* @subpackage newsletter
* @author	???
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package	cms
* @subpackage newsletter
* ZoneSending
*/

class ZoneSending extends CopixZone {
	function _createContent (&$toReturn) {
      $tpl = & new CopixTpl ();

      //$tpl->assign ('URL_NEXT', 'index.php?module=newsletter&desc=admin&action=sendToGroup');
      $tpl->assign ('MESSAGE', CopixI18N::get ('newsletter.messages.send2').' : &nbsp;'.$this->_params['title'].'<br />'.$_SESSION['NEWSLETTER_COUNTER'].'&nbsp;'.CopixI18N::get ('newsletter.messages.send3'));
      
      $toReturn = $tpl->fetch ('sending.tpl');
      return true;
	}

}
?>