DROP TABLE IF EXISTS `logreader_files`;
CREATE TABLE `logreader_files` (
  `id_file` int(10) unsigned NOT NULL auto_increment,
  `path_file` varchar(255) NOT NULL,
  `rotation_file` varchar(150) default NULL,
  `lastread_file` int(11) unsigned default NULL,
  `lastline_file` int(11) unsigned default NULL,
  `lastfirstline_file` TEXT default NULL,
  `type_file` varchar(50) NOT NULL,
  PRIMARY KEY  (`id_file`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;