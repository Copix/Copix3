<?php
/**
 * @package 	standard
 * @subpackage 	test
 * @author		Croës Gérald
 * @copyright	2001-2008 CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * ActionGroup pour lancer les test unitaires
 *
 * @package standard
 * @subpackage test
 */
class ActionGroupUnitTest extends CopixActionGroup {
	/**
	 * Fonction par défaut
	 *
	 * @return CopixActioReturn
	 */
	public function processDefault (){
		$ppo = new CopixPpo ();
		$ppo->arTests = $this->getAllTests ();
		return _arPpo ($ppo, 'tests.choice.php');
	}

	/**
	 * Lancement d'une série de tests
	 *
	 * @return CopixActionReturn
	 */
	public function processLaunch (){
		//Si aucun test n'est donné, on redirige vers la page de choix
		if (($test = _request ('tests')) === null){
			return _arRedirect (_url ('unittest|'));
		}
		//Si on a demandé à lancer les tests avec Ajax, on génère le template d'appel pour chaque élément
		if (_request ('ajax')){
			$ppo = new CopixPpo ();
			$ppo->TITLE_PAGE = 'Lancements des tests unitaires';
			$ppo->arTests = $this->_getTest ();
			return _arPpo ($ppo, 'tests.launch.php');
		}else{
			//on a pas demandé d'appel type ajax, donc on lance directement les tests demandés.
			if (CopixAjax::isAJAXRequest ()){
			}else{
				//C'est une demande normale, la réponse sera de type HTML
				$more = '';
			}
		}

		//On lance enfin la demande de test
		$httpClientRequest = new CopixHTTPClientRequest (CopixUrl::appendToUrl (_url ().'test.php', array ('tests'=>$test, 'xml'=>CopixAjax::isAJAXRequest ())));
		$httpClient = new CopixHttpClient ();
		$response = $httpClient->launch ($httpClientRequest);
		return _arContent ($response[0]->getBody (), array ('content-type'=>'text/html'));
	}

	/**
	 * Récupération de l'ensemble des éléments testables pour un module
	 * Un test doit se trouver dans un fichier *.class.php du répertoire tests de son module
	 *
	 * @param $pModuleName le nom du module dont on veut connaitre l'ensemble des classes de test.
	 * @return tableau de clef => libelle
	 */
	private function getTestableForModule ($pModuleName){
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
	 *
	 * @return array
	 */
	private function getAllTests (){
		$testables= array ();
		foreach (CopixModule::getList () as $modulePosition=>$moduleName){
			$testables = array_merge ($testables, $this->getTestableForModule ($moduleName));
		}
		return $testables;
	}

	/**
	 * Fonction de récupération des test à lancer
	 *
	 * @return array
	 */
	private function _getTest (){
		if (_request ('tests') == 'all'){
			$arToTest = $this->getAllTests ();
		}else{
			foreach ($this->getAllTests () as $moduleName=>$arTestsForModule){
				$arToTest[$moduleName] = array ();
				if (in_array ($moduleName.'|', _request ('tests'))){
					//Le module complet est demandé
					$arToTest[$moduleName] = $arTestsForModule;
				}else{
					//On test chacun des éléments de test
					foreach ($arTestsForModule as $test){
						if (in_array ($test, _request ('tests'))){
							$arToTest[$moduleName][] = $test;
						}
					}
				}
			}
		}
		return $arToTest;
	}
}