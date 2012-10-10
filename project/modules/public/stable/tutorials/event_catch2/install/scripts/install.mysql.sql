create table if not exists tutorial_event2 (
	event_id integer NOT NULL auto_increment,
	titre varchar(30),
    dtcreation date,
	information varchar(30),
 	PRIMARY KEY  (event_id)
);
