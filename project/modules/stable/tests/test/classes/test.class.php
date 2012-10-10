<?php
/**
 * @package standard
 * @subpackage test
 * @author		Croës Gérald, Julien Alexandre
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

class Test {}

/**
 * Représente un test possible
 */
interface ITest {
	/**
	 * Le constructeur doit accepter un identifiant de test
	 * @param int $pIdTest
	 */
	public function __construct (ICopixDAORecord $pRecord);

	/**
	 * Fonction qui lance a proprement parler le test et qui indique s'il est réussit ou non
	 * @return ITestResult
	 */
	public function check ();
}


interface ITestResult {
	function addResult ($pIdTest);
}

class TestResult {
	private $_results = array ();

	private $_id = null;

	function __construct ($pTestId){
		$this->_id = $pTestId;
	}

	public function add ($pResult, $pCaption = null){
		$this->_results[] = array ($pCaption, $pResult);
	}
	/**
	 * retourne le fichier XML contenant les résultats des tests 
	 */
	public function asXml (){
		$toReturn = '<testresults>';
		foreach ($this->_results as $id=>$result){
			$toReturn .= '<test id="'.$this->_id.'" result="'.($result[1] === true ? 'OK' : ($result[1] === null ? 'I' : 'KO')).'">';
			if ($result[1] !== true && $result[1] !== null){
				$toReturn .= $result[0];
			} else {
				$toReturn .= $result[0];
			}
			$toReturn .= '</test>';

		}
		$toReturn .= '</testresults>';
		return $toReturn;
	}

	public function getResults () {
		return $this->_results;
	}

}

/**
 * Classe de base pour les tests unitaires "fonctionnels"
 */
abstract class AbstractTest implements ITest{
	private $_id;
	private $_type;
	private $_caption;
	private $_level;
	private $_category;

	private $_properties = array ();

	private $_testResult;

	function __construct (ICopixDAORecord $pRecord){
		$this->_properties = array ('id', 'type', 'caption', 'level', 'category');

		//Initialisation des propriétés de l'objet a partir des éléments de la base de données
		foreach ($this->_properties as $property){
			$propertyName = '_'.$property;
			$propertyNameInRecord = $property.'_test';
			$this->$propertyName = $pRecord->$propertyNameInRecord;
		}
	}

	/**
	 * Méthode à définir par les descendants pour vérifier le tes
	 * @return boolean
	 */
	abstract protected function _validate ();

	/** @return unknown
	 * Lance un test et renvoie le résultat des étapes
	 */ 	
	public function checkSteps (){
		$this->_testResult = new TestResult ($this->_id);
		$this->_validate ();
		return $this->_testResult;
	}

	/**
	 * @return unkown
	 * Lance des tests
	 */
	public function check () {
		$this->_testResult = new TestResult ($this->_id);
		CopixClassesFactory::fileInclude('notification');

		$parameters = _dao('test')->get($this->_id);

		$this->_validate ();
		$test = $this->_testResult->getResults ();
		$verify = true;
		$errors = array ();
		$timing = new stdClass ();
		foreach ($test as $id => $value) {
			if ($value[1] === false) {
				$verify = false;
				$errors[$id] = $value[0];
			} elseif ($value[0]) {
				$timing = $value[0];
			}
		}
		$arData = $parameters;
		$arData->result = $verify;
		$arData->errors = $errors;
		$arData->timing = $timing;
		
		// Notification des erreurs
		if ($arData->result === false) {
			Notification::notifyError ($parameters, $errors);
		}
		
		return $arData;
	}

	/**
	 * Accesseurs
	 *
	 * @param string $pPropertyName	le nom de la propriété à récupérer
	 */
	public function __call ($pFunctionName, $pParams){
		$callable = array ();
		foreach ($this->_properties as $property){
			$callable[] = 'get'.CopixFormatter::capitalize ($property);
		}

		if (in_array ($pFunctionName, $callable)){
			$propertyName = strtolower ('_'.substr ($pFunctionName, 3));
			return $this->$propertyName;
		}
		throw new CopixException ('Propriété inexistante '.strtolower ('_'.substr ($pFunctionName, 3)));
	}

	protected function _addResult ($pResult, $pCaption = null){
		$this->_testResult->add ($pResult, $pCaption);
	}

	public function save (){
	}

	public function updateFromArray (){
	}
}