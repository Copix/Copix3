CREATE TABLE IF NOT EXISTS `dbgroup` (
  `id_dbgroup` int(11) NOT NULL auto_increment,
  `caption_dbgroup` varchar(255) NOT NULL,
  `description_dbgroup` text NULL,
  `superadmin_dbgroup` tinyint(4) NOT NULL,
  `public_dbgroup` tinyint(4) NOT NULL,
  `registered_dbgroup` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id_dbgroup`)
) CHARACTER SET utf8 AUTO_INCREMENT=2 ;

INSERT IGNORE INTO `dbgroup` (`id_dbgroup`, `caption_dbgroup`, `description_dbgroup`, `superadmin_dbgroup`, `public_dbgroup`, `registered_dbgroup`) VALUES (1, 'Admin', 'Groupe administrateur', 1, 0, 0);

CREATE TABLE IF NOT EXISTS `dbgroup_users` (
  `id_dbgroup` int(11) NOT NULL,
  `userhandler_dbgroup` varchar(255) NOT NULL,
  `user_dbgroup` varchar(255) NOT NULL
) CHARACTER SET utf8;

INSERT IGNORE INTO `dbgroup_users` (`id_dbgroup`, `userhandler_dbgroup`, `user_dbgroup`) VALUES (1, 'auth|dbuserhandler', '1');

CREATE TABLE IF NOT EXISTS `dbuser` (
  `id_dbuser` int(11) NOT NULL auto_increment,
  `login_dbuser` varchar(32) NOT NULL,
  `password_dbuser` varchar(32) NOT NULL,
  `email_dbuser` varchar(255) NOT NULL,
  `enabled_dbuser` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id_dbuser`),
  UNIQUE KEY `login` (`login_dbuser`)
) CHARACTER SET utf8 AUTO_INCREMENT=2 ;

INSERT IGNORE INTO `dbuser` (`id_dbuser`, `login_dbuser`, `password_dbuser`, `email_dbuser`, `enabled_dbuser`) VALUES (1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'webmaster@yourhost.com', 1);


CREATE TABLE IF NOT EXISTS modulecredentials (
    id_mc INT(11) AUTO_INCREMENT,
    module_mc VARCHAR(255),
    name_mc VARCHAR(255) NOT NULL,
    primary key(id_mc) 
) CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS modulecredentialsvalues (
    id_mcv INT(11) AUTO_INCREMENT,
    value_mcv VARCHAR(255) NOT NULL,
    id_mc INT NOT NULL,
    level_mcv INT,
    primary key (id_mcv)
) CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS modulecredentialsoverpass (
    id_mco INT(11) AUTO_INCREMENT,
    id_mc INT(11),
    overpass_id_mc INT(11),
    overpath_id_mc INT(11),
    primary key(id_mco)
) CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS modulecredentialsgroups (
    id_mcg INT(11) AUTO_INCREMENT,
    id_mc INT NOT NULL,
    id_mcv INT(11),
    handler_group VARCHAR(255),
    id_group VARCHAR(255),
    primary key(id_mcg)
) CHARACTER SET utf8;


CREATE TABLE IF NOT EXISTS dynamiccredentials (
    id_dc INT(11) AUTO_INCREMENT,
    name_dc VARCHAR(255) NOT NULL,
    primary key(id_dc) 
) CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS dynamiccredentialsvalues (
    id_dcv INT(11) AUTO_INCREMENT,
    value_dcv VARCHAR(255) NOT NULL,
    id_dc INT NOT NULL,
    level_dcv INT,
    primary key (id_dcv)
) CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS dynamiccredentialsgroups (
    id_dcg INT(11) AUTO_INCREMENT,
    id_dc INT NOT NULL,
    id_dcv INT(11),
    handler_group VARCHAR(255),
    id_group VARCHAR(255),
    primary key(id_dcg)
) CHARACTER SET utf8;
