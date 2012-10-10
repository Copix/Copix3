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
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}

	/**
    * Préparation de l'installation
    */
	function processInstallFramework () {
		// find the current connection type (defined in /plugins/copixDB/profils.definition.xml)
    	$config = CopixConfig::instance ();
    	$driver = $config->copixdb_getProfile ();
    	$typeDB = $driver->getDriverName ();

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
	    return _arPpo(new CopixPPO(array('TITLE_PAGE'=>_i18n('install.title.updateModule',_request('moduleName')), 'module'=>_request('moduleName'))), 'admin|updatemodule.tpl');	}

	/**
	 * Suppression du module
	 */
	public function processDeleteModule () {
	    $tpl = new CopixTpl();
	    $tpl->assign('TITLE_PAGE',_i18n('install.title.deleteModule',_request('moduleName')));
	    $tpl->assignZone('MAIN','admin|deletemodulewithdep', array('moduleName'=>_request('moduleName')));
	    return _arDisplay($tpl);
	}
	
	/**
    * Affiche la liste des modules installables / désinstallables
    */
	public function processManageModules () {
		$tpl = new CopixTpl ();

		$tpl->assign ('TITLE_PAGE', _i18n ('install.title.manageModules'));
		$tpl->assignZone ('MAIN', 'admin|customisedInstall');

		return _arDisplay ($tpl);
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
		$tpl = new CopixTpl ();
		$tpl->assign ('TITLE_PAGE', _i18n ('install.title.selectHomePage'));
		$tpl->assign ('MAIN', CopixZone::process ('selectHomePage'));
		return _arDisplay ($tpl);
	}
}
?>