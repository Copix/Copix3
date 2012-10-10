CREATE TABLE `dbgroup` (
  `id_dbgroup` int(11) NOT NULL auto_increment,
  `caption_dbgroup` varchar(255) NOT NULL,
  `description_dbgroup` text NULL,
  `superadmin_dbgroup` tinyint(4) NOT NULL,
  `public_dbgroup` tinyint(4) NOT NULL,
  `registered_dbgroup` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id_dbgroup`)
) ENGINE=MyISAM CHARSET=latin1 AUTO_INCREMENT=2 ;

INSERT INTO `dbgroup` (`id_dbgroup`, `caption_dbgroup`, `description_dbgroup`, `superadmin_dbgroup`, `public_dbgroup`, `registered_dbgroup`) VALUES (1, 'Admin', 'Groupe administrateur', 1, 0, 0);

CREATE TABLE `dbgroup_users` (
  `id_dbgroup` int(11) NOT NULL,
  `userhandler_dbgroup` varchar(255) NOT NULL,
  `user_dbgroup` varchar(255) NOT NULL
) ENGINE=MyISAM;

INSERT INTO `dbgroup_users` (`id_dbgroup`, `userhandler_dbgroup`, `user_dbgroup`) VALUES (1, 'auth|dbuserhandler', '1');

CREATE TABLE `dbuser` (
  `id_dbuser` int(11) NOT NULL auto_increment,
  `login_dbuser` varchar(32) NOT NULL,
  `password_dbuser` varchar(32) NOT NULL,
  `email_dbuser` varchar(255) NOT NULL,
  `enabled_dbuser` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id_dbuser`),
  UNIQUE KEY `login` (`login_dbuser`)
) ENGINE=MyISAM CHARSET=latin1 AUTO_INCREMENT=2 ;

INSERT INTO `dbuser` (`id_dbuser`, `login_dbuser`, `password_dbuser`, `email_dbuser`, `enabled_dbuser`) VALUES (1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'webmaster@yourhost.com', 1);

CREATE TABLE modulecredentials (
    id_mc INT(11) AUTO_INCREMENT,
    module_mc VARCHAR(255),
    name_mc VARCHAR(255) NOT NULL,
    primary key(id_mc) 
) ENGINE=MyISAM CHARSET=latin1;

CREATE TABLE modulecredentialsvalues (
    id_mcv INT(11) AUTO_INCREMENT,
    value_mcv VARCHAR(255) NOT NULL,
    id_mc INT NOT NULL,
    level_mcv INT,
    primary key (id_mcv)
)  ENGINE=MyISAM CHARSET=latin1;

CREATE TABLE modulecredentialsoverpass (
    id_mco INT(11) AUTO_INCREMENT,
    id_mc INT(11),
    overpass_id_mc INT(11),
    overpath_id_mc INT(11),
    primary key(id_mco)
) ENGINE=MyISAM CHARSET=latin1;

CREATE TABLE modulecredentialsgroups (
    id_mcg INT(11) AUTO_INCREMENT,
    id_mc INT NOT NULL,
    id_mcv INT(11),
    handler_group VARCHAR(255),
    id_group VARCHAR(255),
    primary key(id_mcg)
) ENGINE=MyISAM CHARSET=latin1;


CREATE TABLE dynamiccredentials (
    id_dc INT(11) AUTO_INCREMENT,
    name_dc VARCHAR(255) NOT NULL,
    primary key(id_dc) 
) ENGINE=MyISAM CHARSET=latin1;

CREATE TABLE dynamiccredentialsvalues (
    id_dcv INT(11) AUTO_INCREMENT,
    value_dcv VARCHAR(255) NOT NULL,
    id_dc INT NOT NULL,
    level_dcv INT,
    primary key (id_dcv)
)  ENGINE=MyISAM CHARSET=latin1;

CREATE TABLE dynamiccredentialsgroups (
    id_dcg INT(11) AUTO_INCREMENT,
    id_dc INT NOT NULL,
    id_dcv INT(11),
    handler_group VARCHAR(255),
    id_group VARCHAR(255),
    primary key(id_dcg)
) ENGINE=MyISAM CHARSET=latin1;
