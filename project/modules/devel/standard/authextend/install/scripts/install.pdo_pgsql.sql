CREATE TABLE dbuserextend (
	id INT NOT NULL auto_increment,
	name VARCHAR( 255 ) NOT NULL UNIQUE,
	position INT (11) NOT NULL ,
	caption VARCHAR( 255 ) NOT NULL ,
	type VARCHAR( 25 ) NOT NULL ,
	required BOOL NOT NULL ,
	parameters TEXT NULL ,
	active BOOL NOT NULL DEFAULT 1,
	PRIMARY KEY  (id)
);

CREATE TABLE IF NOT EXISTS dbuserextendvalue (
	id_user VARCHAR( 50 ) NOT NULL ,
	id_userhandler VARCHAR( 50 ) NOT NULL ,
	value BLOB ,
	PRIMARY KEY  (id_extend, id_user, id_userhandler)
)