CREATE TABLE menu_menus (
	menu_id SERIAL,
	menu_nom VARCHAR( 50 ) NOT NULL,
	PRIMARY KEY(menu_id)
) ;

CREATE TABLE menu_rubriques (
	rub_id SERIAL,
	rub_id_parent INT NOT NULL default 0,
	rub_id_menu INT NOT NULL ,
	rub_nom VARCHAR( 50 ) NOT NULL,
	rub_classement INT,
	PRIMARY KEY (rub_id)
);
