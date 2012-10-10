CREATE TABLE webservices (
  id_webservices int(11) NOT NULL AUTO_INCREMENT,
  name_webservices varchar(32) not NULL,
  file_webservices varchar(255) NOT NULL,
  class_webservices varchar(255) NOT NULL,
  PRIMARY KEY  (id_webservices)
) CHARACTER SET utf8;
 ALTER TABLE `webservices` ADD INDEX ( `name_webservices` )  