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
	 * Vérifie que l'on est bien administrateur
	 */
	public function beforeAction (){
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}

	/**
    * Préparation de l'installation
    */
	function processInstallFramework () {
		try {
		   CopixDB::getConnection ();//test de la connexion
		   _class ('InstallService')->installFramework ();
		   return _arRedirect (_url ('admin||'));			
		}catch (CopixDBException $e){
           return _arRedirect (_url ('admin|database|', array ('databaseNotOk'=>1)));			
		}
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
		if (($id = CopixRequest::get ('id')) !== null) {
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