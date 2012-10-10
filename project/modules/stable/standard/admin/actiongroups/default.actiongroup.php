<?php
/**
 * @package		standard
 * @subpackage	admin 
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Ecrans standards pour les opérations d'administration
 * 
 * @package		standard
 * @subpackage	admin 
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
	 * Executé avant toute action
	 *
	 * @param string $pActionName Nom de l'action
	 */
	protected function _beforeAction ($pActionName) {
		CopixAuth::getCurrentUser ()->assertCredential ('basic:registered');
		CopixPage::add ()->setIsAdmin (true);
	}

	/**
	 * Ecran d'accueil des opérations d'administration
	 * 
	 * @return CopixActionReturn
	 */
	public function processDefault () {
		// inclusion de la classe checker pour tester les pré-requis
		_classInclude ('InstallChecker');
		$checker = new InstallChecker ();
		
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('install.title.admin');

		$tips = $this->_getTips ();
		$ppo->tips = $tips->tips;
		$ppo->warning = $tips->warning;
		
		$ppo->homePageUrl = CopixConfig::get ('|homePage');

		$ppo->pdomysqlEnabled = $checker->typeDbInstalled ('pdo_mysql');
		if (!$ppo->pdomysqlEnabled) {
			$ppo->tips[] = _i18n ('install.tips.pdomysql');
		}
	  
		$ppo->phpunitEnabled = is_readable ('PHPUnit/Framework.php');
		if (!$ppo->phpunitEnabled) {
			$ppo->tips[]=_i18n('install.tips.unittest');
		}

		$ppo->databaseEnabled = $checker->isValidDefaultDatabase ();

		if (!$ppo->databaseEnabled) {
			$ppo->tips[] = _i18n ('install.tips.database');
		}
		
		if (!_currentUser ()->testCredential ('basic:admin')) {
			$ppo->tips = array ();
		}
		

		// recherche des liens
		if (CopixRequest::exists ('modules')) {
			$modules = (is_array (_request ('modules'))) ? _request ('modules') : array (_request ('modules'));
			$ppo->links = _class ('admin|adminmenu')->getLinks ($modules);
			$path = array ();
			if (count ($ppo->links) > 0) {
				$links = $ppo->links;
				$link = array_shift ($links);
				$path[_url ('admin||', array ('modules' => $modules))] = $link['caption'];
			} else {
				$path[_url ('admin||', array ('modules' => $modules))] = _i18n ('Inconnu');
			}
			_notify ('breadcrumb', array ('path' => $path));
		} else {
			$ppo->links = _class ('admin|adminmenu')->getLinks ();
		}

		return _arPPO ($ppo, (CopixGroupPreferences::get ('admin|homeStyle', 'small', 'big') == 'small' ? 'admin.php' : 'admin_big.php'));
	}
	
	/**
	 * Réécriture du chemin des classes
	 * 
	 * @return CopixActionReturn
	 */
	public function processRebuildClassPath () {
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
		CopixAutoloader::rebuildClassPath ();
		return _arRedirect (_url ('admin||'));
	}
	
	/**
	 * Retourne le tableau de tips
	 * 
	 * @return object
	 */
	private function _getTips () {
		$checker = _class ('InstallChecker');

		$tips = array ();
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
		$toReturn = new stdClass (); 
		$toReturn->tips = $tips;
		$toReturn->warning = $warning;
		return $toReturn;
	}
}