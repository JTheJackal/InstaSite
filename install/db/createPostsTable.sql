--
-- Create table for storing the posts
--

CREATE TABLE Posts (
    `postid` INT NOT NULL AUTO_INCREMENT,
    `post` VARCHAR(150) NULL DEFAULT '',
    `image` VARCHAR(600) NULL DEFAULT '',
    `description` VARCHAR(700) NULL DEFAULT '',
    `short_description` VARCHAR(100) NULL DEFAULT '',
    `postdate` VARCHAR(50) NULL DEFAULT '',
    `postedby` VARCHAR(50) NULL DEFAULT '',
    `likes` VARCHAR(50) NULL DEFAULT '',
    `title` VARCHAR(50) NULL DEFAULT '',
    `source` VARCHAR(150) NULL DEFAULT '',
    PRIMARY KEY (postid)
)COLLATE='utf8_bin';