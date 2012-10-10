<?php
/**
 * Mises à jour du module cms_rss
 */
class CopixModuleInstallerCMS_RSS extends CopixAbstractModuleInstaller {
	/**
	 * Version 1.0.0 à 1.1.0
	 */
	public function process1_0_0_to_1_1_0 () {
		$daoHeading = DAOheadingelementinformation::instance ();
		foreach (DAOcms_rss::instance ()->findAll () as $rss) {
			$records = $daoHeading->findBy (_daoSP ()->addCondition ('id_helt', '=', $rss->id_rss)->addcondition ('type_hei', '=', 'rss'));
			if (count ($records) == 1) {
				$record = $records[0];
				$record->description_hei = $rss->description_rss;
				$daoHeading->update ($record);
			} else {
				_log ('Le flux RSS "' . $rss->id_rss . '" n\'a pas d\'enregistrement dans headingelementinformation.', 'errors');
			}
		}
		_doQuery ('alter table cms_rss drop column description_rss');
	}

	public function process1_1_0_to_1_2_0 () {
		_doQuery ('RENAME TABLE cms_rss_headingelement TO cms_rss_headingelementinformations');
	}
}