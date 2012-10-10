<?php
class ZoneErrors extends CopixZone {
	function _createContent (&$toReturn) {
		$tpl = new CopixTpl ();
		$tpl->assign('data', $this->getParam('data'));
		$toReturn = $tpl->fetch('errors.zone.tpl');
		return true;
	}
}
?>