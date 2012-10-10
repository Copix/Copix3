<?php
/**
 * @package standard
 * @subpackage admin 
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Ecrans standards pour les opérations d'administration
 * @package standard
 * @subpackage admin 
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
	 * Vérifie que l'on est bien administrateur
	 */
	public function beforeAction (){
		CopixAuth::getCurrentUser ()->assertCredential ('basic:registered');
	}

    /**
     * Ecran d'acceuil des opérations d'administration
     */
	function processDefault (){
        //Inclusion de la classe checker pour tester les pré-requis
        _classInclude ('InstallChecker');
        $checker = new InstallChecker();
	    
        $ppo = new CopixPPO ();
        $ppo->TITLE_PAGE = _i18n ('install.title.admin');

        $tips = $this->_getTips();

        $ppo->tips = $tips->tips;
        $ppo->warning = $tips->warning;
        
		$ppo->homePageUrl    =  CopixConfig::get ('|homePage');

		$ppo->pdomysqlEnabled = $checker->typeDbInstalled ('pdo_mysql');
		if (!$ppo->pdomysqlEnabled) {
		    $ppo->tips[] = _i18n ('install.tips.pdomysql');
		}
	  
		$ppo->phpunitEnabled = @include_once ('PHPUnit/Framework.php');
		if (!$ppo->phpunitEnabled) {
		    $ppo->tips[]=_i18n('install.tips.unittest');
		}

		$ppo->databaseEnabled = $checker->isValidDefaultDatabase();

		if (!$ppo->databaseEnabled) {
    		$ppo->tips[]= _i18n ('install.tips.database');
		}
		
		if (! _currentUser ()->testCredential ('basic:admin')){
			$ppo->tips = array ();
		}
		
		// recherche des liens
		$ppo->links = array ();
		foreach (CopixModule::getList () as $moduleName) {
			$moduleInformations = CopixModule::getInformations ($moduleName);
			
			// si on a au moins un lien
			if (count ($moduleInformations->admin_links) > 0) {
				$groupid = (!is_null ($moduleInformations->admin_links_group->id)) ? $moduleInformations->admin_links_group->id : uniqid ();
				
				$ppo->links[$groupid]['groupcaption'] = $moduleInformations->admin_links_group->caption;
				$ppo->links[$groupid]['icon'] = (!is_null ($moduleInformations->admin_links_group->icon)) ? $moduleInformations->admin_links_group->icon : $moduleInformations->icon;

				foreach ($moduleInformations->admin_links as $linkInformations){
					if (($linkInformations['credentials'] == null) || CopixAuth::getCurrentUser ()->testCredential ($linkInformations['credentials'])){
						$ppo->links[$groupid]['modules'][][$linkInformations['url']] = $linkInformations['caption'];
						$ppo->links[$groupid]['caption'] = $moduleInformations->description;
					}
				}
			}
		}
		
		//echo '<pre><div align="left">';
		//var_dump ($ppo->links);
		
		return _arPPO ($ppo, 'admin.tpl');
	}
	
	/**
	 * Supression des répertoires temporaires
	 */
	public function processClearTemp (){
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	    _class ('admintemp')->clearTemp ();
	    return _arRedirect (_url ('admin||'));
	}
	
	/**
	 * Retourne le tableau de tips
	 */
	private function _getTips() {
	    $checker = _class ('InstallChecker');

	    $tips    = array ();
	    $warning = array ();
        
        if (!$checker->apcInstalled ()) {
	        $tips[] = _i18n ('install.tips.apc');
	    }
	    
	    if (!$checker->magicquotesInstalled () && $checker->magicquotesPluginInstalled ()) {
	        $tips[] = _i18n ('install.tips.disabledmagicquotes');
	    }

	    if ($checker->magicquotesInstalled () && !$checker->magicquotesPluginInstalled ()) {
	        $warning[] = _i18n ('install.tips.warningmagicquotes');
	    }
	     
	    if ($checker->magicquotesInstalled () && $checker->magicquotesPluginInstalled ()) {
	        $tips[] = _i18n ('install.tips.enabledmagicquotes');
	    }
	    $toReturn = new StdClass (); 
	    $toReturn->tips = $tips;
	    $toReturn->warning = $warning;
	    return $toReturn;
	}
	
	/**
	 * Affiche le PHPInfo 
	 */
	public function processPHPInfo (){
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
		
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = 'PHPInfo';
		$ppo->CopixVersion = COPIX_VERSION;

		ob_start();                                                                                                       
		phpinfo();                                                                                                        
		$info = ob_get_contents();                                                                                        
		ob_end_clean();                                                                                                   
		$ppo->phpinfo = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $info);

		return _arPpo ($ppo, 'phpinfo.tpl');
	}
}
?>