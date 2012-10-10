CREATE TABLE `blog_ticket` (
    `id_blog`      int not null auto_increment,
    `heading_blog` varchar (50) NOT NULL,
    `title_blog`   varchar (50) NOT NULL,
    `content_blog` text NOT NULL,
    `author_blog`  varchar (50) NOT NULL,
    `date_blog`    varchar (14) NOT NULL,
    `tags_blog`    varchar (255) default '',
    `typesource_blog` varchar(50) default 'wiki',
    PRIMARY KEY (`id_blog`)
) CHARACTER SET utf8;

CREATE TABLE `blog_heading` (
    `heading_blog` varchar(50),
    `description_blog` varchar (512),
    PRIMARY KEY (`heading_blog`)
) CHARACTER SET utf8;
