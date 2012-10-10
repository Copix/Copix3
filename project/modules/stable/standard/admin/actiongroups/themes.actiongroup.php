<?php
/**
 * @package standard
 * @subpackage admin
 * @author Gérald Croës, Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 */

/**
 * Gestion des thèmes
 * 
 * @package standard
 * @subpackage admin 
 */
class ActionGroupThemes extends CopixActionGroup {
	/**
	 * Executé avant toute action
	 *
	 * @param string $pActionName Nom de l'action
	 */
	public function beforeAction ($pActionName) {
		CopixPage::add ()->setIsAdmin (true);
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}

	/**
	 * Affiche la liste des thèmes disponibles
	 * 
	 * @return CopixActionReturn
	 */
	public function processDefault () {
		_notify ('breadcrumb', array ('path' => array ('#' => _i18n ('themes.titlePage.selectTheme'))));
		
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('themes.titlePage.selectTheme');

		$ppo->publicTheme = CopixTpl::getThemeInformations (CopixConfig::get ('default|publicTheme'));
		$ppo->adminTheme = CopixTpl::getThemeInformations (CopixConfig::get ('default|adminTheme'));
		
		$arThemes = CopixTheme::getList (true);
		$arThemesInfos = array ();
		$ppo->arThemes = array ();
		foreach ($arThemes as $theme => $caption) {
			if ($theme != $ppo->publicTheme->getId () && $theme != $ppo->adminTheme->getId ()) {
				$ppo->arThemes[] = CopixTheme::getInformations ($theme);
			}
		}
		return _arPPO ($ppo, 'theme/list.php');
	}

	/**
	 * Définit le thème à utiliser
	 * 
	 * @return CopixActionReturn
	 */
	public function processDoSelectTheme () {
		if (_request ('type') != null) {
			CopixConfig::set ('default|' . _request ('type') . 'Theme', _request ('theme', 'default'));
		} else {
			CopixConfig::set ('default|publicTheme', _request ('theme', 'default'));
			CopixConfig::set ('default|adminTheme', _request ('theme', 'default'));
		}
		return _arRedirect (_url ('admin|themes|'));
	}

	/**
	 * Confirmation d'optimisation
	 *
	 * @return CopixActionReturn
	 */
	public function processOptimize () {
		$ppo = new CopixPPO ();
		$ppo->theme = CopixTPL::getThemeInformations (_request ('theme'));
		$ppo->is_writable = is_writable ('themes/');
		return _arPPO ($ppo, 'theme/optimize.php');
	}

	/**
	 * Effectue l'optimisation du thème
	 *
	 * @return CopixActionReturn
	 */
	public function processDoOptimize () {
		CopixTheme::optimize (_request ('theme'));

		$params = array (
			'title' => _i18n ('Optimisation terminée.'),
			'redirect_url' => _url ('admin|themes|'),
			'message' => _i18n ('Le thème "' . _request ('theme') . '" a été optimisé.'),
			'links' => array (
				_url ('admin|themes|') => _i18n ('Retour aux thèmes')
			)
		);
		return CopixActionGroup::process ('generictools|messages::getInformation', $params);
	}
	
	/**
	 * Prévisualisation
	 * 
	 * @return CopixActionGroup
	 */
	public function processPreview () {
		$theme = CopixTpl::getThemeInformations (_request ('theme'));
		CopixTpl::setTheme ($theme->getId ());
		
		_notify ('breadcrumb', array ('path' => array (
			'admin|themes|default' => _i18n ('themes.titlePage.selectTheme'),
			'admin|themes|' => $theme->getName (),
			_url ('admin|themes|preview', array ('theme' => $theme->getId ())) => _i18n ('themes.titlePage.preview')
		)));
		
		$ppo = _ppo (array ('TITLE_PAGE' => 'Prévisualisation'));
		$ppo->theme = _request ('theme');
		return _arPPO ($ppo, 'theme/preview.php');
	}
	
	/**
	 * Prévisualisation de l'exception
	 * 
	 * @throws CopixException
	 */
	public function processPreviewException () {
		$theme = CopixTpl::getThemeInformations (_request ('theme'));
		CopixTpl::setTheme ($theme->getId ());
		
		_notify ('breadcrumb', array ('path' => array (
			'admin|themes|default' => _i18n ('themes.titlePage.selectTheme'),
			'admin|themes|' => $theme->getName (),
			_url ('admin|themes|preview', array ('theme' => $theme->getId ())) => _i18n ('themes.titlePage.preview'),
			_url ('admin|themes|PreviewException', array ('theme' => $theme->getId ())) => 'Exception'
		)));
		
		throw new CopixException ('Prévisualisation de l\'exception.');
	}
	
	/**
	 * Prévisualisation de GetError
	 * 
	 * @return CopixActionReturn
	 */
	public function processPreviewGetError () {
		$theme = CopixTpl::getThemeInformations (_request ('theme'));
		CopixTpl::setTheme ($theme->getId ());
		
		_notify ('breadcrumb', array ('path' => array (
			'admin|themes|default' => _i18n ('themes.titlePage.selectTheme'),
			'admin|themes|' => $theme->getName (),
			_url ('admin|themes|preview', array ('theme' => $theme->getId ())) => _i18n ('themes.titlePage.preview'),
			_url ('admin|themes|PreviewGetError', array ('theme' => $theme->getId ())) => 'GetError'
		)));
		
		$params = array (
			'message' => array ('Ligne d\'erreur 1', 'Ligne d\'erreur 2', 'Ligne d\'erreur 3')
		);
		return CopixActionGroup::process ('generictools|messages::GetError', $params);
	}
	
	/**
	 * Prévisualisation de GetConfirm
	 * 
	 * @return CopixActionReturn
	 */
	public function processPreviewGetConfirm () {
		$theme = CopixTpl::getThemeInformations (_request ('theme'));
		CopixTpl::setTheme ($theme->getId ());
		
		_notify ('breadcrumb', array ('path' => array (
			'admin|themes|default' => _i18n ('themes.titlePage.selectTheme'),
			'admin|themes|' => $theme->getName (),
			_url ('admin|themes|preview', array ('theme' => $theme->getId ())) => _i18n ('themes.titlePage.preview'),
			_url ('admin|themes|PreviewGetConfirm', array ('theme' => $theme->getId ())) => 'GetConfirm'
		)));
		
		$params = array (
			'title' => 'Message de confirmation',
			'message' => 'Etes-vous sur de vouloir ne rien faire ?',
			'confirm' => _url ('admin|themes|preview', array ('theme' => $theme->getId ())),
			'cancel' => _url ('admin|themes|preview', array ('theme' => $theme->getId ()))
		);
		return CopixActionGroup::process ('generictools|messages::GetConfirm', $params);
	}
	
	/**
	 * Prévisualisation de GetInformation
	 * 
	 * @return CopixActionReturn
	 */
	public function processPreviewGetInformation () {
		$theme = CopixTpl::getThemeInformations (_request ('theme'));
		CopixTpl::setTheme ($theme->getId ());
		
		_notify ('breadcrumb', array ('path' => array (
			'admin|themes|default' => _i18n ('themes.titlePage.selectTheme'),
			'admin|themes|' => $theme->getName (),
			_url ('admin|themes|preview', array ('theme' => $theme->getId ())) => _i18n ('themes.titlePage.preview'),
			_url ('admin|themes|PreviewGetInformation', array ('theme' => $theme->getId ())) => 'GetInformation'
		)));
		
		$params = array (
			'title' => 'Message d\'information',
			'message' => 'Contenu du message d\'information',
			'links' => array (
				_url ('admin|themes|preview', array ('theme' => $theme->getId ())) => 'Retour à la prévisualisation'
			)
		);
		return CopixActionGroup::process ('generictools|messages::GetInformation', $params);
	}
}