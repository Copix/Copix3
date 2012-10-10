CREATE TABLE `cms_pages` (
  `id_page` int(11) NOT NULL auto_increment,
  `public_id_hei` int(11) default NULL,
  `template_page` varchar(255),
  `browser_page` varchar(255),
  `breadcrumb_type_page` TINYINT UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY  (`id_page`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `cms_pages` ADD INDEX ( `public_id_hei` );
ALTER TABLE `cms_pages` ADD CONSTRAINT FOREIGN KEY (`public_id_hei`) REFERENCES `cms_headingelementinformations` (`public_id_hei`);

 CREATE TABLE `cms_portlets` (
`id_portlet` INT( 11 ) NOT NULL AUTO_INCREMENT ,
`id_page` INT( 11 ) default NULL ,
`public_id_hei` int(11) default NULL,
`type_portlet` VARCHAR( 255 ) NOT NULL ,
`content_portlet` TEXT NOT NULL ,
`serialized_object` LONGTEXT NOT NULL ,
`variable` VARCHAR( 255 ) NOT NULL ,
`position` INT NOT NULL ,
PRIMARY KEY ( `id_portlet` )
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `cms_portlets` ADD INDEX ( `public_id_hei` );
ALTER TABLE `cms_portlets` ADD INDEX ( `id_page` );

ALTER TABLE `cms_portlets` ADD CONSTRAINT FOREIGN KEY (`public_id_hei`) REFERENCES `cms_headingelementinformations` (`public_id_hei`);
ALTER TABLE `cms_portlets` ADD CONSTRAINT FOREIGN KEY (`id_page`) REFERENCES `cms_pages` (`id_page`);

 CREATE TABLE `cms_portlets_headingelementinformations` (
`id_portlet` INT NOT NULL ,
`public_id_hei` INT( 11 ) NOT NULL,
PRIMARY KEY  (`id_portlet`,`public_id_hei`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `cms_portlets_headingelementinformations` ADD INDEX ( `public_id_hei` );
  
ALTER TABLE `cms_portlets_headingelementinformations` ADD CONSTRAINT FOREIGN KEY (`public_id_hei`) REFERENCES `cms_headingelementinformations` (`public_id_hei`);
ALTER TABLE `cms_portlets_headingelementinformations` ADD CONSTRAINT FOREIGN KEY (`id_portlet`) REFERENCES `cms_portlets` (`id_portlet`);
