<?php
/**
 * Mises à jour du module document
 */
class CopixModuleInstallerDocument extends CopixAbstractModuleInstaller {
	/**
	 * Version 1.0.0 à 1.1.0
	 */
	public function process1_0_0_to_1_1_0 () {
		$daoHeading = DAOheadingelementinformation::instance ();
		foreach (DAOdocument::instance ()->findAll () as $doc) {
			$records = $daoHeading->findBy (_daoSP ()->addCondition ('id_helt', '=', $doc->id_doc)->addcondition ('type_hei', '=', 'document'));
			if (count ($records) == 1) {
				$record = $records[0];
				$record->description_hei = $doc->description_doc;
				$daoHeading->update ($record);
			} else {
				_log ('Le document "' . $doc->id_doc . '" n\'a pas d\'enregistrement dans headingelementinformation.', 'errors');
			}
		}
		_doQuery ('alter table document drop column description_doc');
	}

	public function process1_1_0_to_1_2_0 () {
		_doQuery ('RENAME TABLE document TO cms_documents');
		_doQuery ('ALTER TABLE cms_documents CHANGE id_doc id_document int(11) NOT NULL auto_increment');
		_doQuery ('ALTER TABLE cms_documents CHANGE file_doc file_document VARCHAR( 250 ) NOT NULL');
		_doQuery ('ALTER TABLE cms_documents CHANGE size_doc size_document int(11) default NULL');
	}
}