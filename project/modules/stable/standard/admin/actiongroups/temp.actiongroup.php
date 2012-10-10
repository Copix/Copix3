<?php
/**
 * @package standard
 * @subpackage admin
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license	http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 */

/**
 * Gestion des fichiers temporaires et de cache
 *
 * @package standard
 * @subpackage admin
 */
class ActionGroupTemp extends CopixActionGroup {
	/**
	 * Executé avant toute action
	 *
	 * @param string $pActionName Nom de l'action
	 */
	protected function _beforeAction ($pActionName) {
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
		CopixPage::add ()->setIsAdmin (true);
		_notify ('breadcrumb', array ('path' => array (_url ('admin|temp|') => 'Fichiers temporaires')));
	}

	/**
	 * Informations sur le répertoire temporaire
	 *
	 * @return CopixActionReturn
	 */
	public function processDefault () {
		$ppo = new CopixPPO (array ('TITLE_PAGE' => 'Fichiers temporaires'));
		$ppo->infos = CopixTemp::getInformations ();
		$ppo->permissions = fileperms (COPIX_TEMP_PATH);
		return _arPPO ($ppo, 'admin|temp/infos.php');
	}

	/**
	 * Confirmation de suppression de tous les fichiers temporaires
	 *
	 * @return CopixActionReturn
	 */
	public function processClear () {
		_notify ('breadcrumb', array ('path' => array (_url ('admin|temp|clear') => 'Tout supprimer')));
		return CopixActionGroup::process ('generictools|Messages::getConfirm', array (
			'message' => 'Etes-vous sur de vouloir supprimer TOUS les fichiers temporaires ?',
			'confirm' => _url ('admin|temp|doClear'),
			'cancel' => _url ('admin|temp|')
		));
	}

	/**
	 * Effectue la suppression de tous les fichiers temporaires
	 *
	 * @return CopixActionReturn
	 */
	public function processDoClear () {
		_notify ('breadcrumb', array ('path' => array (_url ('admin|temp|clear') => 'Tout supprimer')));
		CopixTemp::clear ();
		
		$links = array ();
		$redirectUrl = null;
		if (CopixRequest::exists ('url_return')) {
			$links[_request ('url_return')] = 'Retour à la page précédente';
			$redirectUrl = _request ('url_return');
		}
		$links[_url ('admin||')] = 'Retour à l\'administration';
		$params = array (
			'title' => 'Fichiers temporaires supprimés',
			'message' => 'Tous les fichiers temporaires ont été supprimés.',
			'links' => $links,
			'redirect_url' => $redirectUrl
		);
		return CopixActionGroup::process ('generictools|messages::getInformation', $params);
	}

	/**
	 * Retourne le nombre de fichiers et la taille du répertoire temporaire
	 *
	 * @return CopixActionReturn
	 */
	public function processGetFiles () {
		$ppo = new CopixPPO ();
		$files = CopixFile::findFiles (COPIX_TEMP_PATH);
		$size = 0;
		foreach ($files as $file) {
			$size += filesize ($file);
		}
		$ppo->count = count ($files);
		$ppo->size = $size;
		return _arPPO ($ppo, 'admin|temp/files.php');
	}

	/**
	 * Choix du module pour supprimer ses fichiers temporaires
	 *
	 * @return CopixActionReturn
	 */
	public function processClearModule () {
		_notify ('breadcrumb', array ('path' => array (_url ('admin|temp|clearModule') => 'Par module')));
		$ppo = new CopixPPO (array ('TITLE_PAGE' => 'Par module'));
		$ppo->modules = array ();
		$modules = CopixModule::getList ();
		foreach ($modules as $module) {
			$infos = CopixModule::getInformations ($module);
			$ppo->modules[$module] = '[' . $module . '] ' . $infos->getDescription ();
		}
		$ppo->modules['templates'] = '[templates] Templates compilés par smarty';
		ksort ($ppo->modules);
		return _arPPO ($ppo, 'admin|temp/clear.module.php');
	}

	/**
	 * supprime les fichiers temporair ed'un module
	 *
	 * @return CopixActionReturn
	 */
	public function processDoClearModule () {
		$module = _request ('moduleName');
		_notify ('breadcrumb', array ('path' => array (
			_url ('admin|temp|clear') => 'Par module'),
			_url ('admin|temp|doClearModule', array ('moduleName' => $module)) => $module
		));
		if ($module == 'templates') {
			CopixTemp::clear ('cache/templates/');
		} else {
			CopixTemp::clearModule ($module);
		}

		$params = array (
			'title' => 'Fichiers temporaires supprimés',
			'message' => 'Les fichiers temporaires du module "' . $module . '" ont été supprimés.',
			'links' => array (_url ('admin||') => 'Retour à l\'administration')
		);
		return CopixActionGroup::process ('generictools|messages::getInformation', $params);
	}

	/**
	 * Débloque l'accès en écriture du répertoire temporaire
	 *
	 * @return CopixActionReturn
	 */
	public function processUnlock () {
		CopixTemp::unlock ();

		$params = array (
			'title' => 'Accès débloqué',
			'message' => 'L\'accès en écriture au répertoire temporaire est débloqué.',
			'links' => array (
				_url ('admin|temp|') => 'Retour à la gestion du répertoire temporaire',
				_url ('admin||') => 'Retour à l\'administration'
			)
		);
		return CopixActionGroup::process ('generictools|messages::getInformation', $params);
	}
}