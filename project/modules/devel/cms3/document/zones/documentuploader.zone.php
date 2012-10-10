<?php

class ZoneDocumentUploader extends CopixZone {
	
	public function _createContent (&$toReturn){
		
		$filename = $this->getParam('filename');
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		list($caption_hei) = explode('.'.$ext, $filename);
		
		$tpl = new CopixTpl ();
		$tpl->assign('fileId', $this->getParam('fileId'));
		$tpl->assign('caption_hei', $caption_hei);
		
		$toReturn = $tpl->fetch ('documentuploader.form.php');
		return true;
	}
}
?>