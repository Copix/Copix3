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
 * Regénération du fichier CopixClassPaths.inc.php servant de base a l'autoload.
 *
 * @package		copix
 * @subpackage	console
 */
class CopixConsoleTaskGenerateAutoload extends CopixConsoleAbstractTask {

	public $description = "Regenere le fichier CopixClassPaths.inc.php servant de base a l'autoload.";

	/**
	 * (non-PHPdoc)
	 * @see action/CopixConsoleAbstractTask#execute()
	 */
	public function execute() {
		echo "[generate-autoload] Generation du fichier CopixClassPaths.inc.php.\n";
		CopixAutoloader::rebuildClassPath ();
		echo "[generate-autoload] Termine\n";
		return ;
	}

}