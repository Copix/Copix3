<?php
/**
* @package	copix
* @subpackage ldap
* @author	Croes Gérald
* @copyright 2001-2006 CopixTeam
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @ignore
*/
if (!defined ('COPIX_LDAP_PATH'))
   define ('COPIX_LDAP_PATH', dirname (__FILE__).'/');

Copix::RequireOnce (COPIX_LDAP_PATH . 'CopixLdapConnection.class.php');
Copix::RequireOnce (COPIX_LDAP_PATH . 'CopixLdapResultSet.class.php');
Copix::RequireOnce (COPIX_LDAP_PATH . 'CopixLdapProfil.class.php');
Copix::RequireOnce (COPIX_LDAP_PATH . 'CopixLdapEntry.class.php');

/**
 * Factory pour acceder aux annuaires ldap
 * @package copix
 * @subpackage ldap
 */
class CopixLdapFactory {
	/**
	* Récupèration d'une connection.
	* @static
	* @param string  $named  nom du profil de connection définie dans CopixLdap.plugin.conf.php
	* @return CopixLdapConnection  objet de connection vers l'annuaire ldap
	*/
	public function getConnection ($named = null) {
		if ($named == null) {
			return CopixLdapFactory::getConnection (CopixLdapFactory::getDefaultConnectionName ());
		}
		$profil = & CopixLdapFactory::_getProfil ($named);

		//peut être partagé ?
		if ($profil->shared){
			$foundedConnection = & CopixLdapFactory::_findConnection ($named);
			if ($foundedConnection === null){
				$foundedConnection = & CopixLdapFactory::_createConnection ($named);
			}
			return $foundedConnection;
		}else{
			//Ne peut pas être partagé.
			return CopixLdapFactory::_createConnection ($named);
		}
	}

	/**
	* récupèration d'une connection par défaut.
	* @static
	* @return    string  nom de la connection par défaut
	*/
	public function getDefaultConnectionName (){
		$pluginLdap = CopixPluginRegistry::get ('CopixLdap');
		return $pluginLdap->config->default;
	}

	/* ======================================================================
	*  private
	*/

	/**
	* récupèration d'un profil de connection à une base de données.
	* @access private
	* @param string  $named  nom du profil de connection
	* @return    CopixLdapProfil   profil de connection
	*/
	private function _getProfil ($named){
		$pluginLdap = CopixPluginRegistry::get ('CopixLdap');
		if (isset ($pluginLdap->config->profils[$named])){
		   return $pluginLdap->config->profils[$named];
		}
		throw new CopixException (_i18n ('copix:ldap.error.unknowProfil', $named));
	}

	/**
	* Récupèration de la connection dans le pool de connection, à partir du nom du profil.
	* @access private
	* @param string  $named  nom du profil de connection
	* @return CopixLdapConnection  l'objet de connection
	*/
	private function _findConnection ($profilName){
		$profil = & CopixLdapFactory::_getProfil ($profilName);
		if ($profil->shared){
			//connection partagée, on peut retourner celle qui existe.
			if (isset ($GLOBALS['COPIX']['LDAP'][$profilName])){
				return $GLOBALS['COPIX']['LDAP'][$profilName];
			}else{
				$return = null;
				return $return;
			}
		}
		//la connection n'est pas partagée, quoi qu'il arrive, on ne
		// peut pas retourner une connection existante.
		//(On fera confiance au pool de PHP pour cette gestion)
		$return = null;
		return $return;
	}

	/**
	* création d'une connection.
	* @access private
	* @param string  $named  nom du profil de connection
	* @return CopixLdapConnection  l'objet de connection
	*/
	private function _createConnection ($profilName){
		$profil = & CopixLdapFactory::_getProfil ($profilName);

		//Création de l'objet
		$obj = new CopixLdapConnection ();
		if ($profil->shared) {
			$GLOBALS['COPIX']['LDAP'][$profilName] = & $obj;
		}

		if (CopixPluginRegistry::getConf ('CopixLdap', 'showLdapQueryEnabled')
   		&& (isset ($_GET['showLdapQuery'])) && ($_GET['showLdapQuery'] == '1')){
			$obj->_debugQuery = true;
		}

		$obj->connect ($profil);
		return $obj;
	}
}
?>