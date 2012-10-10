<?php

class ZoneFileList extends CopixZone {

	protected function _createContent (& $toReturn){
		$id_category = $this->getParam('id_category', null);
		$id_subcategory = $this->getParam('id_subcategory', null);
		$tpl = new CopixTpl ();
		$ppo = new CopixPPO ();
		$ppo->arStoredFile = _ioClass ('repository|storedfile')->getList ($id_category, $id_subcategory);
		$tpl->assign('ppo', $ppo);
		$toReturn = $tpl->fetch ('file.list.php');
	}
}
?>