<?php
/**
 * Mises à jour du module backup
 */
class CopixModuleInstallerBackup extends CopixAbstractModuleInstaller {
	/**
	 * Version 1.0.0 à 1.1.0
	 */
	public function process1_0_0_to_1_1_0 () {
		_doQuery ('
			ALTER TABLE `backup_profiles_email`
			DROP PRIMARY KEY,
			ADD PRIMARY KEY(`id_profile`);'
		);
		_doQuery ('ALTER TABLE `backup_profiles_email` CHANGE `bcc_email` `bcc_email` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ');
	}
	
	public function process1_1_0_to_1_2_0 () {
		_doQuery ('
			CREATE TABLE `backup_profiles_files` (
				`id_file` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`id_profile` int(10) unsigned NOT NULL,
				`path_file` varchar(255) NOT NULL,
				PRIMARY KEY (`id_file`)	
			) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8
		');
	}
	
	public function process1_2_0_to_1_2_1 () {
		_doQuery ('ALTER TABLE `backup_profiles` ADD `filesPath_profile` VARCHAR( 255 ) NULL ');
		_doQuery ('ALTER TABLE `backup_profiles` DROP `compress_profile`');
	}
}