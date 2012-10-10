CREATE TABLE cn_user (
  id int(11) NOT NULL auto_increment,
  login varchar(20) NOT NULL,
  password varchar(255) NOT NULL,
  description text NOT NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE cn_friend_list ( 
  friendid INT NOT NULL ,
  userid INT NOT NULL ,
  PRIMARY KEY ( `friendid` , `userid` ) 
) ;