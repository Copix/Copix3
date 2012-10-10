CREATE TABLE `webserviceclient` (
`ws_id`  int(11) NOT NULL auto_increment,
`name` VARCHAR(255) NOT NULL,
`wsdl` TEXT NOT NULL,
`options` TEXT NULL,
PRIMARY KEY (ws_id)
);