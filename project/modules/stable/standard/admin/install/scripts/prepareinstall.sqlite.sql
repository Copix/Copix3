#DROP TABLE IF EXISTS `copixmodule`;
CREATE TABLE copixmodule (
  name_cpm varchar(255) NOT NULL default '',
  path_cpm varchar(255) NOT NULL default '', 
  version_cpm varchar(255) NULL, 
  PRIMARY KEY  (name_cpm)
);

#DROP TABLE IF EXISTS `copixconfig`;
CREATE TABLE `copixconfig` (
  `id_ccfg` varchar(255) NOT NULL default '',
  `module_ccfg` varchar(255) NOT NULL default '',
  `value_ccfg` TEXT default NULL,
  PRIMARY KEY  (`id_ccfg`)
);


DROP TABLE IF EXISTS `copixlog`;
CREATE TABLE `copixlog` (
  `id_log` INTEGER PRIMARY KEY,
  `date_log` datetime NOT NULL,
  `message_log` text NOT NULL,
  `profile_log` varchar(100) NOT NULL,
  `level_log` tinyint(3)  NOT NULL default '0',
  `type_log` varchar(100) NOT NULL
);
ALTER TABLE `copixlog` ADD INDEX ( `profile_log` ) ;

DROP TABLE IF EXISTS `copixlogextras`;
CREATE TABLE `copixlogextras` (
  `id_extra` INTEGER PRIMARY KEY,
  `id_log` bigint(20)  NOT NULL,
  `key_extra` varchar(255) NOT NULL,
  `value_extra` text NOT NULL
);

DROP TABLE IF EXISTS `copixuserpreferences`;
CREATE TABLE `copixuserpreferences` (
  `id_pref` INTEGER PRIMARY KEY,
  `id_user` varchar(50) NOT NULL,
  `id_userhandler` varchar(50) NOT NULL,
  `login_user` varchar(255) NOT NULL,
  `name_pref` varchar(50) NOT NULL,
  `value_pref` varchar(255) default NULL,
  UNIQUE (`name_pref`, `id_user`, `id_userhandler`)
);

DROP TABLE IF EXISTS `copixgrouppreferences`;
CREATE TABLE `copixgrouppreferences` (
  `id_pref` INTEGER PRIMARY KEY,
  `id_group` varchar(50) NOT NULL,
  `id_grouphandler` varchar(50) NOT NULL,
  `name_group` varchar(255) NOT NULL,
  `name_pref` varchar(50) NOT NULL,
  `value_pref` varchar(255) default NULL,
  UNIQUE (`name_pref`, `id_group`, `id_grouphandler`)
);