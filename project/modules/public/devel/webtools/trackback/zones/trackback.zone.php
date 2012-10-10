<?php
/**
 * Trackback zone (can be called from templates)
 * @author Patrice Ferlet - <metal3d@copix.org>
 * @package webtools
 * @subpackage trackback
 * @copyright Copix Team (c) 2007-2008
 */

class ZoneTrackback extends CopixZone {
	
	public function _createContent(&$toReturn){
		CopixHTMLHeader::addCSSLink(_resource('styles/trackbacks.css'));
		$id = $this->getParam('id');
		$module = $this->getParam('module');
		
		//check if there are trackbacks
		$tbs = _ioDao('trackbacks')->findBy( _daoSp()
											->addCondition('target_tb','=',$id)
											->addCondition('valid_tb','=',1)
											->orderBy('date_tb')
		);
		
		$tpl = new CopixTpl();
		$tpl->assign('id',$id);
		$tpl->assign('trackbacks',$tbs);
		$toReturn = $tpl->fetch('trackback.link.tpl');
		return true;
	}

	
}
?>