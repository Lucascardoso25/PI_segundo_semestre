-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 14/11/2025 às 02:09
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `webvote`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `representantes`
--

CREATE TABLE `representantes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `nome` varchar(150) NOT NULL,
  `curso` enum('Gestão Empresa','Gestão Industrial','Desenvolvimento de Software') NOT NULL,
  `semestre` tinyint(4) NOT NULL,
  `ra` varchar(30) NOT NULL,
  `ano` year(4) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `criado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `representantes`
--

INSERT INTO `representantes` (`id`, `usuario_id`, `nome`, `curso`, `semestre`, `ra`, `ano`, `id_usuario`, `criado_em`) VALUES
(2, 4, 'Carlinho dandan', 'Gestão Empresa', 4, '2781392513025', '2025', 4, '2025-11-11 21:47:38'),
(3, 4, 'Carlinho dandan', 'Gestão Industrial', 4, '2781392513025', '2025', 4, '2025-11-11 21:50:10'),
(5, 2, 'Lucas Aires Cardoso', 'Desenvolvimento de Software', 2, '2781392513021', '2025', 2, '2025-11-13 20:22:36'),
(9, 5, 'Carlos Daniel Dos Santos Gomes', 'Desenvolvimento de Software', 2, '2781392513045', '2025', 5, '2025-11-13 21:44:56');

--
-- Acionadores `representantes`
--
DELIMITER $$
CREATE TRIGGER `copia_usuario_id` BEFORE INSERT ON `representantes` FOR EACH ROW BEGIN 
    SET NEW.usuario_id = NEW.id_usuario;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `ra` varchar(30) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` enum('aluno','adm') NOT NULL DEFAULT 'aluno',
  `curso` enum('Gestão Empresa','Gestão Industrial','Desenvolvimento de Software') DEFAULT NULL,
  `semestre` tinyint(4) DEFAULT NULL,
  `ano` year(4) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `criado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `ra`, `email`, `senha`, `tipo`, `curso`, `semestre`, `ano`, `foto`, `criado_em`) VALUES
(1, 'Administrador', '000000', 'admin@fatec.sp.gov.br', '$2y$10$nlgc5eBTAerIKJyWps5CiOoFVlCqGowqkbzmw.gcXGR330.DtgEb6', 'adm', NULL, NULL, NULL, 'img/000000.png', '2025-11-06 23:38:00'),
(2, 'Lucas Aires Cardoso', '2781392513021', 'lucas.aires.cardoso@gmail.com', '$2y$10$iS4xbj718PBurHoRwld5yuntRp90GRqaeLIng/w20qUXPX8Vkf8jW', 'aluno', 'Desenvolvimento de Software', 2, '2025', 'uploads/foto_2.png', '2025-11-06 23:44:56'),
(3, 'Carlos Daniel', '2781392513022', 'carlito@gmail.com', '$2y$10$O.GMFZ/mCH1Mp44OLB.sVODNgSVKMRO40c5cAMB7X2LS7jro5qPOG', 'aluno', 'Desenvolvimento de Software', 2, '2025', 'uploads/2781392513022.png', '2025-11-11 19:51:23'),
(4, 'Carlito', '2781392513025', 'carlinhodandan@gmail.com', '$2y$10$TRYA5OTgv5Y4FpSW2WX.SOzHH96DfmYQ/91IKOjYRTKSr44sQ65D2', 'aluno', 'Gestão Industrial', 5, '2025', 'uploads/2781392513025.png', '2025-11-11 21:36:57'),
(5, 'Carlos Daniel dos Santos Gomes', '2781392513045', 'danndann@gmail.com', '$2y$10$0KbpCs/kjas2p/b3raIEQuC4XPEPzB8/o6niWKxh7/6aGlI4FV5ga', 'aluno', 'Desenvolvimento de Software', 2, '2025', 'uploads/2781392513045.png', '2025-11-13 21:14:14');

-- --------------------------------------------------------

--
-- Estrutura para tabela `votacoes`
--

CREATE TABLE `votacoes` (
  `id` int(11) NOT NULL,
  `curso` enum('Gestão Empresa','Gestão Industrial','Desenvolvimento de Software') NOT NULL,
  `semestre` tinyint(4) NOT NULL,
  `ano` year(4) NOT NULL,
  `inicio` datetime NOT NULL,
  `fim` datetime NOT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `criado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `votacoes`
--

INSERT INTO `votacoes` (`id`, `curso`, `semestre`, `ano`, `inicio`, `fim`, `ativo`, `criado_em`) VALUES
(1, 'Desenvolvimento de Software', 2, '2025', '2025-11-08 16:24:00', '2025-11-08 16:25:29', 0, '2025-11-08 16:25:03'),
(6, 'Desenvolvimento de Software', 2, '2025', '2025-11-08 16:39:00', '2025-11-11 21:30:14', 0, '2025-11-08 16:39:34'),
(7, 'Gestão Empresa', 4, '2025', '2025-11-11 21:26:00', '2025-11-26 21:27:00', 0, '2025-11-11 21:29:24'),
(8, 'Gestão Empresa', 4, '2025', '2025-11-11 21:48:00', '2025-11-13 19:44:01', 0, '2025-11-11 21:48:35'),
(9, 'Gestão Industrial', 4, '2025', '2025-11-11 21:51:00', '2025-11-13 19:43:57', 0, '2025-11-11 21:51:11'),
(10, 'Desenvolvimento de Software', 2, '2025', '2025-11-11 21:53:00', '2025-11-13 19:43:05', 0, '2025-11-11 21:53:17'),
(11, 'Desenvolvimento de Software', 2, '2025', '2025-11-13 19:45:00', '2025-11-13 19:46:54', 0, '2025-11-13 19:45:19'),
(12, 'Desenvolvimento de Software', 2, '2025', '2025-11-13 19:47:00', '2025-11-14 19:47:00', 1, '2025-11-13 19:47:46');

-- --------------------------------------------------------

--
-- Estrutura para tabela `votos`
--

CREATE TABLE `votos` (
  `id` int(11) NOT NULL,
  `votante_id` int(11) NOT NULL,
  `candidato_id` int(11) NOT NULL,
  `votacao_id` int(11) NOT NULL,
  `criado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `representantes`
--
ALTER TABLE `representantes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ra` (`ra`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `votacoes`
--
ALTER TABLE `votacoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `votos`
--
ALTER TABLE `votos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unico_por_votante` (`votante_id`,`votacao_id`),
  ADD KEY `candidato_id` (`candidato_id`),
  ADD KEY `votacao_id` (`votacao_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `representantes`
--
ALTER TABLE `representantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `votacoes`
--
ALTER TABLE `votacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `votos`
--
ALTER TABLE `votos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `representantes`
--
ALTER TABLE `representantes`
  ADD CONSTRAINT `fk_representante_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `representantes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `votos`
--
ALTER TABLE `votos`
  ADD CONSTRAINT `votos_ibfk_1` FOREIGN KEY (`votante_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votos_ibfk_2` FOREIGN KEY (`candidato_id`) REFERENCES `representantes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votos_ibfk_3` FOREIGN KEY (`votacao_id`) REFERENCES `votacoes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
