<?php

class ZoneAllUserList extends CopixZone {
	
	function _createContent (&$toReturn){
		$id = $this->getParam ('id');
		$results = _doQuery ('select id, login from cn_user');;
		$toReturn = 'Dernier inscrits: <br/>';
		if (count ($results) > 0) {
			foreach ($results as $user) {
				$toReturn .= '<a href="'._url ('communet_final||page', array ('id'=>$user-id)).'">'.$user->login.'</a><br/>';
			}
		} else {
			$toReturn .= 'Pas encore d\'inscrit<br/>';
		}
		return true;
	}
}