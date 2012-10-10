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
class CopixTestPrinter extends PHPUnit_Util_Printer implements PHPUnit_Framework_TestListener {
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
		$this->printHeader   ($result->time());
		$this->printBody ($result);
		$this->printFooter($result);

		$this->printErrors   ($result);
		$this->printFailures ($result);
		$this->printIncompletes ($result);
		$this->printSkipped ($result);
		
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

		$this->write(
		sprintf(
		"<h2>There %s %d %s%s:</h2>",

		($count == 1) ? 'was' : 'were',
		$count,
		$type,
		($count == 1) ? '' : 's'
          )
		);

		$i = 1;

		foreach ($defects as $defect) {
			$this->printDefect($defect, $i++);
		}
	}

	/**
	 * @param  PHPUnit_Framework_TestFailure $defect
	 * @param  integer                       $count
	 * @access protected
	 */
	protected function printDefect(PHPUnit_Framework_TestFailure $defect, $count) {
		$this->printDefectHeader ($defect, $count);
		$this->printDefectTrace  ($defect);
		$this->printDefectFooter ($defect, $count);
	}

	/**
	 * @param  PHPUnit_Framework_TestFailure $defect
	 * @param  integer                       $count
	 * @access protected
	 */
	protected function printDefectFooter (PHPUnit_Framework_TestFailure $defect, $count){
		$this->write("</table>");
	}

	/**
	 * @param  PHPUnit_Framework_TestFailure $defect
	 * @param  integer                       $count
	 * @access protected
	 */
	protected function printDefectHeader(PHPUnit_Framework_TestFailure $defect, $count) {
		$failedTest = $defect->failedTest();

		if ($failedTest instanceof PHPUnit_Framework_SelfDescribing) {
			$testName = $failedTest->toString();
		} else {
			$testName = get_class($failedTest);
		}

		$this->write(
		sprintf(
		"<table class='CopixTable'><tr><td>%d</td><td> %s</td></tr>",
		$count,
		$testName
		)
		);
	}

	/**
	 * @param  PHPUnit_Framework_TestFailure $defect
	 * @access protected
	 */
	protected function printDefectTrace(PHPUnit_Framework_TestFailure $defect)  {
		$this->write ('<tr><td>*</td><td>');
		$e = $defect->thrownException();

		if ($e instanceof PHPUnit_Framework_SelfDescribing) {
			$this->write(_copix_utf8_htmlentities ($e->toString()));

			if ($e instanceof PHPUnit_Framework_ExpectationFailedException) {
				$comparisonFailure = $e->getComparisonFailure();
				if ($comparisonFailure !== NULL &&
				    $comparisonFailure instanceof PHPUnit_Framework_ComparisonFailure_String) {
					$this->write(_copix_utf8_htmlentities ($comparisonFailure->toString()));
			    }
			}
		}elseif ($e instanceof PHPUnit_Framework_Error) {
			$this->write(_copix_utf8_htmlentities ($e->getMessage()));
		}else{
			$this->write(_copix_utf8_htmlentities (get_class($e) . ': ' . $e->getMessage()));
		}

		$this->write(
		PHPUnit_Util_Filter::getFilteredStacktrace(
		$defect->thrownException(),
		FALSE
		)
		);

		$this->write ('</td></tr>');
	}

	/**
	 * @param  PHPUnit_Framework_TestResult  $result
	 * @access protected
	 */
	protected function printErrors(PHPUnit_Framework_TestResult $result)  {
		$this->printDefects($result->errors(), $result->errorCount(), 'error');
	}

	/**
	 * @param  PHPUnit_Framework_TestResult  $result
	 * @access protected
	 */
	protected function printFailures(PHPUnit_Framework_TestResult $result)  {
		$this->lastTestFailed = true;
		$this->printDefects($result->failures(), $result->failureCount(), 'failure');
	}

	/**
	 * @param  PHPUnit_Framework_TestResult  $result
	 * @access protected
	 */
	protected function printIncompletes(PHPUnit_Framework_TestResult $result)  {
		$this->printDefects($result->notImplemented(), $result->notImplementedCount(), 'incomplete test');
	}

	/**
	 * @param  PHPUnit_Framework_TestResult  $result
	 * @access protected
	 * @since  Method available since Release 3.0.0
	 */
	protected function printSkipped(PHPUnit_Framework_TestResult $result) {
		$this->printDefects($result->skipped(), $result->skippedCount(), 'skipped test');
	}

	/**
	 * @param  float   $timeElapsed
	 * @access protected
	 */
	protected function printHeader($timeElapsed) {
		$minutes = ($timeElapsed >= 60) ? floor($timeElapsed / 60) : 0;

		$this->write ('<table class="CopixTable" width="100%">
<thead style="background-color: gray" >
 <tr>
 <th style="text-align:left" >Test</th>
 <th>Success</th>
 <th>Incomplete</th>
 <th>Failure</th>
 <th>Error</th>
 </tr>
 <tr>
 <th style="text-align:left" colspan="5">'.sprintf(
		"Time: %02d:%02d",
		$minutes,
		$timeElapsed - $minutes * 60
		).'</th>
 </tr>
</thead>
');
	}

	/**
	 * @param  PHPUnit_Framework_TestResult  $result
	 * @access protected
	 */
	protected function printFooter(PHPUnit_Framework_TestResult $result)  {
		if ($result->wasSuccessful() &&
		$result->allCompletlyImplemented() &&
		$result->noneSkipped()) {
			$this->write(
			sprintf(
			"<tfoot><tr style='background-color: green'><td colspan='5'>OK (%d test%s)</td>/<tr></tfoot>",
			count($result),
			(count($result) == 1) ? '' : 's'
              )
			);
		}else if ((!$result->allCompletlyImplemented() ||
		!$result->noneSkipped())&&
		$result->wasSuccessful()) {
			$this->write(
			sprintf(
			"<tfoot><tr style='background-color: yellow'><td colspan='5'>OK, but incomplete or skipped tests! Tests: %d%s%s.</td>/<tr></tfoot>",
			count($result),
			$this->getCountString($result->notImplementedCount(), 'Incomplete'),
			$this->getCountString($result->skippedCount(), 'Skipped')
			)
			);
		}else {
			$this->write(
			sprintf(
			"<tfoot><tr style='background-color: red'><td colspan='5'>FAILURES ! Tests: %d%s%s.</td>/<tr></tfoot>",			
 			count($result),
			$this->getCountString($result->failureCount(), 'Failures'),
			$this->getCountString($result->errorCount(), 'Errors'),
			$this->getCountString($result->notImplementedCount(), 'Incomplete'),
			$this->getCountString($result->skippedCount(), 'Skipped')
			)
			);
		}
		$this->write ('</table>');
	}

	/**
	 * @param  integer $count
	 * @param  string  $name
	 * @return string
	 * @access protected
	 * @since  Method available since Release 3.0.0
	 */
	protected function getCountString($count, $name) {
		$string = '';

		if ($count > 0) {
			$string = sprintf(
			', %s: %d',

			$name,
			$count
			);
		}

		return $string;
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
		$this->write ("<tbody>");
		foreach ($this->elements as $element){
			if (($element['failure'] === 0) && ($element['error'] === 0) && 
			    ($element['success'] === 0) && ($element['incomplete'] === 0)){
			    	$this->write ('<tr style="background-color:silver"><td colspan="5">'.str_repeat ('&nbsp;', $element['depth']*2).$element["name"]."</td></tr>");
			}else{
				$color = '#11aa11';
				if ($element['incomplete'] !== 0){
					$color = 'yellow';
				}
				if ($element['failure'] !== 0 || $element['error'] !== 0){
					$color = 'red';
				}
				$this->write ('<tr style="background-color:'.$color.'"><td>'.str_repeat ('&nbsp;', $element['depth']*2).$element["name"].'</td><td>'.$element["success"].'</td><td>'.$element["incomplete"].'</td><td>'.$element["failure"].'</td><td>'.$element["error"].'</td></tr>');
			}
		}
		$this->write ("</tbody>");
	}
	
	/**
	 * 
	 */
	public function write ($string){
		$this->buffer .= $string;
	}
	
	public function codeCoverage ($pPath){
		if ($pPath !== false){
			$this->write ("<a href='file://".$pPath."index.html'>Code coverage report</a> (file://".$pPath."index.html)<br />");
		}else{
			$this->write ("With XDebug, you would see a CodeCoverage report !");
		}
	}
}