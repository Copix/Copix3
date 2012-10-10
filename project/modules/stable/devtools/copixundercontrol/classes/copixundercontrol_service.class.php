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
class CopixUnderControl_Service {

	/**
	 * Parsing d'un fichier local pour le rendre accessible via une url
	 *
	 * @param string $filePath le chemin vers le ficheir
	 * @param string $urlAction l'action qui effectuera le parsing
	 * @param string $wwwFolder le sous-répertoire où sont stockées les ressources
	 * @return string le contenu du ficheir formatter
	 */
	public function parseFileForHTMLDisplay ($filePath, $urlAction, $wwwFolder) {
		if (!file_exists($filePath)) {
			return false;
		}
		$toReturn = file_get_contents($filePath);

		$toReturn = str_replace('charset=ISO-8859-1', 'charset=utf-8', $toReturn);
	
		//Modification des liens vers les ressources
		switch ($wwwFolder) {
			case 'codecoverage':
				$toReturn = str_replace('href="style.css"', 'href="' . CopixUrl::getResource ("copixundercontrol|css/$wwwFolder/style.css").'"', $toReturn);
				$toReturn = str_replace('src="', 'src="' . CopixUrl::getResource ("copixundercontrol|img/$wwwFolder/"), $toReturn);
				break;
			case 'phpdoc':
				//Entête du document
				$toReturn = str_replace('<?xml version="1.0" encoding="iso-8859-1"?>', '', $toReturn);
				$toReturn = str_replace('<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//FR"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
					'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
					$toReturn);
				
				//Lien vers les différents package
				$toReturn = str_replace('<option value="li_', '<option value="' . CopixUrl::get ("copixundercontrol|admin|$urlAction") . '?file=li_', $toReturn);

				$toReturn = str_replace('href="media/stylesheet.css"', 'href="' . CopixUrl::getResource ("copixundercontrol|css/$wwwFolder/stylesheet.css").'"', $toReturn);
				$toReturn = str_replace('href="../media/stylesheet.css"', 'href="' . CopixUrl::getResource ("copixundercontrol|css/$wwwFolder/stylesheet.css").'"', $toReturn);
				$toReturn = str_replace('href="../../media/stylesheet.css"', 'href="' . CopixUrl::getResource ("copixundercontrol|css/$wwwFolder/stylesheet.css").'"', $toReturn);
				$toReturn = str_replace('href="media/banner.css"', 'href="' . CopixUrl::getResource ("copixundercontrol|css/$wwwFolder/banner.css").'"', $toReturn);
				$toReturn = str_replace('href="../media/banner.css"', 'href="' . CopixUrl::getResource ("copixundercontrol|css/$wwwFolder/banner.css").'"', $toReturn);
				$toReturn = str_replace('href="../../media/banner.css"', 'href="' . CopixUrl::getResource ("copixundercontrol|css/$wwwFolder/banner.css").'"', $toReturn);

				$toReturn = str_replace("<FRAME src='", "<FRAME src='" . CopixUrl::get ("copixundercontrol|admin|$urlAction") . '?file=', $toReturn);

				//Les images
				$toReturn = str_replace('src="media/images/', 'src="' . CopixUrl::getResource ("copixundercontrol|img/$wwwFolder/"), $toReturn);
				$toReturn = str_replace('src="../media/images/', 'src="' . CopixUrl::getResource ("copixundercontrol|img/$wwwFolder/"), $toReturn);
				$toReturn = str_replace('src="../../media/images/', 'src="' . CopixUrl::getResource ("copixundercontrol|img/$wwwFolder/"), $toReturn);
				$toReturn = str_replace('src="../../../media/images/', 'src="' . CopixUrl::getResource ("copixundercontrol|img/$wwwFolder/"), $toReturn);

				$toReturn = str_replace('<a target="right" href="', '<a target="right" href="' . CopixUrl::get ("copixundercontrol|admin|$urlAction") . '?file=', $toReturn);
				break;
			default:
				break;
		}
		
		//Modification des urls
		$toReturn = str_replace('<a href="#', '<a #href="#', $toReturn);
		$toReturn = str_replace('<a href=\'#', '<a #href=\'#', $toReturn);
		$toReturn = str_replace('<a href="', '<a href="' . CopixUrl::get ("copixundercontrol|admin|$urlAction") . '?file=', $toReturn);
		$toReturn = str_replace('<a href=\'', '<a href=\'' . CopixUrl::get ("copixundercontrol|admin|$urlAction") . '?file=', $toReturn);
		$toReturn = str_replace('<a #href="#', '<a href="#', $toReturn);
		$toReturn = str_replace('<a #href=\'#', '<a href=\'#', $toReturn);

		return $toReturn;
	}

	/**
	 * Lit le fichier image $pFilePath, permet d'afficher des images qui ne sont pas accessible par CopixResource
	 *
	 * @param string $pFilePath le chemin du ficheir
	 * @return void
	 */
	public static function sendFile ($pFilePath) {

		$mimeType = CopixMIMETypes::getFromFileName($pFilePath);

		header("Cache-Control: public");
		header("Date: ".gmdate("r"));
		header("Last-Modified: ".gmdate("r", filemtime($pFilePath)));
		header('Content-Type: '.$mimeType);
		header('Content-Length: '.filesize($pFilePath));

		// Vérification de la date de modification
		if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			$time = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
			if($time !== false && filemtime($pFilePath) <= $time) {
				header("304 Not Modified", null, 304);
				return;
			}
		}

		// On ne fait le md5_file qu'ici, car il consomme un peu plus de CPU
		header('ETag: '.md5_file($pFilePath));

		readfile($pFilePath, false);
		ob_end_flush();
		return;
	}

	/**
	 * Parse les rapport XML de PHPUnit pour extraire les données
	 *
	 * @return object $toReturn les données du rapport à transmettre au template
	 */
	public static function parsePHPUnitXMLReport () {

		if (!is_file(COPIX_TEST_LOG_XML_PATH . 'phpunit.xml')){
			return false;
		}

		$arResult = array();

		$xml = simplexml_load_file(COPIX_TEST_LOG_XML_PATH . 'phpunit.xml');
		if ($xml === false) {
			return false;
		}

		foreach ($xml->testsuite as $xmlSuite) {
			$objSuite = self::parseSuiteXml($xmlSuite);
			$arResult[] = $objSuite;
		}
		//var_dump($result_tab);die;

		return $arResult;

	}

	/**
	 * Parsing d'une suite de test au format SimpleXMLElement
	 *
	 * @param SimpleXMLElement $pXMLSuite
	 * @return object $suite_obj
	 */
	public static function parseSuiteXml($pXMLSuite){

		$suite = self::makeObjectSuite($pXMLSuite);
		foreach ($pXMLSuite->testsuite as $xmlSousSuite) {
			$sousSuite = self::parseSuiteXml($xmlSousSuite);
			$suite->testSuite[] = $sousSuite;
		}
		foreach ($pXMLSuite->testcase as $testCase) {
			$testCase = self::makeObjectTestCase($testCase);
			$suite->testCase[] = $testCase;
		}

		return $suite;

	}//parseSuiteXml


	/**
	 * Création d'un objet suite
	 *
	 * @param SimpleXMLElement $pXMLSuite
	 * @return $suite
	 */
	public static function makeObjectSuite($pXMLSuite){

		$suite = new stdClass();
		$suite->name 		= (string)$pXMLSuite['name'];
		$suite->tests		= (int)$pXMLSuite['tests'];
		$suite->assertions 	= (int)$pXMLSuite['assertions'];
		$suite->failures 	= (int)$pXMLSuite['failures'];
		$suite->errors		= (int)$pXMLSuite['errors'];
		$suite->time		= (string)$pXMLSuite['time'];
		$suite->testSuite 	= array();
		$suite->testCase 	= array();

		return $suite;

	}//makeObjectSuite


	/**
	 * Création d'un objet test case
	 *
	 * @param SimpleXMLElement $pXMLTestCase
	 * @return $testCase
	 */
	public static function makeObjectTestCase($pXMLTestCase){

		$testCase = new stdClass();
		$testCase->name 		= (string)$pXMLTestCase['name'];
		$testCase->line 		= (int)$pXMLTestCase['line'];
		$testCase->assertions 	= (int)$pXMLTestCase['assertions'];
		$testCase->time			= (string)$pXMLTestCase['time'];

		foreach ($pXMLTestCase->failure as $failure) {
			if ((string)$failure['type'] == 'PHPUnit_Framework_ExpectationFailedException') {
				$strFailure = 'Expectation Failed';
			} elseif ((string)$failure['type'] == 'PHPUnit_Framework_AssertionFailedError ') {
				$strFailure = 'Assertion Failed';
			} else {
				$strFailure = (string)$failure['type'];
			}
			$testCase->failure = $strFailure;
		}
		foreach ($pXMLTestCase->error as $error) {
			if ((string)$error['type'] == 'PHPUnit_Framework_IncompleteTestError') {
				$testCase->error = 'Incomplete Test';
			} else {
				$testCase->error = (string)$error['type'];
			}
		}

		return $testCase;
	}//makeObjectTestCase


	public static function parseCodeSnifferXMLReport() {
		$arResult = array();

		$xml = simplexml_load_file(COPIX_SNIFFER_LOG_PATH . 'checkstyle.xml');
		if ($xml === false) {
			return false;
		}

		foreach ($xml->file as $xmlFile) {
			$file = self::parseFileXml($xmlFile);
			$arResult[] = $file;
		}
		
		return $arResult;
	}

	public static function parseFileXml ($xmlFile) {
		$file = new stdClass();
		$file->path = (string)$xmlFile['name'];
		$file->name = CopixFile::extractFileName($file->path);
		$file->errors = (string)$xmlFile['errors'];
		$file->warnings = (string)$xmlFile['warnings'];
		
		$file->arErrors = array();
		foreach ($xmlFile->error as $xmlError) {
			$error = new stdClass();
			$error->line = (string)$xmlError['line'];
			$error->column = (string)$xmlError['column'];
			$error->source = (string)$xmlError['source'];
			$error->string = (string)$xmlError;
			$file->arErrors[] = $error;
		}

		return $file;
	}

	public static function parsePMDXMLReport() {
		$arResultDuplication = array();

//		$xml = simplexml_load_file(COPIX_TEST_LOG_PMD_PATH . 'phpunit.pmd-cpd.xml');
//		if ($xml === false) {
//			return false;
//		}
//		foreach ($xml->duplication as $xmlDuplication) {
//			$duplication = self::parseDuplicationXml($xmlDuplication);
//			$arResultDuplication[] = $duplication;
//		}

		$xml = simplexml_load_file(COPIX_TEST_LOG_PMD_PATH . 'phpunit.pmd.xml');
		if ($xml === false) {
			return false;
		}
		$arResultViolation = array();
		foreach ($xml->violation as $xmlViolation) {
			$violation = new stdClass();
			$violation->rule = $xmlViolation['rule'];
			$violation->package = $xmlViolation['package'];
			$violation->string = (string)$xmlViolation;
			$arResultViolation[] = $violation;
		}
		$arResultPmd = array();
		foreach ($xml->file as $xmlFile) {
			$file = self::parsePMDFileXml($xmlFile);
			$arResultPmd[] = $file;
		}

		$result = new stdClass();
		//$result->duplications = $arResultDuplication;
		$result->violation = $arResultViolation;
		$result->pmd = $arResultPmd;

		return $result;
	}

	public static function parseDuplicationXml ($xmlDuplication) {
		$duplication = new stdClass();
		$duplication->lines = (string)$xmlDuplication['lines'];
		$duplication->tokens = (string)$xmlDuplication['tokens'];
		$duplication->files = array();
		foreach ($xmlDuplication->file as $xmlFile) {
			$file = new stdClass();
			$file->path = (string)$xmlFile['path'];
			$file->name = CopixFile::extractFileName($file->path);
			$file->line = (string)$xmlFile['line'];
			$duplication->files[] = $file;
		}
		$duplication->codefragment = highlight_string("<?php\n" .$xmlDuplication->codefragment, true);
		
		return $duplication;
	}

	public static function parsePMDFileXml($xmlFile) {
		$file = new stdClass();
		$file->path = (string)$xmlFile['name'];
		$file->name = CopixFile::extractFileName($file->path);
		
		$file->arViolations = array();
		foreach ($xmlFile->violation as $xmlViolation) {
			$violation = new stdClass();
			$violation->rule = (string)$xmlViolation['rule'];
			$violation->line = (string)$xmlViolation['line'];
			$violation->toline = (string)$xmlViolation['to-line'];
			$violation->package = (string)$xmlViolation['package'];
			$violation->class = (string)$xmlViolation['class'];
			$violation->method = (string)$xmlViolation['method'];
			$violation->string = (string)$xmlViolation;
			$file->arViolations[] = $violation;
		}
		return $file;
	}
	
}