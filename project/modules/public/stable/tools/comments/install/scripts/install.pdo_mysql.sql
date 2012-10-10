CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL auto_increment,
  `content_comment` text  NOT NULL,
  `format_comment` varchar(5)  default NULL,
  `authorlogin_comment` varchar(20)  NOT NULL,
  `authoremail_comment` varchar(255)  NOT NULL,
  `authorsite_comment` varchar(255)  default NULL,
  `page_comment` varchar(255)  NOT NULL,
  `date_comment` varchar(14)  NOT NULL,
  PRIMARY KEY  (`comment_id`)
) CHARACTER SET utf8;


CREATE TABLE commentscaptcha (
	captcha_id int(11) NOT NULL auto_increment,
	captcha_question varchar(255) NOT NULL,
	captcha_answer varchar(255) NOT NULL,
	PRIMARY KEY (captcha_id)
) CHARACTER SET utf8;


CREATE TABLE commentslocked (
  locked_id int(11) NOT NULL auto_increment,
  locked_page_comment varchar(60) NOT NULL,
  PRIMARY KEY  (locked_id)
) CHARACTER SET utf8;
