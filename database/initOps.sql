CREATE TABLE Users (
    
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
    displayname VARCHAR(30) NOT NULL,
    username VARCHAR(30) NOT NULL,
    userpass VARCHAR(50),
)

CREATE TABLE Posts (
    
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
    image VARCHAR(30) NOT NULL,
    title VARCHAR(30) NOT NULL,
    description VARCHAR(50),
    postdate TIMESTAMP
    postedby VARCHAR(30) NOT NULL,
)