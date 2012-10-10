CREATE TABLE storedfile (
	id SERIAL,
	name VARCHAR (255) NOT NULL,
	title VARCHAR (255) NOT NULL,
	path VARCHAR (255) NOT NULL,
	description TEXT,
	uploader VARCHAR(255),
	uploaddate TIMESTAMP,
	nbdownload INT,
	category_id INT,
	subcategory_id INT,
	PRIMARY KEY (id)
);	
