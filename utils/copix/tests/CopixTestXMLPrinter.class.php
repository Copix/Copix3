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
PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

/**
 * Prints the result of a TextUI TestRunner run.
 *
 * @package    copix
 * @subpackage tests
 * @author     Croës Gérald based on PHPUnit_TextUI_ResultPrinter Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright  CopixTeam
 * @license    LGPL
 * @link       http://www.copix.org
 */
class CopixTestXMLPrinter extends PHPUnit_Util_Printer implements PHPUnit_Framework_TestListener {
	/**
	 * buffer de sorrtie 
	 */
	private $buffer = '';

	/**
	 * @var    integer
	 * @access private
	 */
	private $column = 0;

	/**
	 * @var    integer
	 * @access private
	 */
	private $depth = 0;

	/**
	 * @var    integer
	 * @access private
	 */
	private $lastEvent = -1;

	/**
	 * @var    boolean
	 * @access private
	 */
	private $lastTestFailed = FALSE;
	
	
	/**
	 * Liste des tests qui ont étés lancés.
	 */
	private $elements = array ();

	/**
	 * @param  PHPUnit_Framework_TestResult $result
	 * @access public
	 */
	public function printResult (PHPUnit_Framework_TestResult $result) {
		header ('content-type: application/xml');
		/*
		$this->printHeader   ($result->time());
		$this->printBody ($result);
		$this->printFooter($result);

		*/

		$this->write ('<result>');
		$this->printBody ($result);
		$this->printErrors ($result);
		$this->printFailures ($result);
		$this->printIncompletes ($result);
		$this->printSkipped ($result);
		$this->write ('</result>');
		

		echo $this->buffer;
	}

	/**
	 * @param  array   $defects
	 * @param  integer $count
	 * @param  string  $type
	 * @access protected
	 */
	protected function printDefects(array $defects, $count, $type) {
		if ($count == 0) {
			return;
		}
		$this->write("<$type>");
		foreach ($defects as $defect) {
			$this->printDefect ($defect);
		}
		$this->write ("</$type>");
	}

	/**
	 * @param  PHPUnit_Framework_TestFailure $defect
	 * @param  integer                       $count
	 * @access protected
	 */
	protected function printDefect(PHPUnit_Framework_TestFailure $defect) {
		$this->printDefectHeader ($defect);
		$this->printDefectTrace  ($defect);
	}

	/**
	 * @param  PHPUnit_Framework_TestFailure $defect
	 * @param  integer                       $count
	 * @access protected
	 */
	protected function printDefectHeader(PHPUnit_Framework_TestFailure $defect) {
		$failedTest = $defect->failedTest();
		if ($failedTest instanceof PHPUnit_Framework_SelfDescribing) {
			$testName = $failedTest->toString();
		} else {
			$testName = get_class($failedTest);
		}
		$this->write ("<name>$testName</name>");
	}

	/**
	 * @param  PHPUnit_Framework_TestFailure $defect
	 * @access protected
	 */
	protected function printDefectTrace(PHPUnit_Framework_TestFailure $defect)  {
		$this->write ('<description>');
		$e = $defect->thrownException ();

		if ($e instanceof PHPUnit_Framework_SelfDescribing) {
			$this->write ($e->toString(), true);

			if ($e instanceof PHPUnit_Framework_ExpectationFailedException) {
				$comparisonFailure = $e->getComparisonFailure();
				if ($comparisonFailure !== NULL &&
				    $comparisonFailure instanceof PHPUnit_Framework_ComparisonFailure_String) {
					$this->write ($comparisonFailure->toString (), true);
			    }
			}
		}elseif ($e instanceof PHPUnit_Framework_Error) {
			$this->write ($e->getMessage (), true);
		}else{
			$this->write (get_class ($e) . ': ' . $e->getMessage (), true);
		}

		$this->write (
		PHPUnit_Util_Filter::getFilteredStacktrace (
		$defect->thrownException (),
		FALSE
		), true
		);
		$this->write ('</description>');
	}

	/**
	 * @param  PHPUnit_Framework_TestResult  $result
	 * @access protected
	 */
	protected function printErrors(PHPUnit_Framework_TestResult $result)  {
		$this->printDefects ($result->errors(), $result->errorCount(), 'errors');
	}

	/**
	 * @param  PHPUnit_Framework_TestResult  $result
	 * @access protected
	 */
	protected function printFailures(PHPUnit_Framework_TestResult $result)  {
		$this->lastTestFailed = true;
		$this->printDefects ($result->failures(), $result->failureCount(), 'failures');
	}

	/**
	 * @param  PHPUnit_Framework_TestResult  $result
	 * @access protected
	 */
	protected function printIncompletes(PHPUnit_Framework_TestResult $result)  {
		$this->printDefects ($result->notImplemented(), $result->notImplementedCount(), 'incompletes');
	}

	/**
	 * @param  PHPUnit_Framework_TestResult  $result
	 * @access protected
	 * @since  Method available since Release 3.0.0
	 */
	protected function printSkipped(PHPUnit_Framework_TestResult $result) {
		$this->printDefects ($result->skipped(), $result->skippedCount(), 'skipped');
	}

	/**
	 * An error occurred.
	 *
	 * @param  PHPUnit_Framework_Test $test
	 * @param  Exception              $e
	 * @param  float                  $time
	 * @access public
	 */
	public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)  {
		$this->elements[count ($this->elements)-1]['error']+=1;
		$this->lastTestFailed = true;
	}

	/**
	 * A failure occurred.
	 *
	 * @param  PHPUnit_Framework_Test                 $test
	 * @param  PHPUnit_Framework_AssertionFailedError $e
	 * @param  float                                  $time
	 * @access public
	 */
	public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time) {
		$this->elements[count ($this->elements)-1]['failure']++;
		$this->lastTestFailed = true;
	}

	/**
	 * Incomplete test.
	 *
	 * @param  PHPUnit_Framework_Test $test
	 * @param  Exception              $e
	 * @param  float                  $time
	 * @access public
	 */
	public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time) {
		$this->elements[count ($this->elements)-1]['incomplete']++;
	}

	/**
	 * Skipped test.
	 *
	 * @param  PHPUnit_Framework_Test $test
	 * @param  Exception              $e
	 * @param  float                  $time
	 * @access public
	 * @since  Method available since Release 3.0.0
	 */
	public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time) {
		$this->elements[count ($this->elements)-1]['skip']++;
	}

	/**
	 * A testsuite started.
	 *
	 * @param  PHPUnit_Framework_TestSuite $suite
	 * @access public
	 * @since  Method available since Release 2.2.0
	 */
	public function startTestSuite(PHPUnit_Framework_TestSuite $suite)  {
		$this->elements[] = array ('name'=>$suite->getName (), 'failure'=>0, 
		   'success'=>0, 'incomplete'=>0, 'skip'=>0, 'error'=>0, 'depth'=>$this->depth);
		   $this->depth++;
		    
	}

	/**
	 * A testsuite ended.
	 *
	 * @param  PHPUnit_Framework_TestSuite $suite
	 * @access public
	 * @since  Method available since Release 2.2.0
	 */
	public function endTestSuite(PHPUnit_Framework_TestSuite $suite) {
		$this->depth--;
	}

	/**
	 * A test started.
	 *
	 * @param  PHPUnit_Framework_Test $test
	 * @access public
	 */
	public function startTest(PHPUnit_Framework_Test $test) {
	}

	/**
	 * A test ended.
	 *
	 * @param  PHPUnit_Framework_Test $test
	 * @param  float                  $time
	 * @access public
	 */
	public function endTest(PHPUnit_Framework_Test $test, $time) {
		if (!$this->lastTestFailed){
			$this->elements[count ($this->elements)-1]['success']++;
		}
		$this->lastTestFailed = false;;
	}

	/**
	 * @param  string $progress
	 * @access protected
	 */
	protected function writeProgress($progress) {}
	
	public function printBody (){
		foreach ($this->elements as $element){
			if (! (($element['failure'] === 0) && ($element['error'] === 0) && 
			      ($element['success'] === 0) && ($element['incomplete'] === 0))){
				$color = '#11aa11';
				if ($element['incomplete'] !== 0){
					$color = 'yellow';
				}
				if ($element['failure'] !== 0 || $element['error'] !== 0){
					$color = 'red';
				}
				$this->write ('<name>'.$element["name"].'</name><success>'.$element["success"].'</success><incomplete>'.$element["incomplete"].'</incomplete><failure>'.$element["failure"].'</failure><error>'.$element["error"].'</error>');
			}
		}
	}
	
	/**
	 * 
	 */
	public function write ($string, $replace = false){
		$this->buffer .= $replace ? str_replace (array ('<', '>'), array ('[', ']'), $string) : $string;
	}
	
	public function codeCoverage ($pPath){
		if ($pPath !== false){
			$this->write ("<codecoverage>".$pPath."</codecoverage>");
		}
	}	
}
?>