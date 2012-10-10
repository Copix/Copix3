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
 * Lancement des tests unitaires
 *
 * @package		copix
 * @subpackage	console
 */
class CopixConsoleTaskPHPDoc extends CopixConsoleAbstractTask {

	public $description = "Generation de la documentation technique a partir du code";
	public $requiredArguments = array('param_name' => "Determine sur quel element on lance la verification : \n\t\t\t'copix'   -> le framework copix\n\t\t\t'project' -> tout le projet\n\t\t\t<le nom d'un module>\n\t\t\t<le nom d'un fichier>");
	public $optionalArguments = array('output_style' => "Le type de fichier a generer, correspond a l'option -o de phpdoc (par defaut : HTML:frames:earthli)");

	/**
	 * (non-PHPdoc)
	 * @see action/CopixConsoleAbstractTask#execute()
	 */
	public function execute() {
		echo "[PHPDoc] Generation de la phpdoc : {$this->getArgument('param_name')}.\n";

		$toCheck = $this->_getFilesToCheck($this->getArgument('param_name'));

		echo "\tTraitement de : " . $toCheck . "\n";

		CopixFile::createDir(COPIX_DOCUMENTATION_PATH);

		//Titre
		$documentationTitle = "\"Documentation g&eacute;n&eacute;r&eacute; le " . CopixDateTime::timestampToDateTime(time()) . " <br/>[Copix ".COPIX_VERSION."]\"";

		$cmdLine = "phpdoc -d $toCheck -t " . COPIX_DOCUMENTATION_PATH . " -q -ti " . $documentationTitle;

		$outputStyle = $this->getArgument('output_style');
		if (is_null($outputStyle)) {
			$outputStyle = 'HTML:frames:earthli';
		}

		$cmdLine .= " -o " . $outputStyle;

		//Execution de la commande
		echo `$cmdLine`;

		echo "[PHPDoc] Termine\n";
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