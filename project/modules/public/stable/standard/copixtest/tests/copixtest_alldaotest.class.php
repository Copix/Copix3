<?php
/**
 * @package standard
 * @subpackage copixtest
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Test des classes de DAO
 * @package standard
 * @subpackage copixtest
 */
class CopixTest_AllDAOTest extends CopixTest {
	function setUp (){
		CopixContext::push ('copixtest');
	}
	
	function tearDown (){
		CopixContext::pop ();
	}
	
	/**
    * Récupération de toutes les DAO installées dans le proje
    */
	function _getAllDAO () {
		//on récupère la liste des modules.
		$arModules = CopixModule::getList ();
		$arDAOOfModule = array ();
		foreach ($arModules as $modulePosition=>$moduleName){
			$arDAOOfModule[$moduleName] = $this->_getAllDAOForModule ($moduleName);
		}
		return $arDAOOfModule;
	}

	/**
    * Récupération de la liste des DAO d'un module donné
    * @param $pModuleName le nom du module
    */
	function _getAllDAOForModule ($pModuleName){
		//on va rechercher l'ensemble des fichiers DAO pour le module donné.
		$toReturn = array ();
		$filePath = CopixModule::getPath ($pModuleName).'resources/*.dao.xml';
		//$filePath = str_replace ('/', '\\', $filePath);
		if (($files = glob ($filePath)) !== false){
			foreach ($files as $fileName){
				$className = basename ($fileName, '.dao.xml');
				$toReturn[] = $pModuleName.'|'.$className;
			}
		}
		return $toReturn;
	}

	/**
    * test de création des DAO de tout le projet
    */
	function testCreationDAO (){
		$arDAOToCreate = $this->_getAllDAO ();
		foreach ($arDAOToCreate as $moduleName=>$arDAO){
			foreach ($arDAO as $daoName){
				$this->assertTrue (is_object (_dao ($daoName)));
			}
		}
	}

	/**
    * Test de création de record
    */
	function testCreationRecord (){
		$arDAOToCreate = $this->_getAllDAO ();
		foreach ($arDAOToCreate as $moduleName=>$arDAO){
			foreach ($arDAO as $daoName){
				$this->assertTrue (is_object (_record ($daoName)));
			}
		}
	}

	/**
    * Test de la méthode check
    */
	function testMethodeCheck (){
		$arDAOToCreate = $this->_getAllDAO ();
		foreach ($arDAOToCreate as $moduleName=>$arDAO){
			foreach ($arDAO as $daoName){
				$dao     = _dao ($daoName);
				$record  = _record ($daoName);
//				CopixContext::push ($moduleName);
				if (method_exists ($dao, 'check')){
					$results = $dao->check ($record);
				}else{
					$results = $record->check ();
				}
				$this->assertTrue ((is_array ($results) &&(count ($results)>0)) || ($results === true));
//				CopixContext::pop ();
			}
		}
	}

	/**
    * On essaye de récupérer l'ensemble des enregistrements
    */
	function testMethodeFindAll (){
		$arDAOToCreate = $this->_getAllDAO ();
		foreach ($arDAOToCreate as $moduleName=>$arDAO){
			foreach ($arDAO as $daoName){
				$dao     = _dao ($daoName);
				$this->assertTrue ($dao->findAll () instanceof ArrayAccess);
			}
		}
	}
}
?>