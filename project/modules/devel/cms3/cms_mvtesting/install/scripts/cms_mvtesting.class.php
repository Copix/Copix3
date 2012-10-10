<?php
/**
 * Mises à jour du module mvtesting
 */
class CopixModuleInstallerCMS_MVTesting extends CopixAbstractModuleInstaller {
	/**
	 * Version 1.0.0 à 1.1.0
	 */
	public function process1_0_0_to_1_1_0 () {
		_doQuery ('RENAME TABLE cms_mvtesting TO cms_mvtestings');
		_doQuery ('RENAME TABLE cms_mvtesting_elements TO cms_mvtestings_elements');
	}

	public function process1_1_0_to_1_2_0 () {
		_doQuery ('RENAME TABLE cms_mvtestings_elements TO cms_mvtestings_headingelementinformations');
	}

	public function process1_2_0_to_1_2_1 () {
		_doQuery ('ALTER TABLE `cms_mvtestings` MODIFY COLUMN `public_id_hei` INTEGER  DEFAULT NULL');
	}
}