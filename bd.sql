CREATE DATABASE IF NOT EXISTS gti_bd;
USE gti_bd;

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nome_completo` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `cpf` varchar(20) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo_usuario` enum('cliente','empresa','administrador') DEFAULT 'cliente',
  `data_cadastro` datetime DEFAULT current_timestamp(),
  `termos_aceitos` tinyint(1) DEFAULT 0,
  `google_id` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `unique_cpf` (`cpf`),
  KEY `idx_usuario_email` (`email`),
  KEY `idx_usuario_cpf` (`cpf`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `plano` (
  `id_plano` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco_mensal` decimal(10,2) NOT NULL,
  `beneficios` text DEFAULT NULL,
  PRIMARY KEY (`id_plano`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `assinatura` (
  `id_assinatura` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `id_plano` int(11) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `status` enum('ativa','inativa','cancelada','suspensa') DEFAULT 'ativa',
  PRIMARY KEY (`id_assinatura`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_plano` (`id_plano`),
  CONSTRAINT `assinatura_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE,
  CONSTRAINT `assinatura_ibfk_2` FOREIGN KEY (`id_plano`) REFERENCES `plano` (`id_plano`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `plano_tipo_cacamba` (
  `id_cacamba` int(11) NOT NULL AUTO_INCREMENT,
  `id_plano` int(11) NOT NULL,
  `tipo_residuo` varchar(100) NOT NULL,
  `tamanho` varchar(50) NOT NULL,
  `descricao` text DEFAULT NULL,
  PRIMARY KEY (`id_cacamba`),
  KEY `idx_plano` (`id_plano`),
  KEY `idx_tipo_residuo` (`tipo_residuo`),
  CONSTRAINT `plano_tipo_cacamba_ibfk_1` FOREIGN KEY (`id_plano`) REFERENCES `plano` (`id_plano`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `endereco` (
  `id_endereco` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `cep` varchar(10) NOT NULL,
  `logradouro` varchar(100) NOT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `complemento` varchar(45) DEFAULT NULL,
  `bairro` varchar(50) NOT NULL,
  `cidade` varchar(45) NOT NULL,
  `estado` varchar(2) NOT NULL,
  PRIMARY KEY (`id_endereco`),
  KEY `idx_usuario` (`id_usuario`),
  CONSTRAINT `endereco_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pagamentos` (
  `id_pagamento` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `plano` varchar(20) NOT NULL COMMENT 'básico ou premium',
  `metodo_pagamento` varchar(20) NOT NULL COMMENT 'credit, pix ou boleto',
  `numero_cartao` varchar(19) DEFAULT NULL COMMENT 'Últimos 4 dígitos apenas (por segurança)',
  `nome_cartao` varchar(100) DEFAULT NULL,
  `validade_cartao` varchar(5) DEFAULT NULL,
  `parcelamento` int(11) DEFAULT NULL COMMENT 'Número de parcelas',
  `nome_completo` varchar(255) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `data_nascimento` date DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `telefone` varchar(15) NOT NULL,
  `status_pagamento` enum('pendente','aprovado','cancelado','reembolsado') DEFAULT 'pendente',
  `valor` decimal(10,2) NOT NULL,
  `termos_aceitos` tinyint(1) DEFAULT 0,
  `data_pagamento` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_pagamento`),
  KEY `idx_usuario` (`id_usuario`),
  KEY `idx_plano` (`plano`),
  KEY `idx_status` (`status_pagamento`),
  KEY `idx_data_pagamento` (`data_pagamento`),
  CONSTRAINT `pagamentos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `recuperacao_senha` (
  `id_recuperacao_senha` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `data_solicitacao` datetime DEFAULT current_timestamp(),
  `data_expiracao` datetime NOT NULL,
  `utilizado` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id_recuperacao_senha`),
  UNIQUE KEY `token` (`token`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `recuperacao_senha_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `chatbot` (
  `id_chatbot` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `negocio` varchar(50) DEFAULT NULL,
  `data_integracao` datetime DEFAULT current_timestamp(),
  `respondido` tinyint(1) DEFAULT 0,
  `id_recuperacao_senha` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_chatbot`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_recuperacao_senha` (`id_recuperacao_senha`),
  CONSTRAINT `chatbot_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE,
  CONSTRAINT `chatbot_ibfk_2` FOREIGN KEY (`id_recuperacao_senha`) REFERENCES `recuperacao_senha` (`id_recuperacao_senha`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `solicitacao_empresarial` (
  `id_solicitacao_empresarial` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `data_solicitacao` datetime DEFAULT current_timestamp(),
  `descricao_necessidades` text DEFAULT NULL,
  `quantidade_cacambas` int(11) NOT NULL,
  `tipo_residuos` varchar(100) NOT NULL,
  `status` enum('pendente','aprovada','rejeitada','em_andamento','concluida') DEFAULT 'pendente',
  PRIMARY KEY (`id_solicitacao_empresarial`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `solicitacao_empresarial_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `plano` (`id_plano`, `nome`, `descricao`, `preco_mensal`, `beneficios`) VALUES
(1, 'Plano Básico', 'Plano básico para residências', 99.90, 'Coleta mensal, suporte básico'),
(2, 'Plano Empresarial', 'Plano completo para empresas', 299.90, 'Coleta semanal, suporte prioritário, relatórios'),
(3, 'Plano Premium', 'Plano premium com todos os benefícios', 199.90, 'Coleta sob demanda, suporte 24/7, relatórios detalhados');

INSERT INTO `plano_tipo_cacamba` (`id_cacamba`, `id_plano`, `tipo_residuo`, `tamanho`, `descricao`) VALUES
(1, 1, 'Resíduos Metálicos', '3m³', 'Caçamba pequena ideal para resíduos de metal domésticos'),
(2, 1, 'Resíduos de Papel', '3m³', 'Caçamba pequena ideal para resíduos de papel domésticos'),
(3, 1, 'Resíduos de Plástico', '3m³', 'Caçamba pequena ideal para resíduos de plástico domésticos'),
(4, 1, 'Resíduos de Vidro', '3m³', 'Caçamba pequena ideal para resíduos de vidro domésticos'),
(5, 1, 'Resíduos Orgânicos', '3m³', 'Caçamba pequena ideal para resíduos de orgânicos domésticos'),
(6, 1, 'Resíduos de Madeira', '3m³', 'Caçamba pequena ideal para resíduos de madeira domésticos'),
(7, 2, 'Resíduos Metálicos', '5m³', 'Caçamba média para resíduos de metal de empresas'),
(8, 2, 'Resíduos de Papel', '5m³', 'Caçamba média para resíduos de papel de empresas'),
(9, 2, 'Resíduos de Plástico', '5m³', 'Caçamba média para resíduos de plástico de empresas'),
(10, 2, 'Resíduos de Vidro', '5m³', 'Caçamba média para resíduos de vidro de empresas'),
(11, 2, 'Resíduos Orgânicos', '5m³', 'Caçamba média para resíduos orgânicos de empresas'),
(12, 2, 'Resíduos de Madeira', '5m³', 'Caçamba média para resíduos de madeira de empresas'),
(13, 2, 'Resíduos Radioativos', '5m³', 'Caçamba média para resíduos radioativos de empresas'),
(14, 2, 'Resíduos Contaminados', '5m³', 'Caçamba média para resíduos contaminados de empresas'),
(15, 3, 'Resíduos Metálicos', '7m³', 'Caçamba grande para resíduos de metal'),
(16, 3, 'Resíduos de Papel', '7m³', 'Caçamba grande para residuos de papel'),
(17, 3, 'Resíduos de Plástico', '7m³', 'Caçamba grande para residuos de plástico'),
(18, 3, 'Resíduos de Vidro', '7m³', 'Caçamba grande para residuos de vidro'),
(19, 3, 'Resíduos Orgânicos', '7m³', 'Caçamba grande para residuos Orgânicos'),
(20, 3, 'Resíduos de Madeira', '7m³', 'Caçamba grande para residuos de madeira'),
(21, 3, 'Resíduos de Radioativos', '7m³', 'Caçamba grande para residuos radioativos'),
(22, 3, 'Resíduos Contaminados', '7m³', 'Caçamba grande para residuos Contaminados');