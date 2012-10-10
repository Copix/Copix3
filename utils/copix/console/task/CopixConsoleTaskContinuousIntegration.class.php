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
 * Lancement d'un build complet avec mise à jour svn lancement des tests, du checkstyle, génération de la documentation...
 *
 * @package		copix
 * @subpackage	console
 */
class CopixConsoleTaskContinuousIntegration extends CopixConsoleAbstractTask {

	public $description = "Lancement d'un build complet";
	
	/**
	 * (non-PHPdoc)
	 * @see action/CopixConsoleAbstractTask#execute()
	 */
	public function execute() {
		echo "[Continuous-Integration] Lancement d'un build complet du projet.\n";

		echo "Mise à jour des sources...\n";
		echo `svn up ` . COPIX_PATH;
		echo `svn up ` . COPIX_PROJECT_PATH;

		//Lancement test unitaire
		echo "Lancement des tests unitaires...\n";
		//echo `php copix test all`;

		//Lancement code sniffer
		echo "Lancement code sniffer...";
		echo `php copix cs project xml`;

		//Lancement pdepend
		echo "Lancement pdepend...";
		echo `php copix dp project`;

		//Lancement phpdoc
		echo "Generation de la documentation";
		echo `php copix doc project`;

		//Dans l'idéal envoit d'un mail récap si spécifié en config
		echo "TODO voir un envoi de mail si ca interesse quelqu un.\n";

		//Log en base pour pouvoir faire des historique
		echo "TODO logger en base pour faire des stats plus tard.\n";

		echo "[Continuous-Integration] Termine\n";
		return ;
	}
	
}