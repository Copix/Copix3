<?php
/**
* @package	copix
* @subpackage ldap
* @author	Croes Gérald
* @copyright 2001-2006 CopixTeam
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Représente un jeu de résultat d'une requête LDAP faite avec CopixLdapConnection
 * @package copix
 * @subpackage ldap
 */
class CopixLDAPResultSet {
	/**
    * The connection used to the current search
    */
	var $_ldapConnection    = null;
	
	/**
    * The search result ID
    */
	var $_ldapSearchResults = null;

	/**
    * the last fetched entry
    * @var resource
    */
	var $_lastEntryID = null;

	/**
    * first entry
    * @var resource
    */
	var $first = null;

	/**
	* Constuctor
	*/
	public function __construct (& $ldapConnection, $id) {
		$this->first = true;
		$this->_ldapConnection    = $ldapConnection;
		$this->_ldapSearchResults = $id;
	}

	/**
    * fetch the result (single line)
    */
	public function fetch () {
		//static $first = true;
		if ($this->first === true){
			$method = 'ldap_first_entry';
			$this->first = false;
			$searchID = $this->_ldapSearchResults;
		}else{
			$method = 'ldap_next_entry';
			$searchID = $this->_lastEntryID;
		}

		if (($this->_lastEntryID = $method ($this->_ldapConnection->getConnectionResource (), $searchID)) === false){
			$return = null;
			return $return;
		}
		return new CopixLDAPEntry ($this->_ldapConnection->getConnectionResource (), $this->_lastEntryID);
	}

	/**
    * Count the results of the current search
    */
	public function count (){
		return ldap_count_entries ($this->_ldapConnection->getConnectionResource (), $this->_ldapSearchResults);
	}
}