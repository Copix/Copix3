<?php
/**
 * Champs de configuration d'un type de sauvegarde
 */
class ZoneTypeOptionsEditor extends CopixZone {
	/**
	 * Création du contenu
	 *
	 * @param string $pToReturn Contenu à retourner
	 * @return boolean
	 */
	protected function _createContent (&$pToReturn) {
		$tpl = new CopixTPL ();

		if ($this->getParam ('profile') != null) {
			$type = $this->getParam ('profile')->getType ();
		} else if ($this->getParam ('idPprofile')) {
			$type = BackupProfileServices::get ($this->getParam ('idPprofile'))->getType ();
		} else if ($this->getParam ('type') != null) {
			$type = $this->getParam ('type');
		} else {
			$type = BackupTypeServices::get ($this->getParam ('idType', BackupTypeServices::getDefault ()));
		}
		$tpl->assign ('type', $type);

		try {
			$pToReturn = $tpl->fetch ('backup|types/optionseditor.' . $type->getId () . '.php');
		} catch (Exception $e) {}
		return true;
	} 
}