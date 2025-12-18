CREATE DATABASE IF NOT EXISTS projecte_db;
USE projecte_db;

CREATE TABLE users (
id INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(100) NOT NULL,
email VARCHAR(255) NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


CREATE TABLE articles (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
title VARCHAR(255) NOT NULL,
content TEXT NOT NULL,
published_at DATETIME DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;


INSERT INTO users (username, email) VALUES ('lluc', 'l.sanchez5@sapalomera.cat'), ('francesc', 'fbarragan@sapalomera.cat');
INSERT INTO articles (user_id, title, content) VALUES
(1, 'Primer article PROVA', 'Això és una prova'),
(2, 'Segon article PROVA', 'Això és una prova');
