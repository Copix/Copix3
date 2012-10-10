<?php
/**
 * @package standard
 * @subpackage admin 
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 */

_classInclude ('admin|moduleadminexception');

/**
 * Permet l'écriture de la configuration des proxys
 * 
 * @package standard
 * @subpackage admin
 */
class ProxyConfigurationFile {
	/**
	 * Ecrit le fichier de configuration
	 * 
	 * @param CopixProxy[] $pProyxs Proxys à écrire
	 */
	private static function _write ($pProyxs) {
		self::assertWritable ();
		foreach ($pProyxs as $proxyID => $proxyInfos) {
			$proxyInfos['id'] = $proxyID;
			if (($valid = _validator ('CopixProxyValidator')->check ($proxyInfos)) !== true) {
				throw new ModuleAdminException (_i18n ('proxy.write.error', array ($proxyID, $valid->asString ())), ModuleAdminException::PROXY_INVALID);
			}
		}
		$php = new CopixPHPGenerator ();
		$content = $php->getPHPTags ($php->getVariableReturn ($pProyxs));
		CopixFile::write (CopixConfig::instance ()->copixproxy_getConfigFilePath (), $content);
	}
	
	/**
	 * Retourne la variable de config contenue dans le fichier de config
	 *
	 * @return array
	 */
	private static function _getConfig () {
		$path = CopixConfig::instance ()->copixproxy_getConfigFilePath ();
		if (!file_exists ($path)) {
			// ne pas appeler _write ici, sinon on a une boucle infinie d'appels à assertWritable
			$php = new CopixPHPGenerator ();
			$content = $php->getPHPTags ($php->getVariableReturn (array ()));
			CopixFile::write (CopixConfig::instance ()->copixproxy_getConfigFilePath (), $content);
			self::assertWritable ();
		}
		// ne pas utiliser Copix::RequireOnce, sinon, la portée des variables de config ne sera pas suffisante pour être lue ici
		return require ($path);
	}
	
	/**
	 * Ajoute un proxy
	 * 
	 * @param CopixProxy $pCopixProxy Proxy à ajouter
	 * @throws ModuleAdminException Le proxy existe déja, code ModuleAdminException::PROXY_EXISTS
	 */
	public static function add ($pCopixProxy) {
		if (CopixProxy::exists ($pCopixProxy->getId ())) {
			throw new ModuleAdminException (_i18n ('proxy.error.exists', $pCopixProxy->getId ()), ModuleAdminException::PROXY_EXISTS);
		}
		self::assertWritable ();
		$proxys = self::_getConfig ();
		$proxys[$pCopixProxy->getId ()] = array (
			'host' => $pCopixProxy->getHost (),
			'port' => $pCopixProxy->getPort (),
			'user' => $pCopixProxy->getUser (),
			'password' => $pCopixProxy->getPassword (),
			'enabled' => $pCopixProxy->isEnabled (),
			'notForHosts' => $pCopixProxy->getNotForHosts(),
			'forHosts' => $pCopixProxy->getForHosts ()
		);
		self::_write ($proxys);
	}
	
	/**
	 * Modifie un proxy
	 * 
	 * @param string $pId Identifiant
	 * @param boolean $pEnabled Indique si le proxy est actif
	 * @param string $pHost Adresse
	 * @param int $pPort Port
	 * @param string $pUser Utilisateur
	 * @param string $pPassword Mot de passe
	 * @param string[] $pNotForHosts Adresses qui ne sont pas gérées par ce proxy
	 * @param string[] $pForHosts Adresses qui sont gérées par ce proxy
	 * @throws ModuleAdminException Le proxy n'a pu être trouvé, code ModuleAdminException::PROXY_NOT_FOUND
	 */
	public static function edit () {
		
	}
	
	/**
	 * Supprime un proxy
	 * 
	 * @param string $pId Identifiant
	 * @throws ModuleAdminException Le proxy n'a pu être trouvé, code ModuleAdminException::PROXY_NOT_FOUND
	 */
	public static function delete ($pId) {
		if (!CopixProxy::exists ($pId)) {
			throw new ModuleAdminException (_i18n ('proxy.error.exists', $pId), ModuleAdminException::PROXY_NOT_FOUND);
		}
		self::assertWritable ();
		$proxys = self::_getConfig ();
		unset ($proxys[$pId]);
		self::_write ($proxys);
	}
	
	/**
	 * Active un proxy
	 *
	 * @param string $pId Identifiant
	 */
	public static function enable ($pId) {
		self::_enable ($pId);
	}
	
	/**
	 * Désactive un proxy
	 *
	 * @param string $pId Identifiant
	 */
	public static function disable ($pId) {
		self::_enable ($pId, false);
	}
	
	/**
	 * Active ou désactive un proxy
	 *
	 * @param string $pId Identifiant
	 * @param boolean $pEnable Indique si on doit activer ou désactiver
	 */
	private static function _enable ($pId, $pEnable = true) {
		if (!CopixProxy::exists ($pId)) {
			throw new ModuleAdminException (_i18n ('proxy.error.notFound', $pId), ModuleAdminException::PROXY_NOT_FOUND);
		}
		self::assertWritable ();
		$proxys = self::_getConfig ();
		$proxys[$pId]['enabled'] = $pEnable;
		self::_write ($proxys);
	}
	
	/**
	 * Indique si le fichier de configuration est modifiable, le créé si necessaire
	 * 
	 * @return boolean
	 */
	public static function isWritable () {
		$path = CopixConfig::instance ()->copixproxy_getConfigFilePath ();
		self::_getConfig ();
		return is_writable ($path);
	}
	
	/**
	 * Vérifie que le fichier de configuration est modifiable, sinon, lève une exception
	 * 
	 * @throws ModuleAdminException Le fichier de config n'est pas modifiable, code ModuleAdminException::PROXY_CONFIG_NOT_WRITABLE
	 */
	public static function assertWritable () {
		if (!self::isWritable ()) {
			throw new ModuleAdminException (
				_i18n ('proxy.error.configFileNotWritable', CopixConfig::instance ()->copixproxy_getConfigFilePath ()),
				ModuleAdminException::PROXY_CONFIG_NOT_WRITABLE
			);
		}
	}
}