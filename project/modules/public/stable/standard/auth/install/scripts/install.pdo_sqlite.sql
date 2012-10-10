CREATE TABLE dbgroup (
  id_dbgroup INTEGER PRIMARY KEY AUTOINCREMENT,
  caption_dbgroup varchar(255) NOT NULL,
  description_dbgroup text NULL,
  superadmin_dbgroup tinyint(4) NOT NULL,
  public_dbgroup tinyint(4) NOT NULL,
  registered_dbgroup tinyint(4) NOT NULL
);

INSERT INTO dbgroup (caption_dbgroup, description_dbgroup, superadmin_dbgroup, public_dbgroup, registered_dbgroup) VALUES ('Admin', 'Groupe administrateur', 1, 0, 0);

CREATE TABLE dbgroup_users (
  id_dbgroup int(11) NOT NULL,
  userhandler_dbgroup varchar(255) NOT NULL,
  user_dbgroup varchar(255) NOT NULL
);

INSERT INTO dbgroup_users (id_dbgroup, userhandler_dbgroup, user_dbgroup) VALUES (1, 'auth|dbuserhandler', '1');

CREATE TABLE dbuser (
  id_dbuser INTEGER PRIMARY KEY AUTOINCREMENT,
  login_dbuser varchar(32) UNIQUE NOT NULL,
  password_dbuser varchar(32) NOT NULL,
  email_dbuser varchar(255) NOT NULL,
  enabled_dbuser tinyint(4) NOT NULL
);

INSERT INTO dbuser (login_dbuser, password_dbuser, enabled_dbuser,email_dbuser) VALUES ( 'admin', '21232f297a57a5a743894a0e4a801fc3', 1, 'root@localhost');

CREATE TABLE modulecredentials (
    id_mc INTEGER PRIMARY KEY AUTOINCREMENT,
    module_mc VARCHAR(255),
    name_mc VARCHAR(255) NOT NULL);

CREATE TABLE modulecredentialsvalues (
    id_mcv INTEGER PRIMARY KEY AUTOINCREMENT,
    value_mcv VARCHAR(255) NOT NULL,
    id_mc INT NOT NULL,
    level_mcv INT
);

CREATE TABLE modulecredentialsoverpass (
    id_mco INTEGER PRIMARY KEY AUTOINCREMENT,
    id_mc INT(11),
    overpass_id_mc INT(11),
    overpath_id_mc INT(11)
);

CREATE TABLE modulecredentialsgroups (
    id_mcg INTEGER PRIMARY KEY AUTOINCREMENT,
    id_mc INT NOT NULL,
    id_mcv INT(11),
    handler_group VARCHAR(255),
    id_group VARCHAR(255)
);


CREATE TABLE dynamiccredentials (
    id_dc INTEGER PRIMARY KEY AUTOINCREMENT,
    name_dc VARCHAR(255) NOT NULL
   
);

CREATE TABLE dynamiccredentialsvalues (
    id_dcv  INTEGER PRIMARY KEY AUTOINCREMENT,
    value_dcv VARCHAR(255) NOT NULL,
    id_dc INT NOT NULL,
    level_dcv INT
);

CREATE TABLE dynamiccredentialsgroups (
    id_dcg  INTEGER PRIMARY KEY AUTOINCREMENT,
    id_dc INT NOT NULL,
    id_dcv INT(11),
    handler_group VARCHAR(255),
    id_group VARCHAR(255)
);