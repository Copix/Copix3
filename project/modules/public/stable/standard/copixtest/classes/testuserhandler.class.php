<?php
/**
 * @package    standard
 * @subpackage copixtest
 * @author     Guillaume Perréal
 * @copyright  CopixTeam
 * @link       http://copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Gestionnaire
 *
 */
class TestUserHandler implements ICopixUserHandler {
	
	static public $result = false;
	static public $login = null;
	static public $userId = null;
	
	public function login ($pParams){
		if(!self::$result) {
			return new CopixUserLogResponse (false, null, null, null);
		} else {
			return new CopixUserLogResponse (true, 'copixtest|testuserhandler', self::$userId, self::$login);
		}
	}
	
	public function logout ($pParams){
		return new CopixUserLogResponse (true, null, null, null);
	}
	
	private function _getUserRecord() {
		$record = new stdClass();
		$record->login = self::$login;
		$result->id = self::$userId;
		$result->enabled = true;
		return $record;		
	}
	
	public function find ($pParams = array ()){
		if(
			   (!isset($pParams['login']) || ($pParams['login'] == self::$login))
			|| (!isset($pParams['id'])    || ($pParams['id'] == self::$userId))
		) {
			return array($this->_getUserRecord());
		} else {
			return array();			
		}
	}
	
	public function getInformations ($pUserId){
		if($pUserId == self::$userId) {
			$this->_getUserRecord();
		} else {
			throw new CopixException ('No informations on user '.$pUserId);
		}
	}	
	
}

?>