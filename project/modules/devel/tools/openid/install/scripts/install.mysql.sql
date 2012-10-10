CREATE TABLE IF NOT EXISTS `openid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `open_id` varchar(255) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `user_handler` varchar(255) NOT NULL,
  `identifier_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);

--
-- Structure de la table `openid_identifier`
--

CREATE TABLE IF NOT EXISTS `openid_identifier` (
  `id_identifier` int(11) NOT NULL AUTO_INCREMENT,
  `caption_identifier` varchar(255) NOT NULL,
  `url_identifier` varchar(255) NOT NULL,
  PRIMARY KEY (`id_identifier`)
);

--
-- Contenu de la table `openid_identifier`
--

INSERT INTO `openid_identifier` (`id_identifier`, `caption_identifier`, `url_identifier`) VALUES
(1, 'google', 'https://www.google.com/accounts/o8/id'),
(2, 'yahoo', 'https://me.yahoo.com'),
(3, 'facebook', 'https://www.facebook.com/dialog/oauth');

