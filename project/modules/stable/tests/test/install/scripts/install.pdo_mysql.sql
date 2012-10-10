CREATE TABLE `testforeignkeytype` (
  type_test int(11) NOT NULL auto_increment,
  caption_typetest varchar(255) NOT NULL default '',
  PRIMARY KEY  (type_test)
) ENGINE=InnoDB  CHARACTER SET utf8;

CREATE TABLE `testmain` (
  id_test int(11) NOT NULL auto_increment,
  type_test int(11) NOT NULL default '0',
  titre_test varchar(255) NOT NULL default '',
  description_test text NOT NULL,
  date_test varchar(8) NOT NULL default '',
  version_test int NOT NULL,
  PRIMARY KEY  (id_test)
) ENGINE=InnoDB CHARACTER SET utf8;

CREATE TABLE `testhistory` (
  `id_test` int(10) NOT NULL,
  `time_date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `result` tinyint(1) NOT NULL,
  `exception` varchar(255) default NULL,
  `timing` varchar(255) default NULL,
  `id_history` int(15) NOT NULL auto_increment,
  PRIMARY KEY   (`id_history`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1164 ;

CREATE TABLE `testlevel` (
  `id_ltest` int(10) NOT NULL,
  `caption_ltest` varchar(255) NOT NULL,
  `email_ltest` varchar(512) default NULL,
  PRIMARY KEY  (`id_ltest`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `testlevel` (`id_ltest`, `caption_ltest`, `email_ltest`) VALUES
(1, 'Critique', ''),
(2, 'Important', ''),
(3, 'Moyen', ''),
(4, 'Bas', ''),
(5, 'Très bas', '');

CREATE TABLE  `test` (
  `id_test` int(11) NOT NULL auto_increment,
  `caption_test` varchar(255) NOT NULL,
  `type_test` varchar(255) NOT NULL,
  `id_ctest` int(11) NOT NULL,
  `id_ltest` int(11) NOT NULL,
  PRIMARY KEY  (`id_test`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

CREATE TABLE `testautodao` (
  id_test int(11) NOT NULL auto_increment,
  type_test int(11) NOT NULL default '0',
  titre_test varchar(255) NOT NULL default '',
  description_test text NOT NULL,
  date_test varchar(8) NOT NULL default '',
  nullable_test int(11),
  PRIMARY KEY  (id_test)
) ENGINE=InnoDB CHARACTER SET utf8;

CREATE TABLE `testdatetime` (
  `id_dtt` int(11) NOT NULL auto_increment,
  `date_dtt` date default NULL,
  `datetime_dtt` datetime default NULL,
  `time_dtt` time default NULL,
  PRIMARY KEY  (`id_dtt`)
) ENGINE=InnoDB CHARACTER SET utf8;

CREATE TABLE `testcategory` (
`id_ctest` INT NOT NULL AUTO_INCREMENT ,
`caption_ctest` VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( `id_ctest` )
) ENGINE = InnoDB; 

INSERT INTO `testdatetime` (
	`id_dtt` ,
	`date_dtt` ,
	`datetime_dtt` ,
	`time_dtt`
)
VALUES (
	NULL , '2007-11-21', '2007-11-29 10:12:22', '10:12:22'
), (
	NULL , '2007-11-20', '2007-11-20 10:12:42', '10:12:42'
);