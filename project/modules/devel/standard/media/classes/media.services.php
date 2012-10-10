<?php
class ServicesMedia extends CopixServices {
	public function getPathFromId () {
		$path = array (
		1 => 'c:\logo.gif'
		);
		
		return $path[_request('id')];
	}
}
?>
