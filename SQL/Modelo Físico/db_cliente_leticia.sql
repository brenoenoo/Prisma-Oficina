CREATE DATABASE IF NOT EXISTS db_cliente_leticia;
use db_cliente_leticia;


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
-- Dados da tabela `agendamento`
--

INSERT INTO `agendamento`
(`id_agendamento`, `id_cliente`, `id_usuario`, `id_horario_funcionamento`, `data`, `hora`, `status`, `observacao`) VALUES
(1, 1, 2, 1, '2026-10-08', '08:00:00', 'agendado', 'Troca de óleo e filtro do motor'),
(2, 2, 3, 2, '2026-10-09', '09:00:00', 'agendado', 'Revisão do sistema de freios'),
(3, 3, 4, 3, '2026-10-10', '10:00:00', 'concluido', 'Alinhamento e balanceamento'),
(4, 4, 2, 4, '2026-10-11', '13:00:00', 'cancelado', 'Diagnóstico do sistema elétrico'),
(5, 5, 5, 5, '2026-10-12', '15:00:00', 'agendado', 'Troca de pastilhas de freio');

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
-- Dados da tabela `atendimento`
--

INSERT INTO `atendimento`
(`id_atendimento`, `id_agendamento`, `id_cliente`, `id_usuario`, `data_hora`, `descricao`, `status`) VALUES
(1, 1, 1, 2, '2026-10-08 08:00:00', 'Troca de óleo do motor e substituição do filtro de óleo.', 'aberto'),
(2, 2, 2, 3, '2026-10-09 09:00:00', 'Inspeção das pastilhas, discos e sistema de frenagem.', 'aberto'),
(3, 3, 3, 4, '2026-10-10 10:00:00', 'Alinhamento e balanceamento das quatro rodas realizados.', 'concluido'),
(4, 4, 4, 2, '2026-10-11 13:00:00', 'Diagnóstico elétrico cancelado pelo cliente.', 'concluido'),
(5, 5, 5, 5, '2026-10-12 15:00:00', 'Substituição das pastilhas de freio dianteiras.', 'aberto');

-- --------------------------------------------------------
--
-- Estrutura para tabela `atendimento_relatorio`
--

CREATE TABLE `atendimento_relatorio` (
  `id_atendimento` int(11) NOT NULL,
  `id_relatorio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dados da tabela `atendimento_relatorio`
--

INSERT INTO `atendimento_relatorio`
(`id_atendimento`, `id_relatorio`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5);

-- --------------------------------------------------------
--
-- Estrutura para tabela `categoria`
--

CREATE TABLE `categoria` (
  `id_categoria` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dados da tabela `categoria`
--

INSERT INTO `categoria` (`id_categoria`, `nome`) VALUES
(1, 'Motor'),
(2, 'Freios'),
(3, 'Suspensão'),
(4, 'Elétrica'),
(5, 'Lubrificantes');

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
-- Dados da tabela `cliente`
--

INSERT INTO `cliente`
(`id_cliente`, `telefone`, `nome`, `email`) VALUES
(1, '(11) 99821-4501', 'Carlos Henrique Souza', 'carlos.souza@email.com'),
(2, '(11) 99735-2842', 'Marcos Antônio Oliveira', 'marcos.oliveira@email.com'),
(3, '(11) 99642-7315', 'Rafael Mendes Costa', 'rafael.costa@email.com'),
(4, '(11) 99518-6204', 'Fernanda Alves Santos', 'fernanda.santos@email.com'),
(5, '(11) 99427-8136', 'Juliana Martins Pereira', 'juliana.pereira@email.com');

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
-- Dados da tabela `configuracoes_loja`
--

INSERT INTO `configuracoes_loja`
(`id_config_loja`, `nome`, `cnpj`, `telefone`, `email_comercial`, `endereco`, `cidade`, `estado`, `cep`) VALUES
(1, 'Auto Center Letícia', '12.345.678/0001-01', '(11) 98765-4321', 'contato@autocenterleticia.com', 'Rua dos Mecânicos, 100', 'São Paulo', 'SP', '01001-000'),
(2, 'Auto Center Letícia - Unidade 2', '12.345.678/0002-82', '(11) 98765-4322', 'unidade2@autocenterleticia.com', 'Avenida Brasil, 250', 'São Paulo', 'SP', '02002-000'),
(3, 'Auto Center Letícia - Unidade 3', '12.345.678/0003-63', '(21) 98765-4323', 'unidade3@autocenterleticia.com', 'Rua das Oficinas, 300', 'Rio de Janeiro', 'RJ', '22010-000'),
(4, 'Auto Center Letícia - Unidade 4', '12.345.678/0004-44', '(31) 98765-4324', 'unidade4@autocenterleticia.com', 'Rua Central, 400', 'Belo Horizonte', 'MG', '30110-000'),
(5, 'Auto Center Letícia - Unidade 5', '12.345.678/0005-25', '(41) 98765-4325', 'unidade5@autocenterleticia.com', 'Avenida Paraná, 500', 'Curitiba', 'PR', '80010-000');

-- --------------------------------------------------------
--
-- Estrutura para tabela `financeiro`
--

CREATE TABLE `financeiro` (
  `id_financeiro` int(11) NOT NULL,
  `tipo` enum('receita','despesa') NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `categoria` varchar(100) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data_movimentacao` date NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dados da tabela `financeiro`
--

INSERT INTO `financeiro`
(`id_financeiro`, `tipo`, `descricao`, `categoria`, `valor`, `data_movimentacao`, `id_usuario`, `criado_em`) VALUES
(1, 'receita', 'Troca de óleo e filtro', 'Serviços', 180.00, '2026-10-01', 2, '2026-10-07 17:49:13'),
(2, 'receita', 'Revisão do sistema de freios', 'Serviços', 350.00, '2026-10-02', 3, '2026-10-07 17:49:13'),
(3, 'despesa', 'Compra de peças automotivas', 'Peças', 1250.00, '2026-10-03', 2, '2026-10-07 17:49:13'),
(4, 'receita', 'Alinhamento e balanceamento', 'Serviços', 160.00, '2026-10-04', 4, '2026-10-07 17:49:13'),
(5, 'despesa', 'Conta de energia elétrica da oficina', 'Contas', 580.00, '2026-10-05', 1, '2026-10-07 17:49:13');

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
-- Dados da tabela `horario_funcionamento`
--

INSERT INTO `horario_funcionamento`
(`id_horario`, `id_configuracoes_loja`, `dia_semana`, `hora_abertura`, `hora_fechamento`, `ativo`) VALUES
(1, 1, 'Segunda-feira', '08:00:00', '18:00:00', 1),
(2, 1, 'Terça-feira', '08:00:00', '18:00:00', 1),
(3, 1, 'Quarta-feira', '08:00:00', '18:00:00', 1),
(4, 1, 'Quinta-feira', '08:00:00', '18:00:00', 1),
(5, 1, 'Sexta-feira', '08:00:00', '17:00:00', 1);

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
-- Dados da tabela `movimentacao`
--

INSERT INTO `movimentacao`
(`id_movimentacao`, `id_produto`, `id_usuario`, `id_cliente`, `tipo`, `quantidade`, `estoque_anterior`, `estoque_atual`, `motivo`, `realizado_em`) VALUES
(1, 1, 2, NULL, 'entrada', 20, 30, 50, 'Reposição de filtros de óleo', '2026-10-01 09:00:00'),
(2, 2, 3, 1, 'saida', 2, 25, 23, 'Pastilhas utilizadas no veículo do cliente', '2026-10-02 10:30:00'),
(3, 3, 4, 2, 'saida', 4, 18, 14, 'Amortecedores utilizados em manutenção', '2026-10-03 11:00:00'),
(4, 4, 2, NULL, 'entrada', 10, 12, 22, 'Compra de lâmpadas automotivas', '2026-10-04 09:30:00'),
(5, 5, 5, 3, 'saida', 3, 40, 37, 'Óleo utilizado na troca de óleo do veículo', '2026-10-05 15:30:00');

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
-- Dados da tabela `produtos`
--

INSERT INTO `produtos`
(`id_produto`, `id_categoria`, `nome`, `sku`, `preco`, `estoque`, `descricao`, `capa`, `data_cadastro`, `estoque_minimo`) VALUES
(1, 1, 'Filtro de Óleo', 'MOT001', 35.90, 50, 'Filtro de óleo para motores de veículos leves.', 'filtro_oleo.jpg', '2026-10-01', 10),
(2, 2, 'Pastilha de Freio Dianteira', 'FRE001', 189.90, 23, 'Jogo de pastilhas de freio dianteiras.', 'pastilha_freio.jpg', '2026-10-01', 5),
(3, 3, 'Amortecedor Dianteiro', 'SUS001', 420.00, 14, 'Amortecedor dianteiro para veículos de passeio.', 'amortecedor.jpg', '2026-10-02', 4),
(4, 4, 'Lâmpada Automotiva H7', 'ELE001', 45.90, 22, 'Lâmpada automotiva H7 para faróis.', 'lampada_h7.jpg', '2026-10-03', 5),
(5, 5, 'Óleo Lubrificante 5W30', 'LUB001', 42.90, 37, 'Óleo sintético 5W30 para motores automotivos.', 'oleo_5w30.jpg', '2026-10-04', 10);

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
-- Dados da tabela `registro_ponto`
--

INSERT INTO `registro_ponto`
(`id_registro`, `id_usuario`, `data`, `entrada`, `saida`) VALUES
(1, 1, '2026-10-01', '08:00:00', '17:00:00'),
(2, 2, '2026-10-01', '08:10:00', '17:10:00'),
(3, 3, '2026-10-01', '08:00:00', '17:00:00'),
(4, 4, '2026-10-01', '08:30:00', '17:30:00'),
(5, 5, '2026-10-01', '08:30:00', '17:30:00');

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
-- Dados da tabela `relatorio`
--

INSERT INTO `relatorio`
(`id_relatorio`, `periodo`, `data_geracao`, `tipo`) VALUES
(1, '01/10/2026 a 05/10/2026', '2026-10-05 18:00:00', 'Financeiro'),
(2, '01/10/2026 a 05/10/2026', '2026-10-05 18:10:00', 'Estoque de Peças'),
(3, '01/10/2026 a 05/10/2026', '2026-10-05 18:20:00', 'Serviços Realizados'),
(4, '01/10/2026 a 05/10/2026', '2026-10-05 18:30:00', 'Agendamentos'),
(5, '01/10/2026 a 05/10/2026', '2026-10-05 18:40:00', 'Funcionários');

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
-- Dados da tabela `usuario`
--

INSERT INTO `usuario`
(`id_usuario`, `ultimo_acesso`, `senha`, `tipo`, `email`, `status`, `nome`) VALUES
(1, NULL, '123', 'gerente', 'gerente@autocenter.com', 'ativo', 'Roberto Almeida'),
(2, '2026-10-07 08:30:00', 'senha123', 'gerente', 'leticia@autocenter.com', 'ativo', 'Letícia Almeida'),
(3, '2026-10-07 08:45:00', 'senha456', 'funcionario', 'marcos@autocenter.com', 'ativo', 'Marcos Ferreira'),
(4, '2026-10-06 17:30:00', 'senha789', 'funcionario', 'andre@autocenter.com', 'ativo', 'André Martins'),
(5, '2026-10-05 16:20:00', 'senha321', 'funcionario', 'paulo@autocenter.com', 'ativo', 'Paulo Rocha'),
(6, '2026-10-07 09:00:00', 'senha654', 'gerente', 'carolina@autocenter.com', 'ativo', 'Carolina Mendes');

--
-- Índices para tabelas despejadas
--

ALTER TABLE `agendamento`
  ADD PRIMARY KEY (`id_agendamento`),
  ADD KEY `fk_agendamento_cliente` (`id_cliente`),
  ADD KEY `fk_agendamento_usuario` (`id_usuario`),
  ADD KEY `fk_agendamento_horario` (`id_horario_funcionamento`);

ALTER TABLE `atendimento`
  ADD PRIMARY KEY (`id_atendimento`),
  ADD UNIQUE KEY `id_agendamento` (`id_agendamento`),
  ADD KEY `fk_atendimento_cliente` (`id_cliente`),
  ADD KEY `fk_atendimento_usuario` (`id_usuario`);

ALTER TABLE `atendimento_relatorio`
  ADD PRIMARY KEY (`id_atendimento`,`id_relatorio`),
  ADD KEY `fk_ar_relatorio` (`id_relatorio`);

ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id_categoria`);

ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_cliente`);

ALTER TABLE `configuracoes_loja`
  ADD PRIMARY KEY (`id_config_loja`);

ALTER TABLE `financeiro`
  ADD PRIMARY KEY (`id_financeiro`),
  ADD KEY `fk_financeiro_usuario` (`id_usuario`);

ALTER TABLE `horario_funcionamento`
  ADD PRIMARY KEY (`id_horario`),
  ADD KEY `fk_horario_configuracao` (`id_configuracoes_loja`);

ALTER TABLE `movimentacao`
  ADD PRIMARY KEY (`id_movimentacao`),
  ADD KEY `fk_movimentacao_produto` (`id_produto`),
  ADD KEY `fk_movimentacao_usuario` (`id_usuario`),
  ADD KEY `fk_movimentacao_cliente` (`id_cliente`);

ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id_produto`),
  ADD KEY `fk_produto_categoria` (`id_categoria`);

ALTER TABLE `registro_ponto`
  ADD PRIMARY KEY (`id_registro`),
  ADD KEY `fk_ponto_usuario` (`id_usuario`);

ALTER TABLE `relatorio`
  ADD PRIMARY KEY (`id_relatorio`);

ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT
--

ALTER TABLE `agendamento`
  MODIFY `id_agendamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `atendimento`
  MODIFY `id_atendimento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `categoria`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `cliente`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `configuracoes_loja`
  MODIFY `id_config_loja` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `financeiro`
  MODIFY `id_financeiro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `horario_funcionamento`
  MODIFY `id_horario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `movimentacao`
  MODIFY `id_movimentacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `produtos`
  MODIFY `id_produto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `registro_ponto`
  MODIFY `id_registro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `relatorio`
  MODIFY `id_relatorio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restrições para tabelas
--

ALTER TABLE `agendamento`
  ADD CONSTRAINT `fk_agendamento_cliente`
    FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`),
  ADD CONSTRAINT `fk_agendamento_horario`
    FOREIGN KEY (`id_horario_funcionamento`) REFERENCES `horario_funcionamento` (`id_horario`),
  ADD CONSTRAINT `fk_agendamento_usuario`
    FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

ALTER TABLE `atendimento`
  ADD CONSTRAINT `fk_atendimento_agendamento`
    FOREIGN KEY (`id_agendamento`) REFERENCES `agendamento` (`id_agendamento`),
  ADD CONSTRAINT `fk_atendimento_cliente`
    FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`),
  ADD CONSTRAINT `fk_atendimento_usuario`
    FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

ALTER TABLE `atendimento_relatorio`
  ADD CONSTRAINT `fk_ar_atendimento`
    FOREIGN KEY (`id_atendimento`) REFERENCES `atendimento` (`id_atendimento`),
  ADD CONSTRAINT `fk_ar_relatorio`
    FOREIGN KEY (`id_relatorio`) REFERENCES `relatorio` (`id_relatorio`);

ALTER TABLE `financeiro`
  ADD CONSTRAINT `fk_financeiro_usuario`
    FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`)
    ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `horario_funcionamento`
  ADD CONSTRAINT `fk_horario_configuracao`
    FOREIGN KEY (`id_configuracoes_loja`) REFERENCES `configuracoes_loja` (`id_config_loja`);

ALTER TABLE `movimentacao`
  ADD CONSTRAINT `fk_movimentacao_cliente`
    FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`),
  ADD CONSTRAINT `fk_movimentacao_produto`
    FOREIGN KEY (`id_produto`) REFERENCES `produtos` (`id_produto`),
  ADD CONSTRAINT `fk_movimentacao_usuario`
    FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

ALTER TABLE `produtos`
  ADD CONSTRAINT `fk_produto_categoria`
    FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`);

ALTER TABLE `registro_ponto`
  ADD CONSTRAINT `fk_ponto_usuario`
    FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);