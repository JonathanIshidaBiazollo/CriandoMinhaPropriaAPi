DROP DATABASE IF EXISTS minha_api;
CREATE DATABASE IF NOT EXISTS minha_api;
USE minha_api;

CREATE TABLE usuarios(
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL
);

INSERT INTO usuarios(
	nome,
    email
)VALUES
('João', 'joao@email.com'),
('Maria', 'maria@email.com'),
('Pedro', 'pedro@email.com');
select * from usuarios;