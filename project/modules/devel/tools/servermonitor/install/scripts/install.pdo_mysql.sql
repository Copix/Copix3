CREATE TABLE `servermonitorrequest` (
  `id_smr` int(11) NOT NULL auto_increment,
  `url_smr` varchar(255) NOT NULL,
  `data_smr` text NOT NULL,
  `datetime_smr` datetime NOT NULL,
  `module_smr` varchar(255) NOT NULL,
  `group_smr` varchar(255) NOT NULL,
  `action_smr` varchar(255) NOT NULL,
  `closed_smr` tinyint(1) NOT NULL,
  `duration_smr` int(11) NOT NULL,
  PRIMARY KEY  (`id_smr`)
)
