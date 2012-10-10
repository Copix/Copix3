<?php

class ZoneFriendList extends CopixZone {
	
	function _createContent (&$toReturn){
		$id = $this->getParam ('id');
		$results = _doQuery ('select friendid, login from cn_friend_list, cn_user where userid='.$id.' and friendid=cn_user.id');;
		$toReturn = 'Liste d\'amis: <br/>';
		if (count ($results) > 0) {
			foreach ($results as $friend) {
				$toReturn .= '<a href="'._url ('communet_final||page', array ('id'=>$friend->friendid)).'">'.$friend->login.'</a><br/>';
			}
		} else {
			$toReturn .= 'Pas encore d\'amis<br/>';
		}
		return true;
	}
}