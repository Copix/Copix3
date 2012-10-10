<?php
/**
* @package	copix
* @subpackage ldap
* @author	Croes Gérald
* @copyright 2001-2006 CopixTeam
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Classe de connection à un annuaire LDAP
 * @package copix
 * @subpackage ldap
 */
class CopixLDAPConnection {
	/**
    * connection in the ldap
    * @var resource
    */
	var $_connection = false;

	/**
    * are we bound ?
    * @var boolean
    */
	var $_binded     = false;

	/**
    * Host of the ldap server
    * @var string
    */
	var $_host = null;

	/**
    * Port of the ldap server
    */
	var $_port = null;

	/**
    * the base dn
    */
	var $_baseDn = null;

	/**
    * If we wants to debug the query or not.
    */
	var $_debugQuery = false;

	/**
    * Last executed query
    * @var string
    */
	var $_lastQuery = '';

	/**
    * Applies the profile to the object
    */
	private function _applyProfile ($profile){
		$this->_baseDn   = $profile->dn;
		$this->_host     = $profile->host;
		$this->_user     = $profile->user;
		$this->_password = $profile->password;
	}

	/**
    * connection
    * @return boolean true on success, false on failure
    */
	public function connect ($profile){
		
	    $this->_applyProfile ($profile);
		
		if ($this->_host === null){
			throw new CopixException (_i18n ('copix:ldap.error.hostUndefined'));
		}

		$this->_connection = ldap_connect ($this->_host);
		if ($this->_connection === false){
			return false;
		}

		@ldap_set_option ($this->_connection, LDAP_OPT_PROTOCOL_VERSION, 3);
		@ldap_set_option ($this->_connection, LDAP_OPT_REFERRALS, 0);

		$this->_bind ();
		return true;
	}

	/**
    * Gets the connection id.
    */
	public function getConnectionResource () {
		return $this->_connection;
	}

	/**
    * Closing the connection
    */
	public function close () {
		if ($this->_connection !== false){
			ldap_close ($this->_connection);
			$this->_connection = false;
		}
	}

    /**
    * Indique si nous sommes arrivé à nous connecter
    * @return boolean
    */
    public function isConnected (){
        return $this->_connection !== null && $this->_connection !== false;
    }

	/**
    * @return bool true on success, false on failure
    */
	private function _bind () {
		//if not connected, try to connect as anonymous
		if ($this->_connection === false){
			if ($this->connect () === false){
				return false;
			}
		}

		//finnally, we bind.
		$this->_binded = ldap_bind ($this->_connection, $this->_user, $this->_password);

		if ($this->_binded === false) {
			$this->close ();
			return false;
		}
		return true;
	}

	/**
    * unbind....
    */
	private function _unbind (){
		if ($this->_binded !== false){
			ldap_unbind ($this->_connection);
			$this->_binded = false;
			//$this->close ();
		}
	}

	/**
    * Search in the directory
    */
	public function  doQuery ($searchString,$sortFilter=null,$maxResults=0) {
		$this->_lastQuery = $searchString;
		if ($this->_debugQuery){
			echo $this->_lastQuery;
		}

		$this->_assertConnexion ();

		if (($search = @ldap_search ($this->_connection, $this->_baseDn, $searchString, array(), 0, $maxResults)) !== false){
			if ($sortFilter !== null){
				ldap_sort ($this->_connection, $search, $sortFilter);
			}
			$return = new CopixLDAPResultSet ($this, $search);
		}else{
			$return = false;
		}
		return $return;
	}

	/**
	* Récupération à partir d'un dn (recherche).
	* @static
	* @param string  $dn  dn à rechercher
	* @return le résultat corespondant au dn.
	*/
	public function get ($dn) {
		$this->_assertConnexion ();
		if (($search = @ldap_read ($this->_connection, $dn, '(objectClass=*)')) !== false){
			$resultSet = new CopixLDAPResultSet ($this, $search);
		}else{
			return null;
		}
		if ($resultSet->count () == 0){
			return null;
		}
		return $resultSet->fetch ();
	}

	/**
	* ajouter des entrées à un annuaire LDAP.
	* @static
	* @param string  $dn  dn ou l'ajout à lieu
	* @param array   $entry valeurs à insérer
	*/
	public function insert ($dn, $entry) {
		$this->_assertConnexion ();

		return (ldap_add ($this->_connection, $dn, $this->_cleanArrayForOperations ($entry->asArray ())) !== false);
	}

	/**
	* Supprimer des entrées d'un annuaire LDAP.
	* @static
	* @param string  $dn  le nom distingué de l'entrée à supprimer
	*/
	public function delete ($dn) {
		$this->_assertConnexion ();

		if ((@ldap_delete ($this->_connection, $dn)) !== false){
			return true;
		}else{
			return false;
		}
	}

	/**
	* Modifier des entrées d'un annuaire LDAP.
	* @static
	* @param string  $dn  le nom distingué de l'entrée à supprimer
    * @param CopixLdapEntry  $entry valeurs à insérer
    */
	public function update ($dn, $entry) {
		$this->_assertConnexion ();

		//first we delete empty elements.
		$toDelete = array ();
		foreach (($entryAttributes = $entry->asArray ()) as $key=>$value){
			if ($value == "" || (is_array ($value) && count ($value)== 0)){
				$toDelete[$key] = array ();
			}
		}
		if (count ($toDelete) > 0){
			ldap_mod_del ($this->_connection, $dn, $toDelete);
		}

		if ((ldap_modify ($this->_connection, $dn, $this->_cleanArrayForOperations ($entryAttributes))) !== false){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Renomage d'une entrée de l'annuaire
	 */
	public function rename ($entry,$newdn,$parent){
		$this->_assertConnexion ();
		//$parent = 'ou=Salaries,ou=Annuaire,dc=intranet-kse,dc=net';
		$res = @ldap_rename($this->_connection, $entry->dn, $newdn, $parent,false);
		return $res;
	}


	/**
    * Retourne le nom distingué du noeud supérieur.
    * @static
    * @param string  $dn  le nom distingué de l'entrée dont on veut noeud supérieur
    * @param string  $rankUp le nombre de fois ou l'on désire remonter dans le noeud
    */
	public function belongsTo ($dn , $rankUp = 1) {
		$tab      = explode (',', $dn);
		$searchDn = '';
		$first = true;
		foreach ($tab as $index=>$elem){
			if ($index >= $rankUp) {
				if (!$first) {
					$searchDn .= ',';
				}
				$searchDn .= $elem;
				$first = false;
			}
		}
		$this->_assertConnexion ();

		if (($search = @ldap_read ($this->_connection, $searchDn, '(objectClass=*)')) !== false){
			$resultSet = new CopixLDAPResultSet ($this, $search);
		}else{
			return null;
		}
		if ($resultSet->count () == 0){
			return null;
		}
		return $resultSet->fetch ();
	}


	/**
    * Gets a list of child entries for an entry. Given a DN, this function fetches the list of DNs of
    * child entries one level beneath the parent. For example, for the following tree:
    *
    * <code>
    * dc=example,dc=com
    *   ou=People
    *      cn=Dave
    *      cn=Fred
    *      cn=Joe
    *      ou=More People
    *         cn=Mark
    *         cn=Bob
    * </code>
    *
    * Calling <code>get_container_contents("ou=people,dc=example,dc=com" )</code>
    * would return the following list:
    *
    * <code>
    *  cn=Dave
    *  cn=Fred
    *  cn=Joe
    *  ou=More People
    * </code>
    *
    * @param string $dn The DN of the entry whose children to return.
    * @param int $size_limit (optional) The maximum number of entries to return.
    *             If unspecified, no limit is applied to the number of entries in the returned.
    * @param string $filter (optional) An LDAP filter to apply when fetching children, example: "(objectClass=inetOrgPerson)"
    * @return array An array of DN strings listing the immediate children of the specified entry.
    */
	public function getContainerContents( $dn, $size_limit=0, $filter='(objectClass=*)', $deref=LDAP_DEREF_ALWAYS ) {
		$this->_assertConnexion ();

		//	echo "get_container_contents( $server_id, $dn, $size_limit, $filter, $deref )\n";
		$search = @ldap_list( $this->_connection, $dn, $filter, array( 'dn' ), 1, $size_limit, 0, $deref );
		if(! $search) {
			return array();
		}else{
			return new CopixLDAPResultSet ($this, $search);
		}
	}

	/**
    * Cleans the array for an adding purposes
    */
	private function _cleanArrayForOperations ($entry){
		$return = array ();
		foreach ($entry as $key=>$value){
			if (! ($value == "" || (is_array ($value) && count ($value)== 0))){
				$return[$key] = $value;
			}
		}
		return $return;
	}
	
	/**
	 * Vérifie que les infos de connexion ont bien été remplis, le cas échéant, génère une CopixException
	 */
	private function _assertConnexion () {
		if ($this->_connection === false) {
			throw new CopixException (_i18n ('copix:ldap.error.notConnected'));
		}
		if ($this->_baseDn === null){
			throw new CopixException (_i18n ('copix:ldap.error.dnNotGiven'));
		}
	}
}
?>