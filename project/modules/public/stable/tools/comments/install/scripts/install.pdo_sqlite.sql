CREATE TABLE comments (
  comment_id INTEGER PRIMARY KEY AUTOINCREMENT,
  content_comment text  NOT NULL,
  format_comment varchar(5)  default NULL,
  authorlogin_comment varchar(20)  NOT NULL,
  authoremail_comment varchar(255)  NOT NULL,
  authorsite_comment varchar(255)  default NULL,
  page_comment varchar(255)  NOT NULL,
  date_comment varchar(14)  NOT NULL
) ;

CREATE TABLE commentscaptcha (
	captcha_id INTEGER PRIMARY KEY AUTOINCREMENT,
	captcha_question varchar(255) NOT NULL,
	captcha_answer varchar(255) NOT NULL
);


CREATE TABLE commentslocked (
  locked_id INTEGER PRIMARY KEY AUTOINCREMENT,
  locked_page_comment varchar(60) NOT NULL
) ;





