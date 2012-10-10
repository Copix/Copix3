<?php

class ZoneDescription extends CopixZone {
	
	function _createContent (&$toReturn){
		$id = $this->getParam ('id');
		if (isset ($id)) {
			$record = _ioDao('cn_user')->get ($id);
			$toReturn = 'Description : <br/>';
			$toReturn .= $record->description.'<br/>';
		} else {
			$results = _ioDao('cn_user')->findBy (_daoSp ()->orderBy (array('id', 'DESC')));
			$toReturn = 'Dernier inscrit : '.$results[0]->login.'<br/>';
			$toReturn .= 'Description : <br/>';
			$toReturn .= $results[0]->description.'<br/>';
		}
		return true;
	}
}
