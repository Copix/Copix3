<?php
/**
 * @package		copix
 * @subpackage	console
 * @author		Nicolas Bastien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @experimental
 */

/**
 * Suppression d'un module
 *
 * @package		copix
 * @subpackage	console
 */
class CopixConsoleTaskDeleteModule extends CopixConsoleAbstractTask {

	public $description = "Suppression du module dont le nom est fourni en parametre";
	public $requiredArguments = array('module_name' => 'Nom du module a desinstaller');

	/**
	 * (non-PHPdoc)
	 * @see action/CopixConsoleAbstractTask#execute()
	 */
	public function execute() {
		echo "[delete-module] Installation du module : {$this->getArgument('module_name')}.\n";

		//Installation (les vérification sont gérées dans CopixModule)
		$arModuleToDelete = @CopixModule::getDependenciesForDelete($this->getArgument('module_name'));

		foreach ($arModuleToDelete as $moduleName) {
			@CopixModule::deleteModule($moduleName);
			echo "\t\tModule : $moduleName desinstalle.\n";
		}

		echo "[delete-module] Termine\n";
		return ;
	}


	public function getOptionalText() {


		//        $modules = @_class ('admin|InstallService')->getModules (); var_dump($modules['installed']);die;
		//		$groupsInstalled = array ();
		//		$groupsAvailables = array ();
		//		foreach ($modules as $status => $groups) {
		//			if ($status == 'installed') {
		//				$groupToEdit = &$groupsInstalled;
		//			} else {
		//				$groupToEdit = &$groupsAvailables;
		//			}
		//			foreach ($groups as $group) {
		//				foreach ($group as $module) {
		//					$group = $module->getGroup ();
		//					if (!array_key_exists ($group->getId (), $groupToEdit)) {
		//						$groupToEdit[$group->getId ()] = array ('caption' => $group->getCaption (), 'count' => 0);
		//					}
		//					$groupToEdit[$group->getId ()]['count'] += 1;
		//				}
		//			}
		//		}
		//
		//        var_dump($modules,$groupsInstalled,$groupsAvailables);die;
		//
		//        return "hello fqlmsdf";
	}

}

