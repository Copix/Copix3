CREATE TABLE storedfile (
	storedfile_id SERIAL,
	storedfile_name VARCHAR (255) NOT NULL,
	storedfile_path VARCHAR (255) NOT NULL,
	storedfile_description TEXT,
	storedfile_uploader VARCHAR(255),
	storedfile_uploaddate TIMESTAMP,
	storedfile_nbdownload INT,
	PRIMARY KEY (storedfile_id)
);	
