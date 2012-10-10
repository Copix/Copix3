<?php
/**
 * Mises à jour du module heading
 */
class CopixModuleInstallerHeading extends CopixAbstractModuleInstaller {
	/**
	 * Version 1.0.0 à 1.1.0
	 */
	public function process1_0_0_to_1_1_0 () {
		_doQuery ('ALTER TABLE headingelementcredentials CHANGE id_dbgroup id_dbgroup VARCHAR( 100 ) NOT NULL;');
		_doQuery ('ALTER TABLE headingelementcredentials ADD group_handler VARCHAR( 100 ) NOT NULL DEFAULT \'auth|dbgrouphandler\';');
		_doQuery ('ALTER TABLE headingelementcredentials DROP PRIMARY KEY , ADD PRIMARY KEY ( public_id_hei , id_dbgroup , group_handler ) ;');
	}

	/**
	 * Version 1.1.0 à 1.2.0
	 */
	public function process1_1_0_to_1_2_0 () {
		_doQuery ('ALTER TABLE link ADD COLUMN caption_link TINYINT UNSIGNED DEFAULT NULL, ADD COLUMN url_link TINYINT UNSIGNED DEFAULT NULL;');
	}

	/**
	 * Version 1.2.0 à 1.3.0
	 */
	public function process1_2_0_to_1_3_0 () {
		_doQuery ('ALTER TABLE headingelementinformation ADD COLUMN description_hei TEXT DEFAULT NULL;');
		$daoHeading = DAOheadingelementinformation::instance ();

		// répertoires
		foreach (DAOheading::instance ()->findAll () as $heading) {
			$records = $daoHeading->findBy (_daoSP ()->addCondition ('id_helt', '=', $heading->id_head)->addcondition ('type_hei', '=', 'heading'));
			if (count ($records) == 1) {
				$record = $records[0];
				$record->description_hei = $heading->description_head;
				$daoHeading->update ($record);
			} else {
				_log ('Le répertoire "' . $heading->id_heading . '" n\'a pas d\'enregistrement dans headingelementinformation.', 'errors');
			}
		}
		_doQuery ('alter table heading drop column description_head');

		// liens
		foreach (DAOlink::instance ()->findAll () as $link) {
			$records = $daoHeading->findBy (_daoSP ()->addCondition ('id_helt', '=', $link->id_link)->addcondition ('type_hei', '=', 'link'));
			if (count ($records) == 1) {
				$record = $records[0];
				$record->description_hei = $link->description_link;
				$daoHeading->update ($record);
			} else {
				_log ('Le lien "' . $link->id_link . '" n\'a pas d\'enregistrement dans headingelementinformation.', 'errors');
			}
		}
		_doQuery ('alter table link drop column description_link');
	}

	/**
	 * Version 1.3.0 à 1.4.0
	 */
	public function process1_3_0_to_1_4_0 () {
		_doQuery ('RENAME TABLE link TO cms_links');
	}

	/**
	 * Version 1.4.0 à 1.5.0
	 */
	public function process1_4_0_to_1_5_0 () {
		_doQuery ('RENAME TABLE heading TO cms_headings');
		_doQuery ('ALTER TABLE cms_headings CHANGE COLUMN `id_head` `id_heading` INTEGER  NOT NULL AUTO_INCREMENT, DROP PRIMARY KEY, ADD PRIMARY KEY (`id_heading`)');
		_doQuery ('ALTER TABLE cms_headings CHANGE COLUMN `accueil_head` `home_heading` INTEGER  DEFAULT NULL;');

		_doQuery ('RENAME TABLE headingelementcredentials TO cms_elements_credentials');
		_doQuery ('ALTER TABLE `cms_elements_credentials` CHANGE COLUMN `id_dbgroup` `id_group` VARCHAR(100) NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (`public_id_hei`, `id_group`, `group_handler`)');
		_doQuery ('ALTER TABLE cms_elements_credentials CHANGE COLUMN `credential` `value_credential` VARCHAR(255) NOT NULL');

		_doQuery ('RENAME TABLE headingelementmenu TO cms_elements_menus');

		try {
			_doQuery ('RENAME TABLE heading_sitemap TO cms_sitemaps');
			_doQuery ('RENAME TABLE heading_sitemap_links TO cms_sitemaps_links');
		} catch (Exception $e) {
			_doQuery ('CREATE TABLE IF NOT EXISTS `cms_sitemaps` (
				  `id` int(11) NOT NULL auto_increment,
				  `sitemap_link` int(11) NOT NULL,
				  PRIMARY KEY  (`id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;'
			);
			_doQuery ('CREATE TABLE IF NOT EXISTS `cms_sitemaps_links` (
				  `id` int(11) NOT NULL auto_increment,
				  `caption` varchar(255) NOT NULL,
				  `url_mode` int(1) NOT NULL,
				  `cms_link` int(11) default NULL,
				  `custom_url` varchar(255) default NULL,
				  `child_mode` int(1) NOT NULL,
				  `parent_id` int(11) default NULL,
				  `cms_heading` int(11) default NULL,
				  `new_window` int(1) default NULL,
				  `position` int(11) NOT NULL,
				  PRIMARY KEY  (`id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;'
			);
		}
	}

	public function process1_5_0_to_1_6_0 () {
		_doQuery ('ALTER TABLE headingelementinformation MODIFY COLUMN parent_heading_public_id_hei INTEGER DEFAULT NULL');
		_doQuery ('UPDATE headingelementinformation SET parent_heading_public_id_hei = null WHERE public_id_hei = 0');
		CopixPluginConfigFile::enable ('heading|cmstoolsbar');
	}

	public function process1_6_0_to_1_6_1 () {
		_doQuery ('UPDATE headingelementinformation SET theme_id_hei = CONCAT(theme_id_hei, \'|main.php\') WHERE theme_id_hei NOT LIKE \'%|%\' AND theme_id_hei IS NOT NULL');
	}

	public function process1_6_1_to_1_6_2 () {
		_doQuery ('CREATE TABLE `cms_actions` (
		  `id_action` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `url_action` varchar(255) NOT NULL,
		  `referer_action` varchar(255) NOT NULL,
		  `date_action` datetime NOT NULL,
		  `page_id_action` varchar(20) NOT NULL,
		  `type_action` tinyint(4) NOT NULL,
		  `level_action` tinyint(3) unsigned NOT NULL,
		  `element_action` text NOT NULL,
		  `public_id_hei` int(10) unsigned NOT NULL,
		  `hierarchy_action` varchar(255) NOT NULL,
		  PRIMARY KEY (`id_action`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

		_doQuery ('CREATE TABLE `cms_actions_extras` (
		  `id_action` int(10) unsigned NOT NULL,
		  `id_extra` varchar(100) NOT NULL,
		  `value_extra` varchar(255) NOT NULL,
		  PRIMARY KEY (`id_action`,`id_extra`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

		_doQuery ('CREATE TABLE `cms_actions_profiles` (
		  `id_profile` varchar(100) NOT NULL,
		  `id_action` int(10) unsigned NOT NULL,
		  PRIMARY KEY (`id_profile`,`id_action`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

		_doQuery ('CREATE TABLE `cms_actions_users` (
		  `id_action` int(10) unsigned NOT NULL,
		  `id_user` int(10) unsigned NOT NULL,
		  `login_user` varchar(100) NOT NULL,
		  `userhandler_user` varchar(100) NOT NULL,
		  PRIMARY KEY (`id_action`,`userhandler_user`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
	}

	public function process1_6_2_to_1_6_3 () {
		_doQuery ('ALTER TABLE cms_actions ADD COLUMN `version_action` int(10) unsigned NOT NULL');
	}

	public function process1_6_3_to_1_6_4 () {
		_doquery ('RENAME TABLE cms_elements_credentials TO cms_headingelementinformations_credentials');
		_doquery ('RENAME TABLE cms_elements_menus TO cms_headingelementinformations_menus');
		_doquery ('RENAME TABLE headingelementinformation TO cms_headingelementinformations');

		_doQuery ('ALTER TABLE cms_headingelementinformations ADD INDEX `id_helt`(`id_helt`)');
	}

	public function process1_6_4_to_1_6_5 () {
		if (CopixModule::isEnabled ('portal')) {
			_doQuery ('UPDATE cms_portlets SET serialized_object = REPLACE(serialized_object, \'DAORecordheadingelementinformation\', \'DAORecordcms_headingelementinformations\')');
			_doQuery ('UPDATE cms_portlets SET serialized_object = REPLACE(serialized_object, \'DAOheadingelementinformation\', \'DAOcms_headingelementinformations\')');
		}
	}
	
	public function process1_6_5_to_1_6_6 () {
		_doQuery ('
			CREATE TABLE `cms_headingelementinformations_linkedelements` (
			`public_id_hei` int(10) unsigned NOT NULL,
			`linked_public_id_hei` int(10) unsigned NOT NULL,
			PRIMARY KEY (`public_id_hei`,`linked_public_id_hei`),
			KEY `public_id_hei` (`public_id_hei`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8
		');
	}
	
	public function process1_6_6_to_1_6_7 () {
		_doQuery ('ALTER TABLE `cms_headings` ADD `breadcrumb_show_heading` TINYINT UNSIGNED NOT NULL DEFAULT \'1\'');
	}
}