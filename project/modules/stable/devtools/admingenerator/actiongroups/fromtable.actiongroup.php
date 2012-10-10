<?php
/**
 * @package devtools
 * @subpackage admingenerator
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Création d'une interface d'admin depuis une table
 *
 * @package devtools
 * @subpackage admingenerator
 */
class ActionGroupFromTable extends CopixActionGroup {
	/**
	 * executée avant toute action
	 *
	 * @param string $pAction Nom de l'action
	 */
	protected function _beforeAction ($pAction) {
		CopixPage::add ()->setIsAdmin (true);
		_currentUser ()->assertCredential ('basic:admin');
		_notify ('breadcrumb', array ('path' => array ('admingenerator|fromtable|' => 'Génération admin depuis une table')));
	}

	/**
	 * Formulaire de création d'interface d'admin
	 *
	 * @return CopixActionReturn
	 */
	public function processDefault () {
		$config = CopixConfig::instance ();

		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = 'Génération admin depuis une table';

		$ppo->db_profiles = array ();
		foreach ($config->copixdb_getProfiles () as $profile) {
			$ppo->db_profiles[$profile] = $profile;
		}
		$ppo->db_defaultProfile = $config->copixdb_getDefaultProfileName ();

		$ppo->modules = array ();
		foreach (CopixModule::getList () as $module) {
			$ppo->modules[$module] = $module;
		}
		ksort ($ppo->modules);

		return _arPPO ($ppo, 'fromtable/form.php');
	}

	/**
	 * Appel ajax pour avoir un select des tables du profil
	 *
	 * @return CopixActionReturn
	 */
	public function processGetTables () {
		$select = array ();
		foreach (CopixDB::getConnection (_request ('profile'))->getTableList () as $table) {
			$select[$table] = $table;
		}
		$html = _tag ('select', array ('name' => 'table', 'values' => $select, 'emptyShow' => false, 'selected' => CopixSession::get ('table', 'generatorfromtable')));
		$url = _url ('admingenerator|fromtable|getFields');
		$js = <<<JS
		$ ('table').addEvent ('change', function () {
			new Request.HTML ({
				url : '$url',
				evalScripts: true,
				update : $ ('fields')
			}).post ({'profile' : $ ('profile').value, 'table' : $ ('table').value});
		});
		$ ('table').fireEvent ('change');
JS;
		$html .= '<script type="text/javascript">' . $js . '</script>';
		return _arString ($html);
	}

	/**
	 * Appel ajax pour avoir les champs de la table
	 *
	 * @return CopixActionReturn
	 */
	public function processGetFields () {
		$ppo = new CopixPPO ();
		$ppo->fields = GeneratorFromTable::getFields (_request ('profile'), _request ('table'));
		return _arDirectPPO ($ppo, 'fromtable/fields.php');
	}

	/**
	 * Génération du code PHP
	 *
	 * @return CopixActionReturn
	 */
	public function processGenerate () {
		$params = CopixRequest::asArray ();

		$errors = _validator ('admingenerator|GeneratorValidator')->check ($params);
		if ($errors instanceof CopixErrorObject) {
			$ppo = new CopixPPO (array ('MAIN' => _tag ('error', array ('message' => implode ('<br />', $errors->asArray ())))));
			return _arDirectPPO ($ppo, 'generictools|blanknohead.tpl');
		}

		$generator = new GeneratorFromTable ($params);
		if (_request ('exception_generate')) {
			$generator->generateException ();
		}
		if (_request ('info_generate')) {
			$generator->generateMainClass ();
		}
		if (_request ('validator_generate')) {
			$generator->generateValidator ();
		}
		if (_request ('service_generate')) {
			$generator->generateService ();
		}
		if (_request ('actiongroup_generate')) {
			$generator->generateActiongroupAdmin ();
		}
		if (_request ('search_generate')) {
			$generator->generateSearchClass ();
		}
		CopixTemp::clear ();
		
		return _arString ('Génération OK : ' . uniqid ());
	}

	/**
	 * Sauvegarde les saisies en session
	 *
	 * @return CopixActionReturn
	 */
	public function processSave () {
		foreach (CopixRequest::asArray () as $name => $value) {
			CopixSession::set ($name, $value, 'generatorfromtable');
		}
		return _arNone ();
	}
}