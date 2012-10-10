<?php
/**
 * @package standard
 * @subpackage admin 
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

_classRequire ('admin|ProxyConfigurationFile');

/**
 * Gestion des proxys
 * 
 * @package standard
 * @subpackage admin 
 */
class ActionGroupProxy extends CopixActionGroup {
	/**
	 * Executé avant toute action
	 *
	 * @param string $pActionName Nom de l'action
	 */
	public function beforeAction ($pActionName) {
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
		CopixPage::add ()->setIsAdmin (true);
		_notify ('breadcrumb', array ('path' => array ('admin|proxy|' => _i18n ('breadcrumb.proxy'))));
		
	}
	
	/**
	 * Liste des proxys
	 * 
	 * @return CopixActionReturn
	 */
	public function processDefault () {		
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('proxy.list.title.page');
		$proxys = CopixConfig::instance ()->copixproxy_getProxys ();
		$ppo->proxysEnabled = array ();
		$ppo->proxysDisabled = array ();
		foreach ($proxys as $proxyId => $proxy) {
			if ($proxy->isEnabled ()) {
				$ppo->proxysEnabled[$proxyId] = array (
					'host' => $proxy->getHost (),
					'port' => $proxy->getPort ()
				);
			} else {
				$ppo->proxysDisabled[$proxyId] = array (
					'host' => $proxy->getHost (),
					'port' => $proxy->getPort ()
				);
			}
		}
				
		return _arPPO ($ppo, 'proxy.list.tpl');
	}
	
	/**
	 * Ajout / modification d'un proxy
	 * 
	 * @return CopixActionReturn
	 */
	public function processEdit () {
		_notify ('breadcrumb', array ('path' => array ('#' => _i18n ('admin|breadcrumb.proxy.add'))));
		
		$ppo = new CopixPPO ();
		$ppo->errors = array ();
		
		// si on a validé le formulaire
		if (_request ('enabled') !== null) {
			$proxy = array ();
			$proxy['id'] = _request ('id');
			$proxy['enabled'] = CopixRequest::getBoolean ('enabled');
			$proxy['host'] = _request ('host');
			// si le port n'est pas composé que de chiffres, on passe exprès la chaine au validateur, pour qu'il retourne une erreur
			$proxy['port'] = (preg_match ('/^[0-9]/', _request ('port'))) ? CopixRequest::getInt ('port') : _request ('port');
			$proxy['user'] = _request ('user');
			$proxy['password'] = _request ('password');
			// explode nous renvoi forcément un élément, vide si on n'a pas saisi d'adresses
			if (count ($notForHosts = explode ("\n", str_replace ("\r\n", "\n", trim (_request ('notForHosts'))))) == 0 || $notForHosts[0] == '') {
				$notForHosts = array ();
			}
			$proxy['notForHosts'] = $notForHosts;
			if (count ($forHosts = explode ("\n", str_replace ("\r\n", "\n", trim (_request ('forHosts'))))) == 0 || $forHosts[0] == '') {
				$forHosts = array ();
			}
			$proxy['forHosts'] = $forHosts;
			if (($validator = _validator ('copixproxyvalidator')->check ($proxy)) === true) {
				if (CopixProxy::exists ($proxy['id'])) {
					$ppo->errors[] = _i18n ('proxy.edit.error.proxyExists', $proxy['id']);
				} else {
					$copixproxy = new CopixProxy (
						$proxy['id'], $proxy['host'], $proxy['port'], $proxy['user'],
						$proxy['password'], $proxy['enabled'], $proxy['notForHosts'], $proxy['forHosts']
					);
					ProxyConfigurationFile::add ($copixproxy);
					return _arRedirect (_url ('admin|proxy|'));
				}
			} else {
				$ppo->errors = array_merge ($ppo->errors, $validator->asArray ());
			}
		}
		
		$ppo->TITLE_PAGE = _i18n ('proxy.edit.title.pageAdd');
		
		return _arPPO ($ppo, 'proxy.edit.tpl');
	}
	
	/**
	 * Supprime un proxy
	 *
	 * @return CopixActionReturn
	 * @throws ModuleAdminException Proxy non trouvé, code ModuleAdminException::PROXY_NOT_FOUND
	 */
	public function processDelete () {
		$proxy = _request ('proxy');
		_notify ('breadcrumb', array ('path' => array ('#' => _i18n ('admin|breadcrumb.proxy.delete'))));
		
		if (!CopixProxy::exists ($proxy)) {
			throw new ModuleAdminException (_i18n ('proxy.error.notFound', $proxy), ModuleAdminException::PROXY_NOT_FOUND);
		}
		
		// si on n'a pas confirmé, on affiche la demande de confirmation
		if (_request ('confirm') !== 'true') {
			return CopixActionGroup::process ('generictools|Messages::getConfirm', 
				array (
					'message'=>_i18n ('proxy.delete.message', $proxy),
					'confirm'=>_url ('admin|proxy|delete', array ('proxy' => $proxy, 'confirm' => 'true')),
					'cancel'=>_url ('admin|proxy|')
				)
			);
		}
		
		// si on arrive là, c'est qu'on a déja confirmé
		ProxyConfigurationFile::delete ($proxy);
		return _arRedirect (_url ('admin|proxy|'));
	}
	
	/**
	 * Active un proxy
	 *
	 * @return CopixActionReturn
	 */
	public function processEnable () {
		// la méthode enable s'occupe d'appeler exists
		ProxyConfigurationFile::enable (_request ('proxy'));
		return _arRedirect (_url ('admin|proxy|'));
	}
	
	/**
	 * Désactive un proxy
	 *
	 * @return CopixActionReturn
	 */
	public function processDisable () {
		// la méthode enable s'occupe d'appeler exists
		ProxyConfigurationFile::disable (_request ('proxy'));
		return _arRedirect (_url ('admin|proxy|'));
	}
}