<?php
/**
 * Mises à jour du module cms_form
 */
class CopixModuleInstallerForm extends CopixAbstractModuleInstaller {
	/**
	 * Version 1.0.0 à 1.1.0
	 */
	public function process1_0_0_to_1_1_0 () {
		$daoHeading = DAOcms_headingelementinformations::instance ();
		foreach (DAOcms_form::instance ()->findAll () as $form) {
			$records = $daoHeading->findBy (_daoSP ()->addCondition ('id_helt', '=', $form->cf_id)->addcondition ('type_hei', '=', 'form'));
			if (count ($records) == 1) {
				$record = $records[0];
				$record->description_hei = $form->cf_description;
				$daoHeading->update ($record);
			} else {
				_log ('Le formulaire "' . $form->cf_id . '" n\'a pas d\'enregistrement dans headingelementinformation.', 'errors');
			}
		}
		_doQuery ('alter table cms_form drop column cf_description');
	}
	/**
	 * Version 1.1.0 à 1.1.1
	 */
	public function process1_1_0_to_1_1_1 () {
		_doQuery ('ALTER TABLE `cms_form` ADD COLUMN description_hei TEXT DEFAULT NULL;');
	}
}