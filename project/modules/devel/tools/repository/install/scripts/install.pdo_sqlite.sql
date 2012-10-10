
DROP TABLE IF EXISTS storedfile;

CREATE TABLE storedfile (
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR (255),
	path VARCHAR (255),
	description TEXT,
	uploader VARCHAR(255),
	uploaddate DATETIME,
	nbdownload INT,
	category_id INT,
	subcategory_id INT
) ;	
