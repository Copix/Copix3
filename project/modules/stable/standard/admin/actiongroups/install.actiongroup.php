<?php
/**
 * @package standard
 * @subpackage admin
 *
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * ActionGroup de gestion du process d'installation de la base
 *
 * Présente le formulaire de connexion à la base de donnée
 * Exécute l'installation et redirige sur l'accueil du site
 * @package standard
 * @subpackage admin
 */
class ActionGroupInstall extends CopixActionGroup {
	/**
	 * Executé avant toute action
	 *
	 * @param string $pActionName Nom de l'action
	 */
	public function beforeAction ($pActionName) {
		CopixPage::add ()->setIsAdmin (true);
		_notify ('breadcrumb', array ('path' => array ('admin|install|manageModules' => _i18n ('install.title.manageModules'))));
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}

	/**
	 * Préparation de l'installation
	 */
	function processInstallFramework () {
		// find the current connection type (defined in /plugins/copixDB/profils.definition.xml)
		$config = CopixConfig::instance ();
		$driver = $config->copixdb_getProfile ();
		$typeDB = CopixDB::driverToDatabase($driver->getDriverName());
		
		// Search each module install file
		$scriptName = 'prepareinstall.'.$typeDB.'.sql';
		$file = CopixModule::getPath ('admin') . COPIX_INSTALL_DIR . 'scripts/' . $scriptName;
		CopixDB::getConnection ()->doSQLScript ($file);
		//make sure that copixmodule is reset
		CopixModule::reset();
		$tpl = new CopixTpl();
		$tpl->assignZone('MAIN','admin|installmodulewithdep', array('arModule'=>array( 'generictools','auth','default','admin'),'url_return'=>_url ('admin|database|done'),'messageConfirm'=>false));
		return _arDisplay($tpl);
	}

	/**
	 * Installation du module
	 */
	public function processInstallModule () {
		_notify ('breadcrumb', array ('path' => array (
			'#' => _i18n ('install.title.installModule', _request('moduleName')))
		));
		$tpl = new CopixTpl();
		$tpl->assign('TITLE_PAGE',_i18n('install.title.installModule',_request('moduleName')));
		$tpl->assignZone('MAIN','admin|installmodulewithdep', array('moduleName'=>_request('moduleName')));
		return _arDisplay($tpl);
	}
	/**
	 * Installation d'un tableau de module
	 */
	public function processInstallModules () {
		$tpl = new CopixTpl();
		//$tpl->assign('TITLE_PAGE',_i18n('install.title.installModule',_request('moduleName')));
		$tpl->assignZone('MAIN','admin|installmodulewithdep', array('arModule'=>explode('|',_request('arModule'))));
		return _arDisplay($tpl);
	}


	/**
	 * Mise a jour du module
	 */
	public function processUpdateModule () {
		_notify ('breadcrumb', array ('path' => array ('#' => _i18n ('install.title.updateModule', _request ('moduleName')))));

		// vérification de la version des dépendances
		$infos = CopixModule::getInformations (_request ('moduleName'));
		$toUpdate = array ();
		foreach ($infos->getDependencies () as $dependency) {
			if ($dependency->getKind () == 'module' && $dependency->getVersion () != null) {
				$dependencyInfos = CopixModule::getInformations ($dependency->getName ());
				if (!CopixModule::isEnabled ($dependency->getName ()) || version_compare ($dependencyInfos->getInstalledVersion (), $dependency->getVersion ()) < 0) {
					$url = _url ('admin|install|updateModule', array ('moduleName' => $dependencyInfos->getName ()));
					$toUpdate[] = '<li><a href="' . $url . '">[' . $dependencyInfos->getName () . '] ' . $dependencyInfos->getDescription () . '</a></li>';
				}
			}
		}
		if (count ($toUpdate) > 0) {
			$ppo = new CopixPPO (array ('TITLE_PAGE' => 'Dépendances à mettre à jour'));
			$message = 'Vous devez mettre les modules suivants à jour avant de mettre à jour "' . _request ('moduleName') . '" :';
			$message .= '<ul>' . implode ("\n", $toUpdate) . '</ul>';
			return CopixActionGroup::process ('generictools|messages::getError', array ('message' => $message, 'back' => _url ('admin||')));
		}
		
		return _arPpo(new CopixPPO(array('TITLE_PAGE'=>_i18n('install.title.updateModule',_request('moduleName')), 'module'=>_request('moduleName'))), 'admin|module/updatemodule.tpl');
	}

	/**
	 * Suppression du module
	 */
	public function processDeleteModule () {
		_notify ('breadcrumb', array ('path' => array (
		'#' => _i18n ('install.title.deleteModule', _request('moduleName')))
		));
		$tpl = new CopixTpl();
		$tpl->assign('TITLE_PAGE',_i18n('install.title.deleteModule',_request('moduleName')));
		$tpl->assignZone('MAIN','admin|deletemodulewithdep', array('moduleName'=>_request('moduleName')));
		return _arDisplay($tpl);
	}

	/**
	 * Affiche la liste des modules installables / désinstallables
	 */
	public function processManageModules () {
		$ppo = new CopixPPO (array ('TITLE_PAGE' => _i18n ('install.title.manageModules')));
		$ppo->MAIN = CopixZone::process ('admin|customisedInstall');
		return _arPPO ($ppo, 'generictools|blank.tpl');
	}

	/**
	 * Set the home page of the web site
	 */
	function processsetHomePage () {
		if ((_request ('id')) !== null) {
			CopixConfig::set ('|homePage', _url ('cms|default|get', array('id'=>CopixRequest::get ('id'), 'online'=>'true')));
		}elseif (($url = CopixRequest::get ('urlinput')) !== null){
			CopixConfig::set ('|homePage', $url);
		}else{
			return CopixActionGroup::process ('generictools|Messages::getError',
			array ('message'=>_i18n ('error|error.specifyid'),
		'back'=>_url  ('admin||')));
		}
		return _arRedirect (_url ('admin||'));
	}

	/**
	 * Sélection de la page par défaut pour "index.php"
	 */
	public function processSelectHomePage (){
		_notify ('breadcrumb', array (
		'path' => array ('#' => _i18n ('install.title.selectHomePage'))
		));
		$tpl = new CopixTpl ();
		$tpl->assign ('TITLE_PAGE', _i18n ('install.title.selectHomePage'));
		$tpl->assign ('MAIN', CopixZone::process ('selectHomePage'));
		return _arDisplay ($tpl);
	}
}