CREATE DATABASE pixel_memories;

USE pixel_memories;

CREATE TABLE admins (
id INT AUTO_INCREMENT PRIMARY KEY,
usuario VARCHAR(50),
senha VARCHAR(255)
);  


CREATE TABLE IF NOT EXISTS fotos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    caminho_arquivo VARCHAR(255) NOT NULL,
    data_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO admins(usuario,senha)
VALUES('admin','123');