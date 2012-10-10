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
 * Lancement du calcul de dépendance inter-classes (Compléxité cyclomatique)
 *
 * @package		copix
 * @subpackage	console
 */
class CopixConsoleTaskPHPDepend extends CopixConsoleAbstractTask {

	public $description = 'Calcul de la dependance inter classes';
	public $requiredArguments = array('param_name' => "Determine sur quel element on lance la verification : \n\t\t\t'copix'   -> le framework copix\n\t\t\t'project' -> tout le projet\n\t\t\t<le nom d'un module>\n\t\t\t<le nom d'un fichier>");
	
	/**
	 * (non-PHPdoc)
	 * @see action/CopixConsoleAbstractTask#execute()
	 */
	public function execute() {
		echo "[PHPDepend] Lancement du calcul de dependance : {$this->getArgument('param_name')}.\n";

		$toCheck = $this->_getFilesToCheck($this->getArgument('param_name'));

		echo "\tVerification de : " . $toCheck . "\n";

		CopixFile::createDir(COPIX_PDEPEND_RESULT_PATH);

		$cmdLine = "pdepend --summary-xml=" . COPIX_PDEPEND_RESULT_PATH . "summary.xml  --jdepend-chart=" . COPIX_PDEPEND_RESULT_PATH . "jdepend.svg  --overview-pyramid=" . COPIX_PDEPEND_RESULT_PATH . "pyramid.svg $toCheck";

		//Execution de la commande
		echo `$cmdLine`;

		echo "[PHPDepend] Termine\n";
		return ;
	}
	
	/**
	 * Renvoit le répertoire ou fichier à vérifier
	 *
	 * @param string $pParam le paramètre de la ligne de commande
	 */
	private function _getFilesToCheck($pParam) {

		switch ($pParam) {
			case 'copix':
				//Vérification du framework
				return COPIX_PATH;
			case 'project':
				//Vérification du projet
				return COPIX_PROJECT_PATH;
			default:
				//On veut vérifier un module
				$modulePath = CopixModule::getPath($pParam);
				if (!is_null($modulePath)) {
					return $modulePath;
				}
				//Dans ce cas on veut vérifier un répertoire
				return $pParam;
		}
	}
}