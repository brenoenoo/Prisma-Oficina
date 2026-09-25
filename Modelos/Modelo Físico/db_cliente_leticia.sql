-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 25/09/2026 às 18:43
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
-- Banco de dados: `db_cliente_leticia`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamento`
--

CREATE TABLE `agendamento` (
  `id_agendamento` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_horario_funcionamento` int(11) NOT NULL,
  `data` date NOT NULL,
  `hora` time NOT NULL,
  `status` enum('agendado','cancelado','concluido') NOT NULL,
  `observacao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `agendamento`
--

INSERT INTO `agendamento` (`id_agendamento`, `id_cliente`, `id_usuario`, `id_horario_funcionamento`, `data`, `hora`, `status`, `observacao`) VALUES
(1, 1, 2, 1, '2026-09-25', '09:00:00', 'concluido', 'Barulho estranho na frenagem dianteira e revisão de óleo.'),
(2, 2, 2, 1, '2026-09-25', '14:00:00', 'agendado', 'Alinhamento, balanceamento e rodízio de pneus.'),
(3, 3, 3, 2, '2026-09-26', '08:30:00', 'agendado', 'Troca preventiva da correia dentada.');

-- --------------------------------------------------------

--
-- Estrutura para tabela `atendimento`
--

CREATE TABLE `atendimento` (
  `id_atendimento` int(11) NOT NULL,
  `id_agendamento` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `data_hora` datetime NOT NULL,
  `descricao` text DEFAULT NULL,
  `status` enum('aberto','concluido') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `atendimento`
--

INSERT INTO `atendimento` (`id_atendimento`, `id_agendamento`, `id_cliente`, `id_usuario`, `data_hora`, `descricao`, `status`) VALUES
(1, 1, 1, 2, '2026-09-25 09:05:00', 'Confirmada a necessidade de troca das pastilhas dianteiras e substituição de 4L de óleo.', 'concluido'),
(2, 2, 2, 2, '2026-09-25 14:05:00', 'Veículo posicionado na rampa. Iniciando diagnóstico da suspensão.', 'aberto');

-- --------------------------------------------------------

--
-- Estrutura para tabela `atendimento_relatorio`
--

CREATE TABLE `atendimento_relatorio` (
  `id_atendimento` int(11) NOT NULL,
  `id_relatorio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `atendimento_relatorio`
--

INSERT INTO `atendimento_relatorio` (`id_atendimento`, `id_relatorio`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `categoria`
--

CREATE TABLE `categoria` (
  `id_categoria` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categoria`
--

INSERT INTO `categoria` (`id_categoria`, `nome`) VALUES
(1, 'Serviços Mecânicos'),
(2, 'Alinhamento e Balanceamento'),
(3, 'Peças de Motor'),
(4, 'Lubrificantes e Fluidos');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int(11) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `telefone`, `nome`, `email`) VALUES
(1, '(11) 98888-1111', 'Carlos Augusto (Civic Cinza)', 'carlos.augusto@email.com'),
(2, '(11) 98888-2222', 'Mariana Souza (Onix Branco)', 'mariana.souza@email.com'),
(3, '(11) 98888-3333', 'Fernando Lima (Hilux Preta)', 'fernando.lima@email.com');

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracoes_loja`
--

CREATE TABLE `configuracoes_loja` (
  `id_config_loja` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cnpj` varchar(18) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email_comercial` varchar(150) DEFAULT NULL,
  `endereco` varchar(200) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `cep` varchar(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `configuracoes_loja`
--

INSERT INTO `configuracoes_loja` (`id_config_loja`, `nome`, `cnpj`, `telefone`, `email_comercial`, `endereco`, `cidade`, `estado`, `cep`) VALUES
(1, 'Leticia Auto Center & Mecânica', '12.345.678/0001-99', '(11) 4333-8888', 'contato@leticiaautocenter.com', 'Av. Taboão, 1500', 'São Bernardo do Campo', 'SP', '09655-000');

-- --------------------------------------------------------

--
-- Estrutura para tabela `horario_funcionamento`
--

CREATE TABLE `horario_funcionamento` (
  `id_horario` int(11) NOT NULL,
  `id_configuracoes_loja` int(11) NOT NULL,
  `dia_semana` varchar(20) NOT NULL,
  `hora_abertura` time NOT NULL,
  `hora_fechamento` time NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `horario_funcionamento`
--

INSERT INTO `horario_funcionamento` (`id_horario`, `id_configuracoes_loja`, `dia_semana`, `hora_abertura`, `hora_fechamento`, `ativo`) VALUES
(1, 1, 'Segunda a Sexta', '08:00:00', '18:00:00', 1),
(2, 1, 'Sábado', '08:00:00', '12:00:00', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `movimentacao`
--

CREATE TABLE `movimentacao` (
  `id_movimentacao` int(11) NOT NULL,
  `id_produto` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `tipo` enum('entrada','saida') NOT NULL,
  `quantidade` int(11) NOT NULL,
  `estoque_anterior` int(11) NOT NULL,
  `estoque_atual` int(11) NOT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `realizado_em` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `movimentacao`
--

INSERT INTO `movimentacao` (`id_movimentacao`, `id_produto`, `id_usuario`, `id_cliente`, `tipo`, `quantidade`, `estoque_anterior`, `estoque_atual`, `motivo`, `realizado_em`) VALUES
(1, 1, 2, 1, 'saida', 4, 44, 40, 'Utilizado na troca de óleo do Civic do Carlos', '2026-09-25 10:30:00'),
(2, 2, 2, 1, 'saida', 1, 9, 8, 'Instalada pastilha de freio no Civic do Carlos', '2026-09-25 10:45:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id_produto` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `estoque` int(11) NOT NULL DEFAULT 0,
  `descricao` text DEFAULT NULL,
  `capa` varchar(255) DEFAULT NULL,
  `data_cadastro` date NOT NULL,
  `estoque_minimo` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id_produto`, `id_categoria`, `nome`, `sku`, `preco`, `estoque`, `descricao`, `capa`, `data_cadastro`, `estoque_minimo`) VALUES
(1, 4, 'Óleo de Motor 5W30 Sintético 1L', 'OLE-5W30-01', 59.90, 40, 'Óleo lubrificante de alta performance.', 'oleo_5w30.jpg', '2026-01-10', 10),
(2, 3, 'Pastilha de Freio Dianteira', 'PAS-FRE-CIVIC', 145.00, 8, 'Jogo de pastilhas de freio para eixo dianteiro.', 'pastilha_freio.jpg', '2026-03-15', 4),
(3, 1, 'Mão de Obra: Troca de Correia Dentada', 'SERV-CORREIA', 350.00, 9999, 'Serviço técnico de substituição do kit de correia.', NULL, '2026-01-05', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `registro_ponto`
--

CREATE TABLE `registro_ponto` (
  `id_registro` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `data` date NOT NULL,
  `entrada` time NOT NULL,
  `saida` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `registro_ponto`
--

INSERT INTO `registro_ponto` (`id_registro`, `id_usuario`, `data`, `entrada`, `saida`) VALUES
(1, 2, '2026-09-24', '07:55:00', '18:02:00'),
(2, 2, '2026-09-25', '07:50:00', NULL),
(3, 3, '2026-09-25', '08:00:00', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `relatorio`
--

CREATE TABLE `relatorio` (
  `id_relatorio` int(11) NOT NULL,
  `periodo` varchar(50) NOT NULL,
  `data_geracao` datetime NOT NULL,
  `tipo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `relatorio`
--

INSERT INTO `relatorio` (`id_relatorio`, `periodo`, `data_geracao`, `tipo`) VALUES
(1, '25/09/2026', '2026-09-25 12:00:00', 'Fechamento Parcial de Ordens de Serviço');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `ultimo_acesso` datetime DEFAULT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` enum('gerente','funcionario') NOT NULL,
  `email` varchar(150) NOT NULL,
  `status` enum('ativo','inativo') NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `ultimo_acesso`, `senha`, `tipo`, `email`, `status`, `nome`) VALUES
(1, '2026-09-25 08:00:00', '$2y$10$xyz123...', 'gerente', 'leticia.gerente@leticiaautocenter.com', 'ativo', 'Letícia Oliveira'),
(2, '2026-09-25 08:15:00', '$2y$10$abc456...', 'funcionario', 'marcos.mecanico@email.com', 'ativo', 'Marcos Silva (Mecânico Chefe)'),
(3, '2026-09-25 08:30:00', '$2y$10$def789...', 'funcionario', 'ricardo.auxiliar@email.com', 'ativo', 'Ricardo Santos (Auxiliar)');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `agendamento`
--
ALTER TABLE `agendamento`
  ADD PRIMARY KEY (`id_agendamento`),
  ADD KEY `fk_agendamento_cliente` (`id_cliente`),
  ADD KEY `fk_agendamento_usuario` (`id_usuario`),
  ADD KEY `fk_agendamento_horario` (`id_horario_funcionamento`);

--
-- Índices de tabela `atendimento`
--
ALTER TABLE `atendimento`
  ADD PRIMARY KEY (`id_atendimento`),
  ADD UNIQUE KEY `id_agendamento` (`id_agendamento`),
  ADD KEY `fk_atendimento_cliente` (`id_cliente`),
  ADD KEY `fk_atendimento_usuario` (`id_usuario`);

--
-- Índices de tabela `atendimento_relatorio`
--
ALTER TABLE `atendimento_relatorio`
  ADD PRIMARY KEY (`id_atendimento`,`id_relatorio`),
  ADD KEY `fk_ar_relatorio` (`id_relatorio`);

--
-- Índices de tabela `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Índices de tabela `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Índices de tabela `configuracoes_loja`
--
ALTER TABLE `configuracoes_loja`
  ADD PRIMARY KEY (`id_config_loja`);

--
-- Índices de tabela `horario_funcionamento`
--
ALTER TABLE `horario_funcionamento`
  ADD PRIMARY KEY (`id_horario`),
  ADD KEY `fk_horario_configuracao` (`id_configuracoes_loja`);

--
-- Índices de tabela `movimentacao`
--
ALTER TABLE `movimentacao`
  ADD PRIMARY KEY (`id_movimentacao`),
  ADD KEY `fk_movimentacao_produto` (`id_produto`),
  ADD KEY `fk_movimentacao_usuario` (`id_usuario`),
  ADD KEY `fk_movimentacao_cliente` (`id_cliente`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id_produto`),
  ADD KEY `fk_produto_categoria` (`id_categoria`);

--
-- Índices de tabela `registro_ponto`
--
ALTER TABLE `registro_ponto`
  ADD PRIMARY KEY (`id_registro`),
  ADD KEY `fk_ponto_usuario` (`id_usuario`);

--
-- Índices de tabela `relatorio`
--
ALTER TABLE `relatorio`
  ADD PRIMARY KEY (`id_relatorio`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agendamento`
--
ALTER TABLE `agendamento`
  MODIFY `id_agendamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `atendimento`
--
ALTER TABLE `atendimento`
  MODIFY `id_atendimento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `configuracoes_loja`
--
ALTER TABLE `configuracoes_loja`
  MODIFY `id_config_loja` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `horario_funcionamento`
--
ALTER TABLE `horario_funcionamento`
  MODIFY `id_horario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `movimentacao`
--
ALTER TABLE `movimentacao`
  MODIFY `id_movimentacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id_produto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `registro_ponto`
--
ALTER TABLE `registro_ponto`
  MODIFY `id_registro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `relatorio`
--
ALTER TABLE `relatorio`
  MODIFY `id_relatorio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `agendamento`
--
ALTER TABLE `agendamento`
  ADD CONSTRAINT `fk_agendamento_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`),
  ADD CONSTRAINT `fk_agendamento_horario` FOREIGN KEY (`id_horario_funcionamento`) REFERENCES `horario_funcionamento` (`id_horario`),
  ADD CONSTRAINT `fk_agendamento_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Restrições para tabelas `atendimento`
--
ALTER TABLE `atendimento`
  ADD CONSTRAINT `fk_atendimento_agendamento` FOREIGN KEY (`id_agendamento`) REFERENCES `agendamento` (`id_agendamento`),
  ADD CONSTRAINT `fk_atendimento_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`),
  ADD CONSTRAINT `fk_atendimento_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Restrições para tabelas `atendimento_relatorio`
--
ALTER TABLE `atendimento_relatorio`
  ADD CONSTRAINT `fk_ar_atendimento` FOREIGN KEY (`id_atendimento`) REFERENCES `atendimento` (`id_atendimento`),
  ADD CONSTRAINT `fk_ar_relatorio` FOREIGN KEY (`id_relatorio`) REFERENCES `relatorio` (`id_relatorio`);

--
-- Restrições para tabelas `horario_funcionamento`
--
ALTER TABLE `horario_funcionamento`
  ADD CONSTRAINT `fk_horario_configuracao` FOREIGN KEY (`id_configuracoes_loja`) REFERENCES `configuracoes_loja` (`id_config_loja`);

--
-- Restrições para tabelas `movimentacao`
--
ALTER TABLE `movimentacao`
  ADD CONSTRAINT `fk_movimentacao_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`),
  ADD CONSTRAINT `fk_movimentacao_produto` FOREIGN KEY (`id_produto`) REFERENCES `produtos` (`id_produto`),
  ADD CONSTRAINT `fk_movimentacao_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `fk_produto_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`);

--
-- Restrições para tabelas `registro_ponto`
--
ALTER TABLE `registro_ponto`
  ADD CONSTRAINT `fk_ponto_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
