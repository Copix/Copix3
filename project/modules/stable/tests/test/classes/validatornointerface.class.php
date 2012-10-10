<?php
/**
 * Uniquement pour tester si la factory retourne bien une exception si l'interface ICOpixValidator n'est pas implémentées 
 */
class ValidatorNoInterface {
	private function _validate ($value){
		//foo
	}
}