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
 * Création d'un module avec les fichiers par défaut
 *
 * @package		copix
 * @subpackage	console
 */
class CopixConsoleTaskGenerateModule extends CopixConsoleAbstractTask {

	public $description = "Generation du module dont le nom est fourni en parametre.";
	public $requiredArguments = array('module_name' => "Chemin et nom du module a generer. Exemple : modules/devel/monmodule");

	/**
	 * (non-PHPdoc)
	 * @see action/CopixConsoleAbstractTask#execute()
	 */
	public function execute() {
		echo "[generate-module] Generation du module : {$this->getArgument('module_name')}.\n";

		//Si le nom commence par un '/', CopixFile remonte une erreur
		$moduleName = $this->getArgument('module_name');
		if (substr($moduleName, 0, 1) == DIRECTORY_SEPARATOR
			||  substr($moduleName, 0, 1) == '/') {
			$moduleName = substr($moduleName, 1);
		}

		//Création des différents fichiers
		CopixFile::write($moduleName . DIRECTORY_SEPARATOR . 'actiongroups'. DIRECTORY_SEPARATOR . 'default.actiongroup.php', $this->_strCommentTpl . $this->_strActionGroupTpl);
		CopixFile::createDir($moduleName . DIRECTORY_SEPARATOR . 'classes');
		CopixFile::createDir($moduleName . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'scripts');
		CopixFile::createDir($moduleName . DIRECTORY_SEPARATOR . 'zones');
		CopixFile::write($moduleName . DIRECTORY_SEPARATOR . 'templates'. DIRECTORY_SEPARATOR . 'default.tpl', $this->_strTemplateTpl);
		CopixFile::write($moduleName . DIRECTORY_SEPARATOR . 'module.xml', $this->_strModuleXMLTpl);

		echo "[generate-module] Termine\n";
		return ;
	}

	protected $_strCommentTpl = "<?php
/**
 * @package		%%package%%
 * @subpackage  %%subpackage%%
 * @author		%%Your Name%%
 */
";

	protected $_strActionGroupTpl = '
/**
 * %%Descption de la classe%%
 *
 * @package     %%package%%
 * @subpackage  %%subpackage%%
 */
class ActionGroupDefault extends CopixActionGroup {

	/**
	 * Action par défaut
	 */
	public function processDefault (){
		$ppo = _ppo();
		$ppo->TITLE_PAGE = "Page par défaut de votre nouveau module!";

		return _arPPO($ppo, "default.tpl");
	}

}';


	protected $_strTemplateTpl = '
	<h1>Votre module à été généré avec succès.</h1>

	<div>
		Pour la suite rendez-vous sur les tutoriaux Copix > <a href="http://www.copix.org/index.php/wiki/Tutoriaux">Cliquez ici.</a>
	</div>
';

	protected $_strModuleXMLTpl = '<?xml version="1.0" encoding="UTF-8"?>
<moduledefinition version="1">
	<general>
		<default name="monmodule" version="0.1" description="Ici description de votre module" />
	</general>
	<dependencies>
	</dependencies>
	<parameters>
		<parameter name="defaultParam" caption="Paramètre par défaut" type="text" default="todo" maxLength="255" />
	</parameters>
	<registry>
	</registry>
</moduledefinition>';

}

