<?php
/**
 *
 * @package		copix
 * @subpackage	console
 * @author		Nicolas Bastien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @experimental
 */

/**
 * Installation d'un module
 *
 * @package		copix
 * @subpackage	console
 */
class CopixConsoleTaskInstallModule extends CopixConsoleAbstractTask {

	public $description = "Installation du module dont le nom est fourni en parametre";
	public $requiredArguments = array('module_name' => 'Nom du module a installer');

	/**
	 * (non-PHPdoc)
	 * @see action/CopixConsoleAbstractTask#execute()
	 */
	public function execute() {
		echo "[install-module] Installation du module : {$this->getArgument('module_name')}.\n";

		//Installation (les vérification sont gérées dans CopixModule)
		$arModuleToInstall = $this->getModulesDependenciesToInstall($this->getArgument('module_name'));

		foreach ($arModuleToInstall as $moduleName) {
			@CopixModule::installModule($moduleName);
			echo "     Module : $moduleName installe.\n";
		}

		echo "[install-module] Termine\n";
		return ;
	}

	/**
	 * Parse la liste des dépendances du module pour obtenir la liste des modules à installer
	 *
	 * @param string $pModuleName
	 */
	public function getModulesDependenciesToInstall($pModuleName) {

		$arModuleToInstall = array ();
		$arOrder = array ();

		$arDependency = @CopixModule::getDependenciesForInstall ($pModuleName);
		foreach ($arDependency as $key=>$dependency) {
			if ($dependency->kind === 'module') {
				//Gestion des modules en double avec les dependences
				if (!in_array($dependency->name,$arModuleToInstall)) {
					$arModuleToInstall[] = $dependency->name;
					$arOrder[] = $dependency->level;
				} else {
					//Gestion du niveau d'install des dependences
					$key = array_search($dependency->name, $arModuleToInstall);
					if ($arOrder[$key] < $dependency->level) {
						$arOrder[$key] = $dependency->level;
					}
				}
			}
		}

		array_multisort ($arOrder,SORT_ASC, $arModuleToInstall, SORT_DESC);

		return $arModuleToInstall;
	}
}

