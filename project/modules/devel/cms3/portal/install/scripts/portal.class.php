<?php
/**
 * Mises à jour du module portal
 */
class CopixModuleInstallerPortal extends CopixAbstractModuleInstaller {
	/**
	 * Version 1.0.0 à 1.1.0
	 */
	public function process1_0_0_to_1_1_0 () {
		ini_set ('max_execution_time', 0);
		$daoHeading = DAOcms_headingelementinformations::instance ();

		// pages
		foreach (DAOpage::instance ()->findAll () as $page) {
			$records = $daoHeading->findBy (_daoSP ()->addCondition ('id_helt', '=', $page->id_page)->addcondition ('type_hei', '=', 'page'));
			if (count ($records) == 1) {
				$record = $records[0];
				$record->description_hei = $page->description_page;
				$daoHeading->update ($record);
			} else {
				_log ('La page "' . $page->id_page . '" n\'a pas d\'enregistrement dans headingelementinformation.', 'errors');
			}
		}
		_doQuery ('alter table page drop column description_page');

		// portlets
		foreach (DAOcms_portlets::instance ()->findBy (_daoSP ()->addCondition ('public_id_hei', '!=', null)) as $portlet) {
			$records = $daoHeading->findBy (_daoSP ()->addCondition ('id_helt', '=', $portlet->id_portlet)->addcondition ('type_hei', '=', 'portlet'));
			if (count ($records) == 1) {
				$record = $records[0];
				$record->description_hei = $portlet->description_portlet;
				$daoHeading->update ($record);
			} else {
				_log ('La portlet "' . $portlet->id_portlet . '" n\'a pas d\'enregistrement dans headingelementinformation.', 'errors');
			}
		}
		_doQuery ('alter table portlet drop column description_portlet');
	}
	
	public function process1_1_0_to_1_1_1 () {
		_doQuery ('alter table portlet_headingelementinformation MODIFY COLUMN `public_id_hei` INT NOT NULL');
	}

	public function process1_1_1_to_1_2_0 () {
		_doQuery ('RENAME TABLE portlet_headingelementinformation TO cms_portlets_elements');
	}

	public function process1_2_0_to_1_2_1 () {
		_doQuery ('RENAME TABLE cms_portlets_elements TO cms_portlets_headingelementinformations');
		_doQuery ('RENAME TABLE page TO cms_pages');
		_doQuery ('RENAME TABLE portlet TO cms_portlets');
	}

	public function process1_2_1_to_1_2_2 () {
		ini_set ('max_execution_time', 0);
		// à un moment il y a du avoir un bug dans les DAO, qui retournaient des instances de CompiledDAORecordXXX au lieu de DAORecordXXX
		// le problème c'est que cette classe ne doit pas être connue, et n'est pas gérée par l'autoload
		// on remet donc le nom de classe qu'il devrait y avoir
		_doQuery ('UPDATE cms_portlets SET serialized_object = REPLACE(serialized_object, \'CompiledDAORecord\', \'DAORecord\')');

		// ajout des options date_create et date_update sur les portlets article
		$results = _doQuery ('SELECT serialized_object FROM cms_portlets WHERE type_portlet = \'PortletArticle\'');
		foreach ($results as $result) {
			$portlet = CopixXMLSerializer::unserialize ($result->serialized_object);
			$portlet->setOption ('date_create', $portlet->getOption ('date_create', false));
			$portlet->setOption ('date_update', $portlet->getOption ('date_update', false));
			$params = array (':object' => CopixXMLSerializer::serialize ($portlet), ':id' => $portlet->getId ());
			_doQuery ('UPDATE cms_portlets SET serialized_object = :object WHERE id_portlet = :id', $params);
		}
	}
	
	public function process1_2_2_to_1_2_3 () {
		_doQuery ('ALTER TABLE `cms_pages` ADD `breadcrumb_type_page` TINYINT UNSIGNED NOT NULL DEFAULT \'1\'');
	}
}