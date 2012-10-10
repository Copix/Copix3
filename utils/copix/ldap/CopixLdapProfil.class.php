<?php
/**
* @package	copix
* @subpackage ldap
* @author	Croes Gérald
* @copyright 2001-2006 CopixTeam
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Représente un profil de connexion à un annuaire ldap
 * @package copix
 * @subpackage ldap
 */

if (!defined ('COPIX_LDAP_PATH'))
   define ('COPIX_LDAP_PATH', dirname (__FILE__).'/');

Copix::RequireOnce (COPIX_LDAP_PATH . 'CopixLdapConnection.class.php');
Copix::RequireOnce (COPIX_LDAP_PATH . 'CopixLdapResultSet.class.php');
Copix::RequireOnce (COPIX_LDAP_PATH . 'CopixLdapEntry.class.php');

class CopixLdapProfil {
   var $dn;
   var $host;
   var $port = 389;
   var $user;
   var $password;
   var $shared = false;
   

   public function __construct ($dnName, $hostName, $portNumber, $userName, $password,$shared=false){
      $this->dn         = $dnName;
      $this->host       = $hostName;
      $this->port		= $portNumber;
      $this->user       = $userName;
      $this->password   = $password;
      $this->shared     = $shared;
   }
}