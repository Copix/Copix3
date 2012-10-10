<?php
class CopixTemplateAuth {
	/**
	* Can we write templates ?
	*/
	function canWrite (){
		return true;
	}

	/**
	* Can we choose the template theme ?
	*/
	function canModerate (){
		return true;
	}
}
?>