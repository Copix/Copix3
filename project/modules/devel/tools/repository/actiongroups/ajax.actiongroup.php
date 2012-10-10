<?php

class ActionGroupAjax extends CopixActionGroup{

	public function processUpload (){
		
		$result = _ioClass ('repository|storedfile')->store ('resume_file');
		if ($result !== false) {
			echo $result;
		} else {
			echo ' ';
		}
		return _arNone ();
	}
}
?>