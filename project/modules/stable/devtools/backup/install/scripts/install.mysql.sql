CREATE TABLE `backup_profiles` (
  `id_profile` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `caption_profile` varchar(50) NOT NULL,
  `id_type` varchar(20) NOT NULL,
  `filename_profile` varchar(100) DEFAULT NULL,
  `dbprofile_profile` varchar(100) NOT NULL,
  `savealltables_profile` tinyint(3) unsigned NOT NULL,
  `filesPath_profile` VARCHAR( 255 ) NULL,
  PRIMARY KEY (`id_profile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `backup_profiles_email` (
  `id_profile` int(10) unsigned NOT NULL,
  `to_email` varchar(255) NOT NULL,
  `bcc_email` varchar(255) NULL,
  `subject_email` varchar(255) NOT NULL,
  PRIMARY KEY (`id_profile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `backup_profiles_tables` (
  `id_profile` int(10) unsigned NOT NULL,
  `name_table` varchar(150) NOT NULL,
  PRIMARY KEY (`id_profile`,`name_table`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `backup_profiles_files` (
 `id_file` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `id_profile` int(10) unsigned NOT NULL,
 `path_file` varchar(255) NOT NULL,
 PRIMARY KEY (`id_file`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;