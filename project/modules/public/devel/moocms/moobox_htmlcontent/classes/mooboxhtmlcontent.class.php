<?php
class MooboxHTMLContent extends MooBox {
	
	public function getContent($params){
		return "<h2>".$params["title"]."</h2>".$params['htmlcontent'];
	}
	
	public function getEdit(){
		$tpl= new CopixTpl();
		return $tpl->fetch("moobox_htmlcontent|edit.php");
	}
	
}
?>