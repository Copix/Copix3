CREATE TABLE `search_domain` (
  `domain_id`  BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `domain_name` VARCHAR(50) NOT NULL,
  `parent_id` BIGINT(20) UNSIGNED NULL,
  PRIMARY KEY(domain_id),
  INDEX domain_name_index(domain_name)
)
CHARACTER SET utf8;

CREATE TABLE `search_objectindex` (
  `objectindex_id`  BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `domain_id`  BIGINT(20) UNSIGNED,
  `objectindex_name` VARCHAR(255) NOT NULL,
  `objectindex_type` VARCHAR(45) NOT NULL,
  `objectindex_url` VARCHAR(255) NOT NULL,
  `objectindex_caption` VARCHAR(255) NOT NULL,
  `objectindex_path` VARCHAR(255) NOT NULL,
  PRIMARY KEY(objectindex_id),
  INDEX objectindex_name_index(objectindex_name),
  FOREIGN KEY(domain_id)
    REFERENCES search_domain(domain_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
)
CHARACTER SET utf8;

CREATE TABLE `search_wordlist` (
  `wordlist_id`  BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `wordlist_text` VARCHAR(50) NOT NULL,
  `wordlist_sortvalue` VARCHAR(50) NOT NULL,
  `wordlist_phonetic` VARCHAR(100) NOT NULL,
  PRIMARY KEY(wordlist_id),
  INDEX wordlist_text_index(wordlist_text)
)
CHARACTER SET utf8;

CREATE TABLE `search_objectcredential` (
  `objectcredential_id`  BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `objectindex_id`  BIGINT(20) UNSIGNED NOT NULL,
  `objectcredential_credential` VARCHAR(255) NOT NULL,
  PRIMARY KEY(objectcredential_id),
  INDEX search_objectcredential_FKIndex1(objectindex_id),
  FOREIGN KEY(objectindex_id)
    REFERENCES search_objectindex(objectindex_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
)
CHARACTER SET utf8;

CREATE TABLE `search_map` (
  `objectindex_id`  BIGINT(20) UNSIGNED NOT NULL,
  `wordlist_id`  BIGINT(20) UNSIGNED NOT NULL,
  `map_point` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY(objectindex_id, wordlist_id),
  FOREIGN KEY(objectindex_id)
    REFERENCES search_objectindex(objectindex_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(wordlist_id)
    REFERENCES search_wordlist(wordlist_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
)
CHARACTER SET utf8;