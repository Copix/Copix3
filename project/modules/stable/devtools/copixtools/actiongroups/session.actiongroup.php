<?php
/**
 * @package standard
 * @subpackage copixtools
 * @author Gérald Croës, Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 */

/**
 * Actions sur la session
 * 
 * @package standard
 * @subpackage copixtools 
 */
class ActionGroupSession extends CopixActionGroup {
	/**
	 * Executée avant l'action
	 * 
	 * @param string $pActionName Nom de l'action
	 */
	public function beforeAction ($pActionName) {
		CopixPage::add ()->setIsAdmin (true);
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}
	
	/**
	 * Affichage du contenu de la session
	 * 
	 * @return CopixActionReturn
	 */
	public function processDefault () {
		$showNamespace = (CopixRequest::exists ('showNamespace')) ? _request ('showNamespace') : _sessionGet ('session|showNamespace', 'copixtools');
		$url = (is_null ($showNamespace)) ? '#' : _url ('copixtools|session|', array ('showNamespace' => null));
		_notify ('breadcrumb', array ('path' => array ($url => _i18n ('session.breadcrumb.show'))));
		
		_sessionSet ('session|showNamespace', $showNamespace, 'copixtools');
		if (!is_null ($showNamespace)) {
			_notify ('breadcrumb', array ('path' => array ('#' => _i18n ('session.breadcrumb.showNamespace', $showNamespace))));
		}
		
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('session.title');

		$ppo->arSessionCopix = array ();
		$namespaces = (!is_null ($showNamespace)) ? array ($showNamespace) : CopixSession::getNamespaces ();
		foreach ($namespaces as $namespace) {
			$ppo->arSessionCopix[$namespace] = CopixSession::getVariables ($namespace);
		}

		$ppo->arSession = $_SESSION;
		unset ($ppo->arSession[CopixConfig::instance ()->copixsession_key]);
		return _arPpo ($ppo, 'session.show.tpl');
	}
	
	/**
	 * Supression de la session Copix
	 * 
	 * @return CopixActionReturn
	 */
	public function processDeleteSession () {
		CopixSession::destroy ();
		return _arRedirect (_url ('copixtools|session|'));
	}

	/**
	 * Supression d'une variable de session
	 * 
	 * @return CopixActionReturn
	 */
	public function processDelete () {
		$namespace = CopixRequest::get ('namespace');
		$var = CopixRequest::get ('var');

		if ($namespace !== null) {
			if ($var !== null) {
				CopixSession::delete ($var, $namespace);
			} else {
				CopixSession::destroyNamespace ($namespace);
			}
		} else {
			if (isset ($_SESSION[$var])) {
				unset ($_SESSION[$var]);
			}
		}

		return _arRedirect (_url ('copixtools|session|'));
	}
}