
DROP TABLE IF EXISTS storedfile;

CREATE TABLE storedfile (
	storedfile_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	storedfile_name VARCHAR (255),
	storedfile_path VARCHAR (255),
	storedfile_description TEXT,
	storedfile_uploader VARCHAR(255),
	storedfile_uploaddate DATETIME,
	storedfile_nbdownload INT	
) ENGINE = INNODB;	
