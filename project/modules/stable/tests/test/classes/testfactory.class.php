<?php
/**
 * @package standard
 * @subpackage copixtest
 * @author		Croës Gérald, Julien Alexandre
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de base pour toutes les exceptions de test
 */
class TestException extends CopixException {}

/**
 * Exception a lancer si le test n'est pas trouvé dans la base de données
 */
class TestNotFoundException extends TestException {
	public function __construct ($pTestId) {
		parent::__construct ('Erreur le test '.$pTestId.' demandé n\'existe pas ');
	}
}

/**
 * Lorsque l'on tente d'exécuter un test qui n'est pas connu
 */
class TestUnknownTypeException extends TestException {}

/**
 * Factory pour la création des tests fonctionnels
 */
class TestFactory {
	public function create ($pTestId){
		if (! $record = _dao ('test')->get ($pTestId)){
			throw new TestNotFoundException ($pTestId);
		}

		//@TODO : possibilité pour un module de gérer plusieurs types de test
		$moduleName = 'test_'.$record->type_test;
		if (! in_array ($moduleName, CopixModule::getList ())){
			throw new TestUnknownTypeException ($record->type_test);
		}

		//On crée la classe de test
		return _class ($moduleName.'|'.$record->type_test, array ($record));
	}

	/**
	 * Récupération de la liste des types de test connu par l'application
	 *
	 * @return array
	 */
	public function getTypeList (){
		$toReturn = array ();
		foreach (CopixModule::getList () as $name){
			if (substr ($name, 0, 5) == 'test_'){
				$toReturn[substr ($name, 5)] = substr ($name, 5);
			}
		}
		return $toReturn;
	}
}