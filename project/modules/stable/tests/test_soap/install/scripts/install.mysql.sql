CREATE TABLE `testsoap` (
  `id_test` int(15) NOT NULL,
  `address_soap` varchar(255) NOT NULL,
  `proxy` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id_test`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `testsoapfunctions` (
  `id_test` int(20) NOT NULL,
  `name_function` varchar(255) NOT NULL,
  `parameters` varchar(255) default NULL,
  `parameters_test` varchar(255) default NULL,
  `return_test` varchar(255) default NULL,
  `checktype` varchar(50) default NULL,
  PRIMARY KEY  (`id_test`,`name_function`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;