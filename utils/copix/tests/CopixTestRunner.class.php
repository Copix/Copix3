<?php
/**
* @package		copix
* @subpackage	tests
* @author     Croës Gérald based on PHPUnit_TextUI_ResultPrinter by Sebastian Bergmann <sb@sebastian-bergmann.de>
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


/**
 * @ignore
 */
require_once 'PHPUnit/Extensions/RepeatedTest.php';
require_once 'PHPUnit/Runner/Version.php';
require_once 'PHPUnit/Runner/Version.php';
require_once 'PHPUnit/Util/Report.php';
require_once 'PHPUnit/Util/Report/GraphViz.php';
require_once 'PHPUnit/Util/Log/GraphViz.php';
require_once 'PHPUnit/Util/Log/JSON.php';
require_once 'PHPUnit/Util/Log/TAP.php';
require_once 'PHPUnit/Util/Log/XML.php';


PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

/**
 * A TestRunner for Copix
 *
 * @package    copix
 * @subpackage tests
 * @author     Croës Gérald based on PHPUnit_TextUI_TestRunner by Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright  CopixTeam
 * @license    LGPL
 * @link       http://www.copix.org
 */
class CopixTestRunner extends PHPUnit_Runner_BaseTestRunner {

    const SUCCESS_EXIT   = 0;
    const FAILURE_EXIT   = 1;
    const EXCEPTION_EXIT = 2;

    /**
     * @var    PHPUnit_Runner_TestSuiteLoader
     * @access private
     * @static
     */
    private static $loader = NULL;

    /**
     * @var    PHPUnit_TextUI_ResultPrinter
     * @access private
     */
    private $printer = NULL;

    /**
     * @var    boolean
     * @access private
     * @static
     */
    private static $versionStringPrinted = FALSE;

    /**
     * @param  mixed $test
     * @param  array $parameters
     * @access public
     * @static
     */
    public static function run($test, array $parameters = array()) {
        if ($test instanceof ReflectionClass) {
            $test = new PHPUnit_Framework_TestSuite($test);
        }

        if ($test instanceof PHPUnit_Framework_Test) {
        	$aTestRunner = new CopixTestRunner ();
            return $aTestRunner->doRun(
              $test,
              $parameters
            );
            
        }
    }

    /**
     * Runs a single test and waits until the user types RETURN.
     *
     * @param  PHPUnit_Framework_Test $suite
     * @access public
     * @static
     */
    public static function runAndWait(PHPUnit_Framework_Test $suite)  {
        $aTestRunner = new PHPUnit_TextUI_TestRunner;

        $aTestRunner->doRun(
          $suite
        );

    }

    /**
     * @return PHPUnit_Framework_TestResult
     * @access protected
     */
    protected function createTestResult() {
        return new PHPUnit_Framework_TestResult;
    }

    /**
     * @param  PHPUnit_Framework_Test $suite
     * @param  array                   $parameters
     * @return PHPUnit_Framework_TestResult
     * @access public
     */
    public function doRun(PHPUnit_Framework_Test $suite, array $parameters = array()) {
        $parameters['repeat']  = isset($parameters['repeat'])  ? $parameters['repeat']  : FALSE;
        $parameters['filter']  = isset($parameters['filter'])  ? $parameters['filter']  : FALSE;
        $parameters['verbose'] = isset($parameters['verbose']) ? $parameters['verbose'] : FALSE;

        if (is_integer($parameters['repeat'])) {
            $suite = new PHPUnit_Extensions_RepeatedTest ($suite, $parameters['repeat']);
        }

        if (isset($parameters['reportDirectory'])) {
            $parameters['reportDirectory'] = $this->getDirectory($parameters['reportDirectory']);
        }

        $result = $this->createTestResult();

        if ($this->printer === NULL) {
            if (isset($parameters['printer']) && $parameters['printer'] instanceof CopixTestPrinter) {
                $this->printer = $parameters['printer'];
            } else {
                $this->printer = new CopixTestPrinter (NULL, $parameters['verbose']);
            }
        }

        $result->addListener($this->printer);

        if (isset($parameters['testdoxHTMLFile'])) {
            $result->addListener(
              PHPUnit_Util_TestDox_ResultPrinter::factory(
                'HTML',
                $parameters['testdoxHTMLFile']
              )
            );
        }

        if (isset($parameters['testdoxTextFile'])) {
            $result->addListener(
              PHPUnit_Util_TestDox_ResultPrinter::factory(
                'Text',
                $parameters['testdoxTextFile']
              )
            );
        }

        if (isset($parameters['graphvizLogfile'])) {
            if (class_exists('Image_GraphViz', FALSE)) {
                $result->addListener(
                  new PHPUnit_Util_Log_GraphViz($parameters['graphvizLogfile'])
                );
            }
        }

        if (isset($parameters['reportDirectory']) &&
            extension_loaded('xdebug')) {
            if (class_exists('Image_GraphViz', FALSE)) {
                $result->addListener(
                  new PHPUnit_Util_Report_GraphViz($parameters['reportDirectory'])
                );
            }

            $result->collectCodeCoverageInformation(TRUE);
        }

        if (isset($parameters['jsonLogfile'])) {
            $result->addListener(
              new PHPUnit_Util_Log_JSON($parameters['jsonLogfile'])
            );
        }

        if (isset($parameters['tapLogfile'])) {
            $result->addListener(
              new PHPUnit_Util_Log_TAP($parameters['tapLogfile'])
            );
        }

        if (isset($parameters['xmlLogfile'])) {
            $result->addListener(
              new PHPUnit_Util_Log_XML($parameters['xmlLogfile'])
            );
        }

        $suite->run($result, $parameters['filter']);

        $result->flushListeners();

        if (isset($parameters['reportDirectory']) &&
            extension_loaded('xdebug')) {
            	$this->printer->write("<br /><a href='file://".$parameters['reportDirectory']."index.html'>Code coverage report</a> (file://".$parameters['reportDirectory']."index.html)<br />");
            PHPUnit_Util_Report::render($result, $parameters['reportDirectory']);
        }else{
        	$this->printer->write("<br />With XDebug, you would see a CodeCoverage report !<br />");
        }

        if ($this->printer instanceof CopixTestPrinter) {
        	$this->printer->printResult($result);
        }

        return $result;
    }

    /**
     * @param  PHPUnit_TextUI_ResultPrinter $resultPrinter
     * @access public
     */
    public function setPrinter(PHPUnit_TextUI_ResultPrinter $resultPrinter) {
        $this->printer = $resultPrinter;
    }

    /**
     * A test started.
     *
     * @param  string  $testName
     * @access public
     */
    public function testStarted($testName)  {}

    /**
     * A test ended.
     *
     * @param  string  $testName
     * @access public
     */
    public function testEnded($testName){}

    /**
     * A test failed.
     *
     * @param  integer                                 $status
     * @param  PHPUnit_Framework_Test                 $test
     * @param  PHPUnit_Framework_AssertionFailedError $e
     * @access public
     */
    public function testFailed($status, PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e) {}

    /**
     * Override to define how to handle a failed loading of
     * a test suite.
     *
     * @param  string  $message
     * @access protected
     */
    protected function runFailed($message) {
        self::printVersionString();
        print $message;
        exit(self::FAILURE_EXIT);
    }

    /**
     * @param  string $directory
     * @return string
     * @throws RuntimeException
     * @access private
     * @since  Method available since Release 3.0.0
     */
    private function getDirectory($directory) {
        if (substr($directory, -1, 1) != DIRECTORY_SEPARATOR) {
            $directory .= DIRECTORY_SEPARATOR;
        }

        if (is_dir($directory) || mkdir($directory, 0777, TRUE)) {
            return $directory;
        } else {
            throw new RuntimeException(
              sprintf(
                'Directory "%s" does not exist.',
                $directory
              )
            );
        }
    }

    /**
     * Returns the loader to be used.
     *
     * @return PHPUnit_Runner_TestSuiteLoader
     * @access public
     * @since  Method available since Release 2.2.0
     */
    public function getLoader(){
        if (self::$loader === NULL) {
            self::$loader = new PHPUnit_Runner_StandardTestSuiteLoader;
        }

        return self::$loader;
    }

    /**
     * Sets the loader to be used.
     *
     * @param PHPUnit_Runner_TestSuiteLoader $loader
     * @access public
     * @static
     * @since  Method available since Release 3.0.0
     */
    public static function setLoader(PHPUnit_Runner_TestSuiteLoader $loader) {
        self::$loader = $loader;
    }

    /**
     * @access public
     */
    public static function showError($message) {
        self::printVersionString();
        print $message . "\n";

        exit(self::FAILURE_EXIT);
    }


    /**
     * @access public
     * @static
     */
    public static function printVersionString() {
        if (!self::$versionStringPrinted) {
            print PHPUnit_Runner_Version::getVersionString() . "\n\n";
            self::$versionStringPrinted = TRUE;
        }
    }
}
?>