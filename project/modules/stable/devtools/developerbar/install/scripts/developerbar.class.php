<?php
/**
 * Installation du module developerbar
 */
class CopixModuleInstallerDeveloperBar extends CopixAbstractModuleInstaller {
	private $_plugin = 'developerbar|developerbar';

	/**
	 * Appelée après avoir installé le module dans Copix
	 */
	public function processPostInstall () {
		CopixPluginConfigFile::enable ($this->_plugin);
	}

	/**
	 * Appelée après avoir désinstallé le module dans Copix
	 */
	public function processPostDelete () {
		CopixPluginConfigFile::disable ($this->_plugin);
	}
}