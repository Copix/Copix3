<?php
/**
 * Mises à jour du module uploader
 */
class CopixModuleInstallerUploader extends CopixAbstractModuleInstaller {
	/**
	 * Version 1.0.0 à 1.1.0
	 */
	public function process1_0_0_to_1_1_0 () {
		// uploaderfiles
		_doQuery ('RENAME TABLE uploaderfiles TO cms_uploader_files');
		_doQuery ('ALTER TABLE cms_uploader_files CHANGE file_id id_file INT NOT NULL AUTO_INCREMENT');
		_doQuery ('ALTER TABLE cms_uploader_files CHANGE session_id id_session VARCHAR( 255 ) NOT NULL');
		_doQuery ('ALTER TABLE cms_uploader_files CHANGE file name_file VARCHAR( 255 ) NOT NULL');
		_doQuery ('ALTER TABLE cms_uploader_files CHANGE date_create create_file DATETIME NOT NULL');

		// uploadersessions
		_doQuery ('RENAME TABLE uploadersession TO cms_uploader_sessions');
		_doQuery ('ALTER TABLE cms_uploader_sessions CHANGE session_id id_session VARCHAR( 255 ) NOT NULL');
		_doQuery ('ALTER TABLE cms_uploader_sessions CHANGE date_create create_session DATETIME NOT NULL');
		_doQuery ('ALTER TABLE cms_uploader_sessions CHANGE state state_session VARCHAR( 255 ) NOT NULL');
		_doQuery ('ALTER TABLE cms_uploader_sessions CHANGE path path_session VARCHAR( 255 ) NOT NULL');
	}
}