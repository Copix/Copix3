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
 * Lancement du checkstyle
 *
 * @package		copix
 * @subpackage	console
 */
class CopixConsoleTaskCodeSniffer extends CopixConsoleAbstractTask {

	public $description = 'Lancement du checkstyle';
	public $requiredArguments = array('param_name' => "Determine sur quel element on lance la verification : \n\t\t\t'copix'   -> le framework copix\n\t\t\t'project' -> tout le projet\n\t\t\t<le nom d'un module>\n\t\t\t<le nom d'un fichier>");
	public $optionalArguments = array('report_mode' => "Style de rapport a generer : correspond a l'option --report de phpcs\n\t\t\tPeut prendre les valeurs : 'full', 'xml', 'checkstyle', 'csv', 'emacs', 'source' ou 'summary' ('full' par défaut)");


	public function execute() {
		echo "[codesniffer] Lancement du checkstyle : {$this->getArgument('param_name')}.\n";

		$CopixCodingStandardFile = COPIX_SNIFFER_STANDARD_PATH;

		$toCheck = $this->_getFilesToCheck($this->getArgument('param_name'));

		echo "\tVerification de : " . $toCheck . "\n";

		$cmdLine = "phpcs --standard=$CopixCodingStandardFile $toCheck";

		//option de report
		if (!is_null($this->getArgument('report_mode'))) {
			//phpcs vérifie lui même les paramètre
			$cmdLine .= " --report={$this->getArgument('report_mode')}";
		}
		CopixFile::createDir(COPIX_SNIFFER_LOG_PATH);
		$cmdLine .= " --report-file=" . COPIX_SNIFFER_LOG_PATH . "checkstyle.xml";

		//Execution de la commande
		echo `$cmdLine`;

		echo "[codesniffer] Termine\n";
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
				if (CopixFile::extractFileExt($pParam) == '.php') {
					//On veut vérifie le fichier
					return $pParam;
				}
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