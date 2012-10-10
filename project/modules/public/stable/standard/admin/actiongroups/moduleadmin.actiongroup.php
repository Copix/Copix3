<?php
/**
 * @package    standard
 * @subpackage admin 
 * @author     Guillaume Perréal
 * @copyright  CopixTeam
 * @link       http://copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

class ActionGroupModuleAdmin extends CopixActionGroup {

	function processDefault() {

		$repos = CopixModuleRepositoryFactory::getRepository();
		foreach(CopixModuleRepositoryFactory::getRepositoryList() as $_repo) {
			if($_repo->isDeployTarget()) {
				$deployRepos = $_repo;
				echo "Deploying into ".$deployRepos->getName()."<br/>\n";
				break;
			}	
		}
		
		
		//var_dump($repos->getModuleList());
		
		$module = $repos->getModuleInformation('wiki');
		
		//var_dump($module);
	
		//var_dump($module->getVersionList());
		
		//var_dump($module->isInstalled());
		//var_dump($module->getName(), $module->getDescription());
		//var_dump($module->getLatestVersion());
		
		$version = $module->getLatestVersion();
		//var_dump($version->getVersionNumber());
		//var_dump($module->getVersion(0));
		
		$dep = $version->getDependencies();
		//var_dump($dep); 
		
		//var_dump($dep[0]->check($repos));
		
		$names = $repos->getModuleList();
		//var_dump(array_map(array($repos, 'getModuleInformation'), $names));
		
		$context = new CopixModuleInstallerContext($repos, $deployRepos); 
		
		$context->addGoal('install', $version);
		
		//var_dump($context);
		
		//$repos->dumpModules();
		
		exit;
	}

}

?>