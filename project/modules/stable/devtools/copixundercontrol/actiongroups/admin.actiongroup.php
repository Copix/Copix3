<?php
/**
 * @package		Copix
 * @subpackage  Devtools
 * @author		Nicolas Bastien
 */

/**
 * Administration du module CopixUnderControl
 *
 * @package		Copix
 * @subpackage  Devtools
 */
class ActionGroupAdmin extends CopixActionGroup {

	
	protected function _beforeAction($pActionName) {
		CopixConfig::instance()->mainTemplate = "module.main.tpl";
		parent::_beforeAction($pActionName);
	}

	/**
	 * Action par défaut
	 */
	public function processDefault (){
		$ppo = _ppo();
		


		


		return _arPPO($ppo, "default.tpl");
	}


	public function processTest () {
		$ppo = _ppo();
		//On vérifie si l'on a des données à afficher
		if (!file_exists(COPIX_TEST_LOG_XML_PATH . 'phpunit.xml')) {
			$ppo->message = "Pour visualiser les résultats, vous devez d'abord lancer les tests unitaire.";
			return _arPPO($ppo, 'error.tpl');
		}
		$ppo->result = CopixUnderControl_Service::parsePHPUnitXMLReport();
		if ($ppo->result === false) {
			$ppo->message = "Le log XML n'est pas valide, cela peut être du à une fatal error lors de l'exécution des tests.";
			return _arPPO($ppo, 'error.tpl');
		}
		
		return _arPPO($ppo, 'test.tpl');
	}

	public function processCodeCoverage () {
		$ppo = _ppo();
		//On vérifie si l'on a des données à afficher
		if (!file_exists(COPIX_TEST_COVERAGE_HTML_PATH . 'index.html')) {
			$ppo->message = "Pour visualiser les résultats, vous devez d'abord lancer les tests unitaire.";
			return _arPPO($ppo, 'error.tpl');
		}
		$ppo->htmlFile = COPIX_TEST_COVERAGE_HTML_PATH . 'index.html';
		return _arPPO($ppo, 'codecoverage.tpl');
	}

	public function processGetCodeCoverageFile () {

		//Nom du ficheir à afficher
		$fileName = _request('file');

		$filePath = COPIX_TEST_COVERAGE_HTML_PATH . $fileName;
		
		$service = new CopixUnderControl_Service();
		$toDisplay = $service->parseFileForHTMLDisplay($filePath, 'getCodeCoverageFile', 'codecoverage');

		return _arContent($toDisplay, 'generictools|blanknohead.tpl');
	}


	public function processDocumentation () {
		$ppo = _ppo();
		//On vérifie si l'on a des données à afficher
		if (!file_exists(COPIX_TEST_COVERAGE_HTML_PATH . 'index.html')) {
			$ppo->message = "Pour visualiser les résultats, vous devez d'abord générer la documentation.";
			return _arPPO($ppo, 'error.tpl');
		}
		$ppo->htmlFile = COPIX_TEST_COVERAGE_HTML_PATH . 'index.html';
		return _arPPO($ppo, 'documentation.tpl');
	}

	public function processGetDocumentationFile () {

		//Nom du ficheir à afficher
		$fileName = _request('file');

		$filePath = COPIX_DOCUMENTATION_PATH . $fileName;

		$service = new CopixUnderControl_Service();
		$toDisplay = $service->parseFileForHTMLDisplay($filePath, 'getDocumentationFile', 'phpdoc');

		return _arContent($toDisplay, 'generictools|blanknohead.tpl');
	}

	public function processPhpDepend () {

		$ppo = _ppo();
		//On vérifie si l'on a des données à afficher
		if (!file_exists(COPIX_PDEPEND_RESULT_PATH . "summary.xml")) {
			$ppo->message = "Pour visualiser les résultats, vous devez d'abord générer la documentation.";
			return _arPPO($ppo, 'error.tpl');
		}

		return _arPPO($ppo, 'phpdepend.tpl');
	}

	public function processGetPhpDependGraph() {
		$file = _request('file');
		if ($file != 'pyramid' && $file != 'jdepend') {
			return _arNone();
		}
		CopixUnderControl_Service::sendFile(COPIX_PDEPEND_RESULT_PATH . $file . '.svg');
		return _arNone();
	}

	public function processCheckstyle() {
		$ppo = _ppo();

		//On vérifie si l'on a des données à afficher
		if (!file_exists(COPIX_SNIFFER_LOG_PATH . 'checkstyle.xml')) {
			$ppo->message = "Pour visualiser les résultats, vous devez d'abord lancer le checkstyle.";
			return _arPPO($ppo, 'error.tpl');
		}
		$ppo->result = CopixUnderControl_Service::parseCodeSnifferXMLReport();
		if ($ppo->result === false) {
			$ppo->message = "Le log XML n'est pas valide, cela peut être du à une fatal error lors de l'exécution des tests.";
			return _arPPO($ppo, 'error.tpl');
		}

		return _arPPO($ppo, 'checkstyle.tpl');
	}
	
	public function processXMLLog() {
		$ppo = _ppo();
		return _arPPO($ppo, 'xmllog.tpl');
	}

	public function processMetrics() {
		$ppo = _ppo();
		return _arPPO($ppo, 'xmllog.tpl');
	}

	public function processPMD () {
		$ppo = _ppo();
		//On vérifie si l'on a des données à afficher
		if (!file_exists(COPIX_TEST_LOG_PMD_PATH . 'phpunit.pmd.xml')
		||	!file_exists(COPIX_TEST_LOG_PMD_PATH . 'phpunit.pmd-cpd.xml')) {
			$ppo->message = "Pour visualiser les résultats, vous devez d'abord lancer les tests.";
			return _arPPO($ppo, 'error.tpl');
		}

		$ppo->result = CopixUnderControl_Service::parsePMDXMLReport();//var_dump($ppo->result);die;
		if ($ppo->result === false) {
			$ppo->message = "Le log XML n'est pas valide.";
			return _arPPO($ppo, 'error.tpl');
		}

		return _arPPO($ppo, 'pmd.tpl');
	}
}