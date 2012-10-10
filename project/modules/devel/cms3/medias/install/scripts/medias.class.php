<?php
/**
 * Mises à jour du module medias
 */
class CopixModuleInstallerMedias extends CopixAbstractModuleInstaller {
	/**
	 * Version 1.0.0 à 1.1.0
	 */
	public function process1_0_0_to_1_1_0 () {
		$daoHeading = DAOheadingelementinformation::instance ();
		foreach (DAOmedias::instance ()->findAll () as $media) {
			$records = $daoHeading->findBy (_daoSP ()->addCondition ('id_helt', '=', $media->id_media)->addcondition ('type_hei', '=', 'video'));
			if (count ($records) == 1) {
				$record = $records[0];
				$record->description_hei = $media->description_media;
				$daoHeading->update ($record);
			} else {
				_log ('Le média "' . $media->id_media . '" n\'a pas d\'enregistrement dans headingelementinformation.', 'errors');
			}
		}
		_doQuery ('alter table medias drop column description_media');
	}

	/**
	 * Version 1.1.0 à 1.2.0
	 */
	public function process1_1_0_to_1_2_0 () {
		_doQuery ('RENAME TABLE medias TO cms_medias');
	}
}