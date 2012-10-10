<?php
/**
* @package		copix
* @subpackage	tests
* @author		Croës Gérald
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Controller pour les tests 
 */
class CopixTestController {
	/**
	 * Fichier de configuration
	 */
	private $_configFile = ''; 
	
    /**
     * Classe capable de prendre en charge les suites de test à lancer depuis le point d'entrée
     * test.php
     * Actuellement capable de lancer uniquement les tests relatifs au framework. Par la suite
     * il est souhaitable qu'il puisse prendre en charge le lancement des tests fonctionnels.
     * 
     * @param	array	$pParams	Tableau de paramètres
     * 
     * @todo être capable de lancer les tests fonctionnels avec ce controller.
     */	
	public function process ($pParams){
		if (@include_once ('PHPUnit/Framework.php')){
			require_once (COPIX_PATH.'tests/CopixTest.class.php');
			require_once (COPIX_PATH.'tests/CopixDBTest.class.php');
			require_once (COPIX_PATH.'tests/CopixTestRunner.class.php');
			require_once (COPIX_PATH.'tests/CopixTestPrinter.class.php');
			require_once (COPIX_PATH.'tests/CopixTestXMLPrinter.class.php');

			// Ignore les fichiers de framework de test
			PHPUnit_Util_Filter::addDirectoryToFilter(dirname(__FILE__));
			PHPUnit_Util_Filter::addDirectoryToFilter(dirname(__FILE__).'/framework');

			$this->_configFile = isset ($pParams['conf']) ? $pParams['conf'] : '../project/config/copix.conf.php';
			if (!isset ($_REQUEST['tests'])){
				$this->testWelcome ();
			}else {
				if (!isset ($pParams['xml']) || ($pParams['xml'] == false)){
					$options['reportDirectory'] = COPIX_TEMP_PATH;
				}
				$options['xml'] = isset ($pParams['xml']) && $pParams['xml']; 
				CopixTestRunner::run ($this->_getSuite (), $options);
			}
		}else{
			$this->showRequiredPHPUnit ();
		}
	}
	
	/**
	 * Cette fonction récupère la suite de test à lancer.
	 * @return PHPUnit_Framework_TestSuite
	 */
	private function _getSuite (){
    	$suite = new PHPUnit_Framework_TestSuite ('Tests');

		// Charge la config (nécessaire pour CopixModule)
    	$config = CopixConfig::instance ();
		$config->copixerrorhandler_enabled = false;
    	//On instancie un controlleur
		new ProjectController ($this->_configFile);

    	//----
		$subSuites = array ();
		$arToTest = array ();
		
		
		//on parcours l'ensemble des tests disponibles et on les ajoute
		//si on les trouve dans l'url.
		if ($_REQUEST['tests'] == 'all'){
			$arToTest = CopixTests::getAllTests ();
		}else{
			foreach (CopixTests::getAllTests () as $moduleName=>$arTestsForModule){
				$arToTest[$moduleName] = array ();
				if (in_array ($moduleName.'|', $_REQUEST['tests'])){
					//Le module complet est demandé
					$arToTest[$moduleName] = $arTestsForModule;
				}else{
					//On test chacun des éléments de test
					foreach ($arTestsForModule as $test){
						if (in_array ($test, $_REQUEST['tests'])){
							$arToTest[$moduleName][] = $test;
						}
					}
				}
			}
		}

		//on parcours ensuite la lise des tests demandés et on crée la suite.
		foreach ($arToTest as $moduleName=>$arTestsForModule){
			if (count ($arTestsForModule)){
				$moduleSuite = new PHPUnit_Framework_TestSuite ('Module '.$moduleName);
				foreach ($arTestsForModule as $nameTest){
					$toTest = explode ('|', $nameTest);
					require_once (CopixModule::getPath ($toTest[0]).'tests/'.$toTest[1].'.class.php');
					$moduleSuite->addTestSuite ($toTest[1]);
				}
				$suite->addTestSuite ($moduleSuite);
			}
		}
		return $suite;
	}

	/**
	 * Ecran d'accueil pour les tests
	 */
	public function testWelcome (){
		$project = new ProjectController ($this->_configFile);
		$arTests = CopixTests::getAllTests ();
		$PHPUnitTest = true;
		require_once (COPIX_PATH.'tests/CopixTestChoices.template.php');
	}
	
	/**
	 * 
	 */
	public function showRequiredPHPUnit (){
		$arTests = array ();
		$PHPUnitTest = false;
		require_once (COPIX_PATH.'tests/CopixTestChoices.template.php');
	}
}

/**
 * Classe de test
 * @todo à migrer pour la prise en charge des modules
 */
class CopixTests {
	/**
	 * 
	 */
    public static function main()  {
        PHPUnit_TextUI_TestRunner::run (self::suite());
    }
 
    
	/**
	* Récupération de l'ensemble des éléments testables pour un module
	* @param $pModuleName le nom du module dont on veut connaitre l'ensemble des classes de test.
	* @return tableau de clef => libelle
	*/
	private static function getTestableForModule ($pModuleName){
	   $toReturn = array ();
	   $filePath = CopixModule::getPath ($pModuleName).'tests/*.class.php';
	   $filePath = str_replace ('\\', '/', $filePath);
	   if (($files = glob ($filePath)) !== false){
	      foreach ($files as $fileName){
	         $className = basename ($fileName, '.class.php');
	         if (!isset ($toReturn[$pModuleName])){
	         	$toReturn[$pModuleName] = array ();
	         }
	         $toReturn[$pModuleName][] = $pModuleName.'|'.$className;
	      }
	   }
	   return $toReturn;
    }
    
    /**
     * Récupération de la liste de tous les tests possibles
     */
    public static function getAllTests (){
	    $arModules = CopixModule::getList ();
		$testables= array ();
		foreach ($arModules as $modulePosition=>$moduleName){
		   $testables = array_merge ($testables, self::getTestableForModule ($moduleName));
		}
		return $testables;
    }
}
?>