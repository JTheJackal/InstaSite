--
-- Create table for storing the posts
--

CREATE TABLE Posts (
    `postid` INT NOT NULL AUTO_INCREMENT,
    `image` VARCHAR(600) NULL DEFAULT '',
    `description` VARCHAR(700) NULL DEFAULT '',
    `postdate` VARCHAR(50) NULL DEFAULT '',
    `postedby` VARCHAR(50) NULL DEFAULT '',
    PRIMARY KEY (postid)
)COLLATE='utf8_bin';