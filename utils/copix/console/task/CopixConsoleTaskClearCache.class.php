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
 * Suppression des fichiers temporaires
 *
 * @package		copix
 * @subpackage	console
 */
class CopixConsoleTaskClearCache extends CopixConsoleAbstractTask {

	public $description = 'Suppression des fichiers de cache : dao, templates...';

	/**
	 * (non-PHPdoc)
	 * @see action/CopixConsoleAbstractTask#execute()
	 */
	public function execute() {
		echo "[clear-cache] Suppression des fichiers de cache.\n";
		CopixTemp::clear ();
		echo "[clear-cache] Termine\n";
		return ;
	}

}