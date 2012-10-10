CREATE TABLE simplehelp (
	id_sh SERIAL,
	title_sh VARCHAR( 100 ) NOT NULL ,
	content_sh TEXT NOT NULL ,
	page_sh VARCHAR( 255 ) NOT NULL ,
	key_sh VARCHAR( 255 ) NOT NULL ,
	PRIMARY KEY(id_sh)
) ;
