-- 
-- Create table for storing the user details
--

CREATE TABLE IF NOT EXISTS Users (
    `id` INT NOT NULL AUTO_INCREMENT,
    `displayname` VARCHAR(50) NULL DEFAULT '',
    `username` VARCHAR(50) NULL DEFAULT '',
    `password` VARCHAR(50) NULL DEFAULT '',
    PRIMARY KEY (id)
)COLLATE='utf8_bin';