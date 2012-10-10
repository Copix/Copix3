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
 * Classe abstraite rassemblant les fonctions de base des tâches de la console
 *
 * @package		copix
 * @subpackage	console
 */
abstract class CopixConsoleAbstractTask {
	public  $taskName             =   null,
			$description          =   null,
			$arguments            =   array(),
			$requiredArguments    =   array(),
			$optionalArguments    =   array(),
			$optionalText         =   null;


	public function __construct() {
		$this->taskName = str_replace('CopixConsoleTask', '', get_class($this));
	}

	/**
	 * execute
	 *
	 * Override with each task class
	 *
	 * @return void
	 * @abstract
	 */
	abstract function execute();

	/**
	 * validate
	 *
	 * Validates that all required fields are present
	 *
	 * @return bool true
	 */
	public function validate()
	{
		$requiredArguments = $this->getRequiredArguments();

		foreach ($requiredArguments as $arg) {
			if ( ! isset($this->arguments[$arg])) {
				return false;
			}
		}

		return true;
	}

	/**
	 * addArgument
	 *
	 * @param string $name
	 * @param string $value
	 * @return void
	 */
	public function addArgument($name, $value)
	{
		$this->arguments[$name] = $value;
	}

	/**
	 * getArgument
	 *
	 * @param string $name
	 * @param string $default
	 * @return mixed
	 */
	public function getArgument($name, $default = null)
	{
		if (isset($this->arguments[$name]) && $this->arguments[$name] !== null) {
			return $this->arguments[$name];
		} else {
			return $default;
		}
	}

	/**
	 * getArguments
	 *
	 * @return array $arguments
	 */
	public function getArguments()
	{
		return $this->arguments;
	}

	/**
	 * setArguments
	 *
	 * @param array $args
	 * @return void
	 */
	public function setArguments(array $args)
	{
		$this->arguments = $args;
	}

	/**
	 * getTaskName
	 *
	 * @return string $taskName
	 */
	public function getTaskName()
	{
		return $this->taskName;
	}

	/**
	 * getDescription
	 *
	 * @return string $description
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * getRequiredArguments
	 *
	 * @return array $requiredArguments
	 */
	public function getRequiredArguments()
	{
		return array_keys($this->requiredArguments);
	}

	/**
	 * getOptionalArguments
	 *
	 * @return array $optionalArguments
	 */
	public function getOptionalArguments()
	{
		return array_keys($this->optionalArguments);
	}

	/**
	 * getRequiredArgumentsDescriptions
	 *
	 * @return array $requiredArgumentsDescriptions
	 */
	public function getRequiredArgumentsDescriptions()
	{
		return $this->requiredArguments;
	}

	/**
	 * getOptionalArgumentsDescriptions
	 *
	 * @return array $optionalArgumentsDescriptions
	 */
	public function getOptionalArgumentsDescriptions()
	{
		return $this->optionalArguments;
	}

	 /**
	 * getOptionalText
	 *
	 * @return string $optionalText
	 */
	public function getOptionalText()
	{
		return $this->optionalText;
	}
}