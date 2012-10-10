CREATE TABLE `cms_mvtestings` (
  `id_mvt` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `public_id_hei` int(10) DEFAULT NULL,
  `choice_mvt` tinyint(3) unsigned NOT NULL,
  `current_mvt` tinyint(3) unsigned DEFAULT NULL,
  `conserve_mvt` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id_mvt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `cms_mvtestings` ADD CONSTRAINT FOREIGN KEY (`public_id_hei`) REFERENCES `cms_headingelementinformations` (`public_id_hei`);

CREATE TABLE `cms_mvtestings_headingelementinformations` (
  `id_element` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_mvt` int(10) unsigned NOT NULL,
  `type_element` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `value_element` varchar(255) DEFAULT NULL,
  `percent_element` tinyint(3) unsigned DEFAULT NULL,
  `show_element` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_element`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `cms_mvtestings_headingelementinformations` ADD CONSTRAINT FOREIGN KEY (`id_mvt`) REFERENCES `cms_mvtestings` (`id_mvt`);
