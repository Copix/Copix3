<?php
/**
 * Uniquement pour tester si la factory retourne bien une exception si l'interface ICOpixValidator n'est pas implémentées 
 */
class ValidatorConstructNoInterface {
	public function __construct ($pArgs){
	}

	private function _validate ($value){
		//foo
	}
}

?>