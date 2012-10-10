create table rss_feeds(
	rss_id int PRIMARY KEY,
	rss_title varchar(50) not null,
	rss_desc text not null,
        rss_pubdate varchar(14) not null,
	rss_link varchar(255) not null,
	rss_category varchar(255) not null
);
