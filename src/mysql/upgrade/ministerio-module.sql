-- Módulo Ministério & Comunicação
-- Criado para ChurchCRM
-- Compatível com MariaDB/MySQL, InnoDB, utf8mb4

-- Tabela: ministerios
CREATE TABLE IF NOT EXISTS `ministerios` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) NOT NULL,
  `descricao` TEXT,
  `lider_id` INT(11) UNSIGNED NOT NULL,
  `coordenador_id` INT(11) UNSIGNED DEFAULT NULL,
  `ativo` TINYINT(1) DEFAULT 1,
  `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_lider` (`lider_id`),
  KEY `idx_coordenador` (`coordenador_id`),
  KEY `idx_ativo` (`ativo`),
  CONSTRAINT `fk_ministerio_lider` FOREIGN KEY (`lider_id`) REFERENCES `person_per` (`per_ID`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_ministerio_coordenador` FOREIGN KEY (`coordenador_id`) REFERENCES `person_per` (`per_ID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: ministerio_membros
CREATE TABLE IF NOT EXISTS `ministerio_membros` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ministerio_id` INT(11) UNSIGNED NOT NULL,
  `membro_id` INT(11) UNSIGNED NOT NULL,
  `funcao` VARCHAR(100) DEFAULT NULL,
  `data_entrada` DATE NOT NULL,
  `data_saida` DATE DEFAULT NULL,
  `ativo` TINYINT(1) DEFAULT 1,
  `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ministerio_membro` (`ministerio_id`, `membro_id`, `ativo`),
  KEY `idx_membro` (`membro_id`),
  KEY `idx_ministerio` (`ministerio_id`),
  KEY `idx_ativo` (`ativo`),
  CONSTRAINT `fk_membro_ministerio` FOREIGN KEY (`ministerio_id`) REFERENCES `ministerios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_membro_person` FOREIGN KEY (`membro_id`) REFERENCES `person_per` (`per_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: ministerio_reunioes
CREATE TABLE IF NOT EXISTS `ministerio_reunioes` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ministerio_id` INT(11) UNSIGNED NOT NULL,
  `titulo` VARCHAR(255) NOT NULL,
  `descricao` TEXT,
  `data_reuniao` DATETIME NOT NULL,
  `local` VARCHAR(255) DEFAULT NULL,
  `criado_por` INT(11) UNSIGNED NOT NULL,
  `ativo` TINYINT(1) DEFAULT 1,
  `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ministerio` (`ministerio_id`),
  KEY `idx_data_reuniao` (`data_reuniao`),
  KEY `idx_ativo` (`ativo`),
  KEY `idx_criado_por` (`criado_por`),
  CONSTRAINT `fk_reuniao_ministerio` FOREIGN KEY (`ministerio_id`) REFERENCES `ministerios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_reuniao_criador` FOREIGN KEY (`criado_por`) REFERENCES `person_per` (`per_ID`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: ministerio_reunioes_participantes
CREATE TABLE IF NOT EXISTS `ministerio_reunioes_participantes` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `reuniao_id` INT(11) UNSIGNED NOT NULL,
  `membro_id` INT(11) UNSIGNED NOT NULL,
  `status` ENUM('pendente', 'confirmado', 'cancelado', 'presente', 'ausente') DEFAULT 'pendente',
  `token_rsvp` VARCHAR(64) DEFAULT NULL,
  `data_confirmacao` DATETIME DEFAULT NULL,
  `observacoes` TEXT,
  `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_reuniao_membro` (`reuniao_id`, `membro_id`),
  UNIQUE KEY `uk_token_rsvp` (`token_rsvp`),
  KEY `idx_membro` (`membro_id`),
  KEY `idx_status` (`status`),
  KEY `idx_reuniao` (`reuniao_id`),
  CONSTRAINT `fk_participante_reuniao` FOREIGN KEY (`reuniao_id`) REFERENCES `ministerio_reunioes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_participante_membro` FOREIGN KEY (`membro_id`) REFERENCES `person_per` (`per_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: ministerio_mensagens
CREATE TABLE IF NOT EXISTS `ministerio_mensagens` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ministerio_id` INT(11) UNSIGNED NOT NULL,
  `reuniao_id` INT(11) UNSIGNED DEFAULT NULL,
  `tipo` ENUM('geral', 'reuniao', 'lembrete', 'aniversario') DEFAULT 'geral',
  `assunto` VARCHAR(255) NOT NULL,
  `conteudo` TEXT NOT NULL,
  `template` TEXT,
  `canal` ENUM('email', 'whatsapp', 'sms', 'interno') DEFAULT 'email',
  `status` ENUM('rascunho', 'agendado', 'enviando', 'enviado', 'falhou') DEFAULT 'rascunho',
  `data_agendamento` DATETIME DEFAULT NULL,
  `data_envio` DATETIME DEFAULT NULL,
  `criado_por` INT(11) UNSIGNED NOT NULL,
  `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ministerio` (`ministerio_id`),
  KEY `idx_reuniao` (`reuniao_id`),
  KEY `idx_status` (`status`),
  KEY `idx_data_agendamento` (`data_agendamento`),
  KEY `idx_criado_por` (`criado_por`),
  CONSTRAINT `fk_mensagem_ministerio` FOREIGN KEY (`ministerio_id`) REFERENCES `ministerios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mensagem_reuniao` FOREIGN KEY (`reuniao_id`) REFERENCES `ministerio_reunioes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_mensagem_criador` FOREIGN KEY (`criado_por`) REFERENCES `person_per` (`per_ID`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: ministerio_mensagens_envio
CREATE TABLE IF NOT EXISTS `ministerio_mensagens_envio` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `mensagem_id` INT(11) UNSIGNED NOT NULL,
  `destinatario_id` INT(11) UNSIGNED NOT NULL,
  `canal` ENUM('email', 'whatsapp', 'sms', 'interno') NOT NULL,
  `status` ENUM('pendente', 'enviando', 'enviado', 'falhou', 'cancelado') DEFAULT 'pendente',
  `tentativas` INT(3) DEFAULT 0,
  `erro` TEXT,
  `data_envio` DATETIME DEFAULT NULL,
  `data_tentativa` DATETIME DEFAULT NULL,
  `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_mensagem` (`mensagem_id`),
  KEY `idx_destinatario` (`destinatario_id`),
  KEY `idx_status` (`status`),
  KEY `idx_canal` (`canal`),
  CONSTRAINT `fk_envio_mensagem` FOREIGN KEY (`mensagem_id`) REFERENCES `ministerio_mensagens` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_envio_destinatario` FOREIGN KEY (`destinatario_id`) REFERENCES `person_per` (`per_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: ministerio_logs
CREATE TABLE IF NOT EXISTS `ministerio_logs` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` INT(11) UNSIGNED DEFAULT NULL,
  `acao` VARCHAR(100) NOT NULL,
  `tabela` VARCHAR(50) NOT NULL,
  `registro_id` INT(11) UNSIGNED DEFAULT NULL,
  `dados_antigos` JSON DEFAULT NULL,
  `dados_novos` JSON DEFAULT NULL,
  `ip_origem` VARCHAR(45) DEFAULT NULL,
  `user_agent` TEXT,
  `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_acao` (`acao`),
  KEY `idx_tabela` (`tabela`),
  KEY `idx_registro` (`registro_id`),
  KEY `idx_data` (`criado_em`),
  CONSTRAINT `fk_log_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `person_per` (`per_ID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índices adicionais para performance
CREATE INDEX idx_ministerio_reunioes_futuras ON ministerio_reunioes(data_reuniao, ativo);
CREATE INDEX idx_mensagens_agendadas ON ministerio_mensagens(status, data_agendamento);
CREATE INDEX idx_envios_pendentes ON ministerio_mensagens_envio(status, canal);

