<?php
class CnCredentialHandler implements ICopixCredentialHandler {

	public function assert ($pStringType, $pString, $pUser){
		
		switch ($pStringType) {
			case 'user':
				return ($pString == $pUser->getCaption());
			default:
				return null;
		}
	}
}
?>