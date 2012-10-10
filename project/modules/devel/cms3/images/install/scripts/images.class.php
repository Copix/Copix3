<?php
/**
 * Mises à jour du module images
 */
class CopixModuleInstallerImages extends CopixAbstractModuleInstaller {
	/**
	 * Version 1.0.0 à 1.1.0
	 */
	public function process1_0_0_to_1_1_0 () {
		$daoHeading = DAOheadingelementinformation::instance ();
		foreach (DAOimages::instance ()->findAll () as $image) {
			$records = $daoHeading->findBy (_daoSP ()->addCondition ('id_helt', '=', $image->id_image)->addcondition ('type_hei', '=', 'image'));
			if (count ($records) == 1) {
				$record = $records[0];
				$record->description_hei = $image->description_image;
				$daoHeading->update ($record);
			} else {
				_log ('L\'image "' . $image->id_image . '" n\'a pas d\'enregistrement dans headingelementinformation.', 'errors');
			}
		}
		_doQuery ('alter table images drop column description_image');
	}

	/**
	 * Version 1.1.0 à 1.2.0
	 */
	public function process1_1_0_to_1_2_0 () {
		_doQuery ('RENAME TABLE images TO cms_images');
	}
}