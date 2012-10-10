CREATE TABLE `moduleserver` (
  `id_export` INT AUTO_INCREMENT NOT NULL,
  `module_name` varchar(255) NOT NULL,
  `module_version` varchar(255) NULL,
  `module_description` varchar(255) NULL,
   PRIMARY KEY(`id_export`)
) CHARACTER SET utf8;
