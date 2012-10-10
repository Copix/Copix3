CREATE TABLE `forms_playground` (
  `id` int(11) NOT NULL auto_increment,
  `caption` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `number` integer NULL,
  `istestordie` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) CHARACTER SET utf8 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `forms_playground`
--

INSERT INTO `forms_playground` (`id`, `caption`, `description`, `istestordie`) VALUES 
(1, 'premier', 'Premier élément','die'),
(2, 'second', 'second élément','test'),
(3, 'troisième', 'troisième élément','die'),
(4, 'quatrième', 'quatrième élément','test'),
(5, 'dernier', 'dernier élément','die');


CREATE TABLE `forms_playground_multiplepk` (
  `id` int(11) NOT NULL auto_increment,
  `number` integer NOT NULL,
  `caption` varchar(255)  NOT NULL,
  `description` varchar(255)  NOT NULL,
  `istestordie` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`,`number`)
) CHARACTER SET utf8 AUTO_INCREMENT=6 ;

INSERT INTO `forms_playground_multiplepk` (`id`,`number`, `caption`, `description`, `istestordie`) VALUES 
(1, 1, 'premier', 'Premier élément','die'),
(2, 2, 'second', 'second élément','test'),
(3, 3, 'troisième', 'troisième élément','die'),
(4, 4, 'quatrième', 'quatrième élément','test'),
(5, 5, 'dernier', 'dernier élément','die');
