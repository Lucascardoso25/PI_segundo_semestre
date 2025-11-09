-- Banco: webvote
CREATE DATABASE IF NOT EXISTS webvote DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE webvote;

CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  ra VARCHAR(30) NOT NULL UNIQUE,
  email VARCHAR(150) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL,
  tipo ENUM('aluno','adm') NOT NULL DEFAULT 'aluno',
  curso ENUM('Gestão Empresa','Gestão Industrial','Desenvolvimento de Software') DEFAULT NULL,
  semestre TINYINT DEFAULT NULL,
  ano YEAR DEFAULT NULL,
  foto VARCHAR(255) DEFAULT NULL,
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE representantes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NULL,
  nome VARCHAR(150) NOT NULL,
  curso ENUM('Gestão Empresa','Gestão Industrial','Desenvolvimento de Software') NOT NULL,
  semestre TINYINT NOT NULL,
  ra VARCHAR(30) NOT NULL,
  ano YEAR NOT NULL,
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

CREATE TABLE votacoes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  curso ENUM('Gestão Empresa','Gestão Industrial','Desenvolvimento de Software') NOT NULL,
  semestre TINYINT NOT NULL,
  ano YEAR NOT NULL,
  inicio DATETIME NOT NULL,
  fim DATETIME NOT NULL,
  ativo TINYINT(1) DEFAULT 1,
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_votacao (curso, semestre, ano)
);

CREATE TABLE votos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  votante_id INT NOT NULL,
  candidato_id INT NOT NULL,
  votacao_id INT NOT NULL,
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (votante_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (candidato_id) REFERENCES representantes(id) ON DELETE CASCADE,
  FOREIGN KEY (votacao_id) REFERENCES votacoes(id) ON DELETE CASCADE,
  UNIQUE KEY unico_por_votante (votante_id, votacao_id)
);
