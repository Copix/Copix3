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
 * Utilitaire pour l'éxécution de tâche en ligne de commande
 * (installation de module, vidage de cache...)
 *
 * CopixConsole à été développée sur le modèle de la commande de Doctrine
 * dont elle est une adaptation pour Copix
 *
 * @package		copix
 * @subpackage	console
 */
class CopixConsole {

	protected $_tasks        = array(),
			  $_taskInstance = null;

	/**
	 * Tableau faisant le lien entre les raccouris et les noms de tâche réels
	 * @var array
	 */
	protected $_arTaskShortCut = array(
		'ai' => 'AutoInstall',
		'cc' => 'ClearCache',
		'ci'  => 'ContinuousIntegration',
		'cs'  => 'CodeSniffer',
		'dm' => 'DeleteModule',
		'doc' => 'PHPDoc',
		'dp'  => 'PHPDepend',
		'emd' => 'ExportModuleData',
		'ga' => 'GenerateAutoload',
		'gm' => 'GenerateModule',
		'im' => 'InstallModule',
		'imd' => 'ImportModuleData',
		't'   => 'Test',
		'tg'  => 'TestGenerate');


	/**
	 * Lancement de la console
	 */
	public function process ($args) {
		try {
			$this->_process($args);
		} catch (Exception $exception) {
			echo $exception->getMessage() . "\n";
		}
	}

	/**
	 * Exécute la commande demandée
	 *
	 * @param array $args argument de la ligne de commande
	 * @return void
	 */
	protected function _process($args) {

		//Gestion de l'affichage de l'aide et de la description des commandes
		if ( ! isset($args[1]) || $args[1] == 'help') {
			$this->printTasks();
			return;
		}

		if (isset($args[1]) && isset($args[2]) && $args[2] === 'help') {
			$this->printTask($args[1], true);
			return;
		}

		$this->_taskInstance = $this->_setTaskInstance($args[1]);
		
		unset($args[0]);
		unset($args[1]);
		if( count( $args) ){
			$args = $this->prepareArgs($args);
			$this->_taskInstance->setArguments($args);
		}

		try {
			//Validation des paramètres
			if ($this->_taskInstance->validate()) {
				$this->_taskInstance->execute();
			} else {
				echo "\nErreur: il manque un ou plusieurs parametres.\n";
				$this->printTask($this->_taskInstance->getTaskName(), true);
			}
		} catch (Exception $e) {
			throw new CopixException($e->getMessage());
		}
	}

	/**
	 * Get the name of the task class based on the first argument
	 * which is always the task name. Do some inflection to determine the class name
	 *
	 * @param  array $args       Array of arguments from the cli
	 * @return string $taskClass Task class name
	 */
	protected function _getTaskClassFromName($task)
	{
		//Gestion des raccourcis
		if (array_key_exists($task, $this->_arTaskShortCut)) {
			$task = $this->_arTaskShortCut[$task];
		}

		return 'CopixConsoleTask' . $task;;
	}
	
	protected function _setTaskInstance( $task ){
		$taskClass = $this->_getTaskClassFromName($task);

		if ( ! class_exists($taskClass)) {
			throw new CopixException("Erreur: La tache " . $taskClass . " n'est pas disponible. Tapez \"Copix help\" pour la liste des taches. \n");
		}
		
		return $this->_taskInstance = new $taskClass();
	}

	/**
	 * Prepare the raw arguments for execution. Combines with the required and optional argument
	 * list in order to determine a complete array of arguments for the task
	 *
	 * @param  array $args      Array of raw arguments
	 * @return array $prepared  Array of prepared arguments
	 */
	protected function prepareArgs($args)
	{
		$args = array_values($args);

		// First lets load populate an array with all the possible arguments. required and optional
		$prepared = array();

		$requiredArguments = $this->_taskInstance->getRequiredArguments();
		foreach ($requiredArguments as $key => $arg) {
			$prepared[$arg] = null;
		}

		$optionalArguments = $this->_taskInstance->getOptionalArguments();
		foreach ($optionalArguments as $key => $arg) {
			$prepared[$arg] = null;
		}

		// If we have a config array then lets try and fill some of the arguments with the config values
		//Ici on peut se baser directement sur copix.conf à voir pour des tâches ayant besoin de la BDD ou autre
		//        if (is_array($this->_config) && !empty($this->_config)) {
		//            foreach ($this->_config as $key => $value) {
		//                if (array_key_exists($key, $prepared)) {
		//                    $prepared[$key] = $value;
		//                }
		//            }
		//        }

		// Now lets fill in the entered arguments to the prepared array
		$copy = $args;
		foreach ($prepared as $key => $value) {
			if ( ! $value && !empty($copy)) {
				$prepared[$key] = $copy[0];
				unset($copy[0]);
				$copy = array_values($copy);
			}
		}

		return $prepared;
	}

	
	/**
	 * Prints information about a task
	 *
	 * @return void
	 */
	public function printTask( $task, $details = false )
	{
		//Gestion des raccourcis
		if( array_key_exists( $task, $this->_arTaskShortCut ) ){
			$shortcut = $task;
			$task = $this->_arTaskShortCut[$task];
		} else {
			$shortcuts = array_flip( $this->_arTaskShortCut );
			$shortcut = $shortcuts[$task];
		}
		$this->_setTaskInstance($task);
		echo "\t[$shortcut]\t[{$this->_taskInstance->getTaskName()}]\t";
		echo $this->_taskInstance->getDescription() . "\n";
		
		if( $details ){
			$requiredArguments = $this->_taskInstance->getRequiredArgumentsDescriptions();
			if ( ! empty($requiredArguments)) {
				echo "\t- requis : \n";
				foreach ($requiredArguments as $name => $description) {
					echo "\t\t$name : $description \n";
				}
			}
	
			$optionalArguments = $this->_taskInstance->getOptionalArgumentsDescriptions();
			if ( ! empty($optionalArguments)) {
				echo "\t- optionnel : \n";
				foreach ($optionalArguments as $name => $description) {
					echo "\t\t$name : $description \n";
				}
			}
	
			//Ajout d'un texte supplémentaire pour complémenter les options disponibles
			echo $this->_taskInstance->getOptionalText() . "\n";
		}
		return;
	}
	
	
	/**
	 * Prints an index of all the available tasks in the CLI instance
	 *
	 * @return void
	 */
	public function printTasks() {
		//Liste toutes les tâches
		echo "[CopixConsole] liste des taches disponibles : \n\n";

		foreach ($this->_arTaskShortCut as $shortcut => $task) {
			$this->printTask( $task, false );
		}

		echo "\n\tPour plus d'informations sur une tache, tapez \"copix <tache> help\"\n\n";
		return;
	}
}