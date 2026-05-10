CREATE DATABASE IF NOT EXISTS pixel_memories
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE pixel_memories;

-- 👤 ADMINS
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 📂 CATEGORIAS
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT NOT NULL,
    imagem VARCHAR(255) NOT NULL
);

-- 📸 FOTOS
CREATE TABLE fotos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    caminho_arquivo VARCHAR(255) NOT NULL,
    categoria_id INT NULL,
    data_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_fotos_categorias
    FOREIGN KEY (categoria_id)
    REFERENCES categorias(id)
    ON DELETE SET NULL
    ON UPDATE CASCADE
);

-- 📌 DADOS INICIAIS
INSERT INTO categorias (nome, descricao, imagem) VALUES
('Natureza', 'Paisagens incríveis', 'fotos/natureza.jpg'),
('Cidade', 'Arquitetura urbana', 'fotos/cidade.jpg'),
('Noite', 'Luzes noturnas', 'fotos/noite.jpg');