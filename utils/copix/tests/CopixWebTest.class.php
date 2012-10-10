<?php
/**
* @package		copix
* @subpackage	tests
* @author		Croës Gérald
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

/**
 * Classe de base pour les tests Copix de type WEB
 */
class CopixWebTest extends PHPUnit_Extensions_SeleniumTestCase {
	protected $_verificationErrors = array ();
	
	/**
	 * Bidouille pour faire en sorte que le array_push exporté par sellenium IDE marque
	 *  aussi automatiquement le navigateur utilisé et les infos que l'on désire
	 *
	 * @param string $pName
	 * @return array
	 */
	function & __get ($pName){
		if ($pName == 'verificationErrors'){
			$this->_verificationErrors[] = '['.get_class ($this->_browserSetUp).']';
			return $this->_verificationErrors;  
		}
		return null;
	}
	
	/**
	 * Enter description here...
	 *
	 * @var unknown_type
	 */
	protected $_browserSetUp = null;

	/**
	 * Définition de la méthode callback a utiliser pour le setup
	 *
	 * @param callback $pMethod l'identifiant de la méthode a appeler pour définir le contexte d'exécution
	 * @return void
	 */
	protected function setSetUp ($pCopixWebTestBrowserSetUp){
		$this->_browserSetUp = $pCopixWebTestBrowserSetUp;
	}
	
	/**
	 * Définition des paramètres du test
	 */
	public function setUp (){
		if (isset ($this->_browserSetUp)){
			$this->_browserSetUp->setUp ($this);
		}else{
			//Par défaut on test sous firefox
			$this->setBrowser ('*firefox');
		}
	}

    public function run(PHPUnit_Framework_TestResult $result = NULL) {
    	$ff = clone ($this);
       	$ff->setSetUp (new CopixWebTestFirefoxSetUp ());
		$result->run ($ff);
		
    	$ie = clone ($this);
       	$ie->setSetUp (new CopixWebTestIESetUp ());
		$result->run ($ie);

        return $result;
    }

    public function toString (){
    	return '['.get_class ($this->_browserSetUp).']'.parent::toString ();
    }
}

/**
 * Classe de base utilisée pour configurer les tests web effectués sous Copix
 */
class CopixWebTestSetUp {
	public function setUp (CopixWebTest $pTest){
	    $pTest->setPort (4444);
	    $pTest->setTimeout (10000);
	}
}

/**
 * Classe de configuration pour les tests sous firefox 
 */
class CopixWebTestFirefoxSetUp extends CopixWebTestSetUp {
	public function setUp (CopixWebTest $pTest){
		parent::setUp ($pTest);
		//Sous windows et firefox 3, je n'arrive à le lancer qu'en "custom"
		if (CopixConfig::osIsWindows ()){
	    	$pTest->setBrowser("*custom c:\\Program Files\\Mozilla Firefox\\firefox.exe");
		}else{
			//Si on est sous linux, ça devrait tourner en "normal"
			$pTest->setBrowser("*chrome");
		}
	}
}

/**
 * Classe de configuration pour les tests web sous IE
 */
class CopixWebTestIESetUp extends CopixWebTestSetUp {
	public function setUp (CopixWebTest $pTest){
		parent::setUp ($pTest);
		$pTest->setBrowser("*iexplore");
	}
}