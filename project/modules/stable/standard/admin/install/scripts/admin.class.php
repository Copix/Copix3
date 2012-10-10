<?php
/**
 * Mises à jour du module admin
 */
class CopixModuleInstallerAdmin extends CopixAbstractModuleInstaller {
	/**
	 * Version 1.0.0 à 1.1.0
	 */
	public function process1_0_0_to_1_1_0 () {
		_doQuery ('ALTER TABLE copixconfig CHANGE value_ccfg value_ccfg TEXT DEFAULT NULL;');
        
	}
}