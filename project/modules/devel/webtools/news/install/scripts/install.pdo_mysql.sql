CREATE TABLE `news` (
`id_news` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`title_news` VARCHAR( 255 ) NOT NULL ,
`summary_news` TEXT NOT NULL ,
`content_news` TEXT NOT NULL ,
`date_news` datetime NOT NULL COMMENT 'Date et heure de parution de la news',
 `heure_news` TIME NOT NULL COMMENT 'heure de parution de la news.',
 `type_news` ENUM( 'wiki', 'wysiwyg', 'text' ) NOT NULL DEFAULT 'text' COMMENT 'Indique le type de l''information stockée en base.'
) CHARACTER SET utf8;

INSERT INTO `news` (
`id_news` ,
`title_news` ,
`summary_news` ,
`content_news` ,
`date_news`
)
VALUES (
NULL , 'Première nouvelle !', 'Le résumé de la première nouvelle n''est pas très original, mais après tout, le but est de faire une simple présentation.', 'Le contenu de la seconde nouvelle n''est pas très original, mais après tout, le but est de faire une simple présentation.', '2007-11-21'
), (
NULL , 'Seconde nouvelle', 'Le résumé de la première nouvelle n''est pas très original, mais après tout, le but est de faire une simple présentation.', 'Le contenu de la seconde nouvelle n''est pas très original, mais après tout, le but est de faire une simple présentation.', '2007-11-22'
);
