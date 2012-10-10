<?php
/**
 * @package		copix
 * @subpackage	console
 * @author		Nicolas Bastien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @experimental
 */


/**
 * Lancement des tests unitaires
 *
 * @package		copix
 * @subpackage	console
 */
class CopixConsoleTaskTest extends CopixConsoleAbstractTask {

	public $description = 'Lancement des tests unitaires';
	public $requiredArguments = array('module_name' => 'Nom du module a tester ou "all" pour lancer tout les tests existants.');
	public $optionalArguments = array('class_name' => 'Nom de la classe a tester (ne pas inclure ".class.php"');

	/**
	 * (non-PHPdoc)
	 * @see action/CopixConsoleAbstractTask#validate()
	 */
	public function validate () {
		if (!@include_once ('PHPUnit/Framework.php')) {
			throw new CopixException('PHPUnit is required to use UnitTesting under Copix');
		}
		return parent::validate();
	}

	/**
	 * (non-PHPdoc)
	 * @see action/CopixConsoleAbstractTask#execute()
	 */
	public function execute() {
		echo "[test] Lancement des tests unitaires pour le module : {$this->getArgument('module_name')}.\n";

		// Ignore les fichiers de framework de test
		PHPUnit_Util_Filter::addDirectoryToFilter(COPIX_VAR_PATH);
		PHPUnit_Util_Filter::addDirectoryToFilter(COPIX_TEMP_PATH);

		//Création de la suite de test à lancer
		$testSuite = $this->_getTestSuite();

		//Formattage des arguments
		$arOptions = $this->_getOptions();

		//var_dump($testSuite->run());
		require_once('PHPUnit/TextUI/TestRunner.php');
		$result = PHPUnit_TextUI_TestRunner::run($testSuite, $arOptions);
		
		echo "[test] Termine\n";
		return ;
	}

	/**
	 * Construction de la suite de test à lancer
	 * @return PHPUnit_Framework_TestSuite
	 */
	private function _getTestSuite() {

		if ($this->getArgument('module_name') == 'all') {
			//On veut lancer la totalité des tests
			$arModules = CopixModule::getList ();
			$allSuite = new PHPUnit_Framework_TestSuite ('Tous les tests!');
			foreach ($arModules as $moduleName) {
				$moduleSuite = CopixTests::getTestSuiteForModule ($moduleName);
				if (count($moduleSuite) > 0) {
					$allSuite->addTestSuite($moduleSuite);
				}
			}
			return $allSuite;
		}

		if (!is_null($this->getArgument('class_name'))) {
			//On veut lancer une classe de tests en particulier
			$filename = strtolower($this->getArgument('class_name')) . 'test.class.php';
			$filepath = CopixModule::getPath ($this->getArgument('module_name')).'tests/' .$filename;
			if (file_exists($filepath)) {
				$classSuite = new PHPUnit_Framework_TestSuite ('Classe '.$this->getArgument('module_name') . ' / '. $filename);
				require_once ($filepath);
				$classSuite->addTestSuite ($this->getArgument('class_name').'test');
				return $classSuite;
			} else {
				echo "[Erreur] Classe introuvable : '$filename'. Prefixer par 'dao' ou 'daorecord' le cas echeant.\n";
				die();
			}
		}

		//On retourne une suite de tests composé des tous les tests du module
		return CopixTests::getTestSuiteForModule ($this->getArgument('module_name'));
	}

	/**
	 * Création du tableau d'argument à passé au test
	 * Simule les options de la ligne de commande
	 *
	 * On peut retrouver le descriptif des options possibles dans la méthode "handleArguments"
	 * de la classe PHPUnit_TextUI_Command
	 *
	 * @return array
	 */
	private function _getOptions() {

		$arOptions = array();

		//--log-xml
		CopixFile::createDir(COPIX_TEST_LOG_XML_PATH);
		$arOptions['xmlLogfile'] = COPIX_TEST_LOG_XML_PATH . 'phpunit.xml';

		if (extension_loaded('xdebug')) {
			//--log-pmd
			CopixFile::createDir(COPIX_TEST_LOG_PMD_PATH);
			$arOptions['pmdXML'] = COPIX_TEST_LOG_PMD_PATH . 'phpunit.pmd.xml';

			//--log-metrics
			CopixFile::createDir(COPIX_TEST_LOG_METRICS_PATH);
			$arOptions['metricsXML'] = COPIX_TEST_LOG_METRICS_PATH . 'phpunit.metrics.xml';

			//--coverage-html
			CopixFile::createDir(COPIX_TEST_COVERAGE_HTML_PATH);
			$arOptions['reportDirectory'] = COPIX_TEST_COVERAGE_HTML_PATH;
		}

		$arOptions['logIncompleteSkipped'] = true;
		$arOptions['stopOnFailure'] = false;

		return $arOptions;
	}

}