<?php
/**
 * @package devtools
 * @subpackage admingenerator
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 */

/**
 * Création d'une classe avec propriétés / setteurs / getteurs
 *
 * @package devtools
 * @subpackage admingenerator
 */
class ActionGroupClassGenerator extends CopixActionGroup {
	/**
	 * Executée avant toute action
	 *
	 * @param string $pAction Nom de l'action
	 */
	protected function _beforeAction ($pAction) {
		CopixPage::add ()->setIsAdmin (true);
		_currentUser ()->assertCredential ('basic:admin');
		_notify ('breadcrumb', array ('path' => array ('admingenerator|generateclass|' => 'Génération d\'une classe')));
	}

	/**
	 * Formulaire de génération d'une classe
	 *
	 * @return CopixActionReturn
	 */
	public function processDefault () {
		$ppo = new CopixPPO (array ('TITLE_PAGE' => 'Génération d\'une classe'));
		$ppo->modules = array ();
		foreach (CopixModule::getList () as $module) {
			$ppo->modules[$module] = $module;
		}
		ksort ($ppo->modules);

		if (_request ('object') != null) {
			$ppo->generator = CopixSession::get ('classgenerator|object|' . _request ('object'), 'admingenerator');
		} else {
			$ppo->generator = new ClassGenerator ();
		}

		if (_request ('error') != null) {
			$ppo->errors = CopixSession::get ('classgenerator|errors|' . _request ('error'), 'admingenerator');
		} else {
			$ppo->errors = array ();
		}
		$ppo->types = _ioClass ('PHPGenerator')->getTypes ();
		return _arPPO ($ppo, 'classgenerator/form.php');
	}

	/**
	 * Génère la classe
	 *
	 * @return CopixActionReturn
	 */
	public function processGenerate () {
		_notify ('breadcrumb', array ('path' => array ('#' => 'Génération')));
		$generator = new ClassGenerator ();
		$generator->setModule (_request ('moduleName'));
		$generator->setDirectory (_request ('directory'));
		$generator->setClassName (_request ('className'));
		foreach (CopixRequest::asArray () as $name => $value) {
			if (substr ($name, 0, 5) == 'name_' && $value != null) {
				$id = substr ($name, 4);
				$generator->addProperty ($value, _request ('comment' . $id),  _request ('type' . $id), _request ('value' . $id));
			}
		}

		$sessionId = uniqid ();
		CopixSession::set ('classgenerator|object|' . $sessionId, $generator, 'admingenerator');

		try {
			$generator->generate ();
		} catch (CopixException $e) {
			$isValid = $generator->isValid ();
			if ($isValid === true) {
				CopixSession::set ('classgenerator|errors|' . $sessionId, $e->getMessage (), 'admingenerator');
			} else {
				CopixSession::set ('classgenerator|errors|' . $sessionId, $isValid->asArray (), 'admingenerator');
			}
			
			return _arRedirect (_url ('admingenerator|classgenerator|', array ('error' => $sessionId, 'object' => $sessionId)));
		}

		$params = array (
			'title' => 'Génération effectuée',
			'message' => 'La génération de la classe "' . $generator->getClassName () . '" a été effectuée.',
			'links' => array (
				_url ('admingenerator|classgenerator|', array ('object' => $sessionId)) => 'Modifier la classe',
				_url ('admin||') => 'Retour à l\'accueil de l\'administration'
			)
		);
		return CopixActionGroup::process ('generictools|messages::getInformation', $params);
	}
}