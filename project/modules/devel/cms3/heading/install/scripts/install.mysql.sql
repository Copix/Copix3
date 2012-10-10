CREATE TABLE `cms_headingelementinformations` (
  `id_hei` int(11) NOT NULL auto_increment,
  `id_helt` int(11) NOT NULL,
  `type_hei` varchar(50) NOT NULL,
  `public_id_hei` int(11) default NULL,
  `site_id_hei` varchar(255) NOT NULL,
  `parent_heading_public_id_hei` int(11) default NULL,
  `author_id_create_hei` varchar(255),
  `author_handler_create_hei` varchar(255),
  `author_caption_create_hei` varchar(255),
  `date_create_hei` datetime NOT NULL,
  `author_id_update_hei` varchar(255),
  `author_handler_update_hei` varchar(255),
  `author_caption_update_hei` varchar(255),
  `date_update_hei` datetime NOT NULL,
  `comment_hei` text,
  `caption_hei` varchar(255) NOT NULL,
  `title_hei` varchar(255) default NULL,
  `menu_caption_hei` varchar(255) default NULL,
  `published_date_hei` datetime default NULL,
  `end_published_date_hei` datetime default NULL,
  `status_hei` int(11) NOT NULL,
  `version_hei` int(11) NOT NULL,
  `from_version_hei` int(11) NOT NULL,
  `show_in_menu_hei` int(1) NOT NULL,
  `menu_html_class_name_hei` varchar(255) default NULL,
  `base_url_hei` varchar(255) default NULL,
  `url_id_hei` varchar(255) default NULL,
  `theme_id_hei` varchar(255) default NULL,
  `order_hei` int(11) default NULL,
  `display_order_hei` int(11) default 1,
  `tags_inherited_hei` int(11) default '1',
  `credentials_inherited_hei` tinyint(4) default '1',
  `target_hei` tinyint(4) default '0',
  `target_params_hei` varchar(255) default NULL,
  `hierarchy_hei` varchar(255) default NULL,
  `hierarchy_level_hei` int(11) default NULL,
  `robots_hei` varchar(255) default NULL,
  PRIMARY KEY  (`id_hei`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
ALTER TABLE `cms_headingelementinformations` ADD INDEX ( `type_hei` );
ALTER TABLE `cms_headingelementinformations` ADD INDEX ( `show_in_menu_hei` );
ALTER TABLE `cms_headingelementinformations` ADD INDEX ( `public_id_hei` );
ALTER TABLE `cms_headingelementinformations` ADD INDEX ( `parent_heading_public_id_hei` );
ALTER TABLE `cms_headingelementinformations` ADD INDEX ( `status_hei` );
ALTER TABLE `cms_headingelementinformations` ADD INDEX ( `version_hei` );
ALTER TABLE `cms_headingelementinformations` ADD COLUMN description_hei TEXT DEFAULT NULL;
ALTER TABLE `cms_headingelementinformations` ADD CONSTRAINT FOREIGN KEY (`parent_heading_public_id_hei`) REFERENCES `cms_headingelementinformations` (`public_id_hei`);

CREATE TABLE `cms_headings` (
  `id_heading` int(11) NOT NULL auto_increment,
  `public_id_hei` int(11) default NULL,
  `home_heading` int(11) default NULL,
  `breadcrumb_show_heading` TINYINT UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY  (`id_heading`)
)  ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `cms_headings` ADD INDEX ( `public_id_hei` );
ALTER TABLE `cms_headings` ADD CONSTRAINT FOREIGN KEY (`public_id_hei`) REFERENCES `cms_headingelementinformations` (`public_id_hei`);


INSERT INTO `cms_headingelementinformations` (
`id_hei` ,
`id_helt` ,
`type_hei` ,
`public_id_hei` ,
`site_id_hei` ,
`parent_heading_public_id_hei` ,
`author_id_create_hei` ,
`author_handler_create_hei` ,
`author_caption_create_hei` ,
`date_create_hei` ,
`author_id_update_hei` ,
`author_handler_update_hei` ,
`author_caption_update_hei` ,
`date_update_hei` ,
`comment_hei` ,
`caption_hei` ,
`title_hei` ,
`published_date_hei` ,
`end_published_date_hei` ,
`status_hei` ,
`version_hei` ,
`from_version_hei` ,
`show_in_menu_hei` ,
`menu_html_class_name_hei` ,
`base_url_hei` ,
`url_id_hei` ,
`theme_id_hei` ,
`order_hei` ,
`display_order_hei` ,
`tags_inherited_hei` ,
`credentials_inherited_hei`,
`hierarchy_hei`,
`hierarchy_level_hei`,
`robots_hei`
)
VALUES (
NULL , '1', 'heading', '0', '0', NULL, NULL , NULL , NULL , '2009-03-13 15:05:57', NULL , NULL , NULL , '2009-03-13 15:05:57', NULL , 'Rubrique principale', NULL , NULL , NULL , '3', '0', '0', '0', NULL , NULL , NULL , NULL , NULL , '1', '1', '1', '0', '0', ''
);


INSERT INTO `cms_headings` (`id_heading`, `public_id_hei`) VALUES
(1, 0);


CREATE TABLE `cms_links` (
  `id_link` int(11) NOT NULL auto_increment,
  `href_link` varchar(255) default NULL,
  `not_rewritten_link` tinyint(1) default NULL,
  `linked_public_id_hei` int(11) default NULL,
  `module_link` varchar(255) DEFAULT NULL,
  `public_id_hei` int(11) default NULL,  
  `extra_link` varchar(255) default NULL,
  PRIMARY KEY  (`id_link`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
ALTER TABLE `cms_links` ADD COLUMN `caption_link` TINYINT UNSIGNED DEFAULT NULL,
ADD COLUMN `url_link` TINYINT UNSIGNED DEFAULT NULL;

ALTER TABLE `cms_links` ADD CONSTRAINT FOREIGN KEY (`public_id_hei`) REFERENCES `cms_headingelementinformations` (`public_id_hei`);


CREATE TABLE `cms_headingelementinformations_menus` (
  `id_hem` int(11) NOT NULL auto_increment,
  `type_hem` varchar(255) NOT NULL,
  `public_id_hei` int(11) NOT NULL,
  `public_id_hem` int(11) NOT NULL,
  `level_hem` int(11) default NULL,
  `depth_hem` int(11) default NULL,
  `portlet_hem` int(11) default '0',
  `template_hem` varchar(255) default NULL,
  `class_hem` varchar(255) default NULL,
  `is_empty_hem` tinyint(4) default '0',
  `modules_hem` text,
  PRIMARY KEY  (`id_hem`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `cms_headingelementinformations_menus` ADD CONSTRAINT FOREIGN KEY (`public_id_hei`) REFERENCES `cms_headingelementinformations` (`public_id_hei`);

CREATE TABLE IF NOT EXISTS `cms_headingelementinformations_credentials` (
  `public_id_hei` int(11) NOT NULL,
  `id_group` int(11) NOT NULL,
  `value_credential` varchar(255) NOT NULL,
  PRIMARY KEY  (`public_id_hei`,`id_group`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `cms_headingelementinformations_credentials` ADD CONSTRAINT FOREIGN KEY (`public_id_hei`) REFERENCES `cms_headingelementinformations` (`public_id_hei`);

INSERT INTO `cms_headingelementinformations_credentials` (`public_id_hei`, `id_group`, `value_credential`) VALUES
(0, 1, '50'),
(0, 2, '30'),
(0, 3, '20');

CREATE TABLE IF NOT EXISTS `cms_sitemaps` (
  `id` int(11) NOT NULL auto_increment,
  `sitemap_link` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cms_sitemaps_links` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `cms_headingelementinformations_credentials` CHANGE `id_group` `id_group` VARCHAR( 100 ) NOT NULL ;
ALTER TABLE `cms_headingelementinformations_credentials` ADD `group_handler` VARCHAR( 100 ) NOT NULL DEFAULT 'auth|dbgrouphandler';
 ALTER TABLE `cms_headingelementinformations_credentials` DROP PRIMARY KEY ,
ADD PRIMARY KEY ( `public_id_hei` , `id_group` , `group_handler` ) ;

CREATE TABLE `cms_actions` (
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
  `version_action` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cms_actions_extras` (
  `id_action` int(10) unsigned NOT NULL,
  `id_extra` varchar(100) NOT NULL,
  `value_extra` varchar(255) NOT NULL,
  PRIMARY KEY (`id_action`,`id_extra`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cms_actions_profiles` (
  `id_profile` varchar(100) NOT NULL,
  `id_action` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_profile`,`id_action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cms_actions_users` (
  `id_action` int(10) unsigned NOT NULL,
  `id_user` int(10) unsigned NOT NULL,
  `login_user` varchar(100) NOT NULL,
  `userhandler_user` varchar(100) NOT NULL,
  PRIMARY KEY (`id_action`,`userhandler_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cms_headingelementinformations_linkedelements` (
 `public_id_hei` int(10) unsigned NOT NULL,
 `linked_public_id_hei` int(10) unsigned NOT NULL,
 PRIMARY KEY (`public_id_hei`,`linked_public_id_hei`),
 KEY `public_id_hei` (`public_id_hei`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;