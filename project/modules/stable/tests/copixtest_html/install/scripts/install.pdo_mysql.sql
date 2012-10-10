CREATE TABLE `copixtesthtml` (
  `id_test` int(20) NOT NULL,
  `url` varchar(200) NOT NULL,
  `param_post` varchar(100) default NULL,
  `param_file` varchar(200) default NULL,
  `param_cookies` varchar(200) character set utf8 default NULL,
  `proxy` tinyint(1) default NULL,
  `domain` varchar(512) NOT NULL,
  `path` varchar(512) default NULL,
  `session` int(16) default NULL,
  PRIMARY KEY  (`id_test`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `copixtesthtmlsession` (
  `id_session` int(16) NOT NULL auto_increment,
  `caption_session` varchar(256) NOT NULL,
  `login_session` int(16) NOT NULL,
  `logout_session` int(16) default NULL,
  PRIMARY KEY  (`id_session`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

CREATE TABLE `copixtesthtmlbody` (
  `id_test` int(20) NOT NULL,
  `id_tag` int(10) NOT NULL,
  `path_tag` varchar(255) NOT NULL,
  `name_tag` varchar(255) NOT NULL,
  `attributes_tag` varchar(4096) default NULL,
  `checkType` varchar(100) NOT NULL,
  `contains` varchar(16384) character set utf8 collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id_test`,`id_tag`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Recense les balises que l''on veut vérifier sur le body ';

CREATE TABLE `copixtesthtmlheader` (
  `id_test` int(20) NOT NULL,
  `id_mark` int(10) NOT NULL,
  `value_mark` varchar(500) NOT NULL,
  PRIMARY KEY  (`id_test`,`id_mark`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Recense les balises que l''on veut vérifier sur l''en-tête d';

CREATE TABLE `copixtesthtmldomain` (
  `caption_domain` varchar(255) NOT NULL,
  `url_domain` varchar(512) NOT NULL,
  PRIMARY KEY  (`url_domain`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;