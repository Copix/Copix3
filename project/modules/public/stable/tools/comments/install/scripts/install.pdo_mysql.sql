CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL auto_increment,
  `content_comment` text collate utf8_unicode_ci NOT NULL,
  `format_comment` varchar(5) collate utf8_unicode_ci default NULL,
  `authorlogin_comment` varchar(20) collate utf8_unicode_ci NOT NULL,
  `authoremail_comment` varchar(255) collate utf8_unicode_ci NOT NULL,
  `authorsite_comment` varchar(255) collate utf8_unicode_ci default NULL,
  `page_comment` varchar(255) collate utf8_unicode_ci NOT NULL,
  `date_comment` varchar(14) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`comment_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE commentscaptcha (
	captcha_id int(11) NOT NULL auto_increment,
	captcha_question varchar(255) NOT NULL,
	captcha_answer varchar(255) NOT NULL,
	PRIMARY KEY (captcha_id)
);


CREATE TABLE commentslocked (
  locked_id int(11) NOT NULL auto_increment,
  locked_page_comment varchar(60) NOT NULL,
  PRIMARY KEY  (locked_id)
) ;
