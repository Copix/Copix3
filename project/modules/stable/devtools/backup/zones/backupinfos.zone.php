<?php
/**
 * Informations sur un backup
 */
class ZoneBackupInfos extends CopixZone {
	/**
	 * Création du contenu
	 *
	 * @param string $pToReturn Contenu à retourner
	 * @return boolean
	 */
	protected function _createContent (&$pToReturn) {
		$tpl = new CopixTPL ();
		$tpl->assign ('restore', $this->getParam ('restore', false));
		$tpl->assign ('backupFilesPath', $this->getParam ('backupFilesPath'));
		$infos = ($this->getParam ('infos') instanceof BackupInfos) ? $this->getParam ('infos') : new BackupInfos ($this->getParam ('xml'));
		$tpl->assign ('infos', $infos);

		$config = CopixConfig::instance ();
		$profiles = $config->copixdb_getProfiles ();
		$dbprofiles = array ();
		foreach ($profiles as $name) {
			$profile = $config->copixdb_getProfile ($name);
			if ($profile->getDriverName () == $infos->getDbDriver ()) {
				$dbprofiles[] = $name;
			}
		}
		$tpl->assign ('dbprofiles', $dbprofiles);

		$pToReturn = $tpl->fetch ('backup|backups/infos.zone.php');
		return true;
	}
}