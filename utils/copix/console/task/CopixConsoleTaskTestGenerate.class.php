<?php
/**
 * @package		copix
 * @subpackage	console
 * @author		Nicolas Bastien
 * @copyright	CopixTeam
 * @link		http://copix.org, http://www.phpunit.de/manual/3.4/en/skeleton-generator.html
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @experimental
 */


/**
 * Génération de classe de test
 *
 * @package		copix
 * @subpackage	console
 */
class CopixConsoleTaskTestGenerate extends CopixConsoleAbstractTask {

	public $description = 'Generation de classe de test';
	public $requiredArguments = array('module_name' => 'Nom du module (les classes de tests seront generees a partir des fichiers contenu dans /classes).');
	public $optionalArguments = array('file_name' => 'Nom du fichier que l\'on veut tester');

	/**
	 * (non-PHPdoc)
	 * @see action/CopixConsoleAbstractTask#validate()
	 */
	public function validate () {
		if (!@include_once ('PHPUnit/Framework.php')) {
			throw new CopixException('PHPUnit is required to use UnitTesting under Copix');
		}
		return parent::validate();
	}

	/**
	 * (non-PHPdoc)
	 * @see action/CopixConsoleAbstractTask#execute()
	 */
	public function execute() {
		echo "[test-generate] Generation des classes de test pour : {$this->getArgument('module_name')}  {$this->getArgument('file_name')}.\n";

		if (is_null($this->getArgument('file_name'))) {
			$nbFileGenerated = CopixTestClassGenerator::generateTestClassForModule($this->getArgument('module_name'));
			echo "\t$nbFileGenerated fichiers genere.\n";
		} else {
			$modulePath = CopixModule::getPath($this->getArgument('module_name'));
			CopixTestClassGenerator::generateTestClass($modulePath . COPIX_CLASSES_DIR . $this->getArgument('file_name'));
		}
		
		echo "[test-generate] Termine\n";
		return ;
	}

}