-- ChurchCRM Database Setup Script (Local Environment)
-- Execute este script no MySQL/MariaDB para configurar o banco de dados

-- Conecte-se como root primeiro: mysql -u root -p
-- Depois execute: source database-setup.sql

-- 1. Criar banco de dados principal
CREATE DATABASE IF NOT EXISTS churchcrm 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- 2. Criar usuário do aplicativo
CREATE USER IF NOT EXISTS 'churchcrm'@'localhost' 
IDENTIFIED BY 'churchcrm123';

-- 3. Conceder privilégios ao usuário
GRANT ALL PRIVILEGES ON churchcrm.* TO 'churchcrm'@'localhost';

-- 4. Aplicar permissões
FLUSH PRIVILEGES;

-- 5. Selecionar o banco de dados para uso
USE churchcrm;

-- 6. Criar tabelas básicas (se não existirem)
-- Nota: O ChurchCRM criará as tabelas automaticamente durante a instalação
-- Mas aqui estão algumas tabelas básicas para verificação

-- Tabela de usuários (básica)
CREATE TABLE IF NOT EXISTS user_usr (
    usr_ID BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usr_per_ID BIGINT UNSIGNED NOT NULL,
    usr_UserName VARCHAR(50) NOT NULL UNIQUE,
    usr_Password VARCHAR(255) NOT NULL,
    usr_NeedPasswordChange BOOLEAN DEFAULT FALSE,
    usr_LastLogin DATETIME,
    usr_FailedLogins INT DEFAULT 0,
    usr_LoginCount INT DEFAULT 0,
    usr_AddedBy BIGINT UNSIGNED,
    usr_DateCreated DATETIME DEFAULT CURRENT_TIMESTAMP,
    usr_EditedBy BIGINT UNSIGNED,
    usr_DateLastEdited DATETIME,
    usr_Inventory BOOLEAN DEFAULT FALSE,
    usr_Admin BOOLEAN DEFAULT FALSE,
    usr_SearchLimit INT DEFAULT 10,
    usr_Style VARCHAR(50) DEFAULT 'style.css',
    usr_SearchFamilyColumn BOOLEAN DEFAULT TRUE,
    usr_WorkPhoneUnavailable BOOLEAN DEFAULT FALSE,
    usr_HomePhoneUnavailable BOOLEAN DEFAULT FALSE,
    usr_CellPhoneUnavailable BOOLEAN DEFAULT FALSE,
    usr_WorkEmailUnavailable BOOLEAN DEFAULT FALSE,
    usr_HomeEmailUnavailable BOOLEAN DEFAULT FALSE,
    usr_NoDelete BOOLEAN DEFAULT FALSE,
    usr_EditSelf BOOLEAN DEFAULT FALSE,
    usr_DefaultFY INT DEFAULT 0,
    usr_CurrentLanguage VARCHAR(10) DEFAULT 'en_US',
    usr_CalStart DATE DEFAULT NULL,
    usr_CalEnd DATE DEFAULT NULL,
    usr_CalNoSchool BOOLEAN DEFAULT FALSE,
    usr_CalGrouplet BOOLEAN DEFAULT FALSE,
    usr_CalHideChurch BOOLEAN DEFAULT FALSE,
    usr_CalHidePrivate BOOLEAN DEFAULT FALSE,
    usr_CalHideWeekend BOOLEAN DEFAULT FALSE,
    usr_CalDisplayBirthday BOOLEAN DEFAULT TRUE,
    usr_CalDisplayAnniversary BOOLEAN DEFAULT TRUE,
    usr_CalDisplayHoliday BOOLEAN DEFAULT TRUE,
    usr_CalStartDay INT DEFAULT 0,
    usr_SearchBarDefault BOOLEAN DEFAULT FALSE,
    usr_ShowPrefilledFamilyCustomFields BOOLEAN DEFAULT FALSE,
    usr_ShowPrefilledPersonCustomFields BOOLEAN DEFAULT FALSE,
    usr_EnableAPIPassword BOOLEAN DEFAULT FALSE,
    usr_APIKey VARCHAR(255),
    usr_TwoFAEnabled BOOLEAN DEFAULT FALSE,
    usr_TwoFASecret VARCHAR(255),
    usr_TwoFABackupCodes TEXT,
    usr_PasswordResetHash VARCHAR(255),
    usr_PasswordResetTimestamp DATETIME,
    usr_PasswordResetAttempts INT DEFAULT 0,
    INDEX idx_usr_per_ID (usr_per_ID),
    INDEX idx_usr_UserName (usr_UserName)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de pessoas (básica)
CREATE TABLE IF NOT EXISTS person_per (
    per_ID BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    per_Title VARCHAR(50),
    per_FirstName VARCHAR(50) NOT NULL,
    per_MiddleName VARCHAR(50),
    per_LastName VARCHAR(50) NOT NULL,
    per_Suffix VARCHAR(50),
    per_Address1 VARCHAR(255),
    per_Address2 VARCHAR(255),
    per_City VARCHAR(50),
    per_State VARCHAR(50),
    per_Zip VARCHAR(10),
    per_Country VARCHAR(50),
    per_HomePhone VARCHAR(30),
    per_WorkPhone VARCHAR(30),
    per_CellPhone VARCHAR(30),
    per_Email VARCHAR(100),
    per_WorkEmail VARCHAR(100),
    per_BirthDate DATE,
    per_Gender ENUM('Male','Female','Unknown') DEFAULT 'Unknown',
    per_Fmr_ID BIGINT UNSIGNED,
    per_cls_ID BIGINT UNSIGNED,
    per_fam_ID BIGINT UNSIGNED,
    per_fmr_ID BIGINT UNSIGNED,
    per_MembershipDate DATE,
    per_FriendDate DATE,
    per_EnteredBy BIGINT UNSIGNED,
    per_EnteredDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    per_EditedBy BIGINT UNSIGNED,
    per_EditedDate DATETIME,
    per_Thumbnail VARCHAR(255),
    per_Facebook VARCHAR(255),
    per_Twitter VARCHAR(255),
    per_LinkedIn VARCHAR(255),
    per_Instagram VARCHAR(255),
    per_CustomFields TEXT,
    per_SocialMedia TEXT,
    per_VerificationToken VARCHAR(255),
    per_VerificationExpires DATETIME,
    per_Privacy ENUM('public','members','staff','admin') DEFAULT 'members',
    per_SearchText TEXT,
    INDEX idx_per_fam_ID (per_fam_ID),
    INDEX idx_per_LastName (per_LastName),
    INDEX idx_per_FirstName (per_FirstName),
    FULLTEXT idx_per_SearchText (per_SearchText)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de famílias (básica)
CREATE TABLE IF NOT EXISTS family_fam (
    fam_ID BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fam_Name VARCHAR(100) NOT NULL,
    fam_Address1 VARCHAR(255),
    fam_Address2 VARCHAR(255),
    fam_City VARCHAR(50),
    fam_State VARCHAR(50),
    fam_Zip VARCHAR(10),
    fam_Country VARCHAR(50),
    fam_HomePhone VARCHAR(30),
    fam_WorkPhone VARCHAR(30),
    fam_CellPhone VARCHAR(30),
    fam_Email VARCHAR(100),
    fam_WeddingDate DATE,
    fam_DateEntered DATETIME DEFAULT CURRENT_TIMESTAMP,
    fam_EnteredBy BIGINT UNSIGNED,
    fam_DateLastEdited DATETIME,
    fam_EditedBy BIGINT UNSIGNED,
    fam_SendNewsLetter BOOLEAN DEFAULT FALSE,
    fam_OkToCanvass BOOLEAN DEFAULT FALSE,
    fam_Canvasser BIGINT UNSIGNED,
    fam_Latitude DECIMAL(10,8),
    fam_Longitude DECIMAL(11,8),
    fam_Envelope VARCHAR(50),
    fam_CustomFields TEXT,
    fam_Notes TEXT,
    fam_SearchText TEXT,
    INDEX idx_fam_Name (fam_Name),
    INDEX idx_fam_City (fam_City),
    INDEX idx_fam_State (fam_State),
    INDEX idx_fam_Zip (fam_Zip),
    FULLTEXT idx_fam_SearchText (fam_SearchText)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Inserir usuário admin padrão (senha: 'changeme')
-- Nota: Esta senha deve ser alterada durante a instalação
INSERT IGNORE INTO user_usr (
    usr_per_ID,
    usr_UserName,
    usr_Password,
    usr_Admin,
    usr_NeedPasswordChange,
    usr_DateCreated
) VALUES (
    1,
    'admin',
    '$2y$10$K8m8K7K7K7K7K7K7K7K7KO7K7K7K7K7K7K7K7K7K7K7K7K7K7K7K7O', -- Hash para 'changeme'
    1,
    1,
    NOW()
);

-- 8. Inserir pessoa admin básica
INSERT IGNORE INTO person_per (
    per_ID,
    per_FirstName,
    per_LastName,
    per_Email,
    per_Gender,
    per_EnteredBy,
    per_EnteredDate
) VALUES (
    1,
    'Admin',
    'User',
    'admin@churchcrm.local',
    'Unknown',
    1,
    NOW()
);

-- 9. Criar usuário de teste (opcional)
INSERT IGNORE INTO user_usr (
    usr_per_ID,
    usr_UserName,
    usr_Password,
    usr_Admin,
    usr_NeedPasswordChange,
    usr_DateCreated
) VALUES (
    2,
    'test',
    '$2y$10$K8m8K7K7K7K7K7K7K7K7KO7K7K7K7K7K7K7K7K7K7K7K7K7K7K7K7O', -- Hash para 'test123'
    0,
    1,
    NOW()
);

INSERT IGNORE INTO person_per (
    per_ID,
    per_FirstName,
    per_LastName,
    per_Email,
    per_Gender,
    per_EnteredBy,
    per_EnteredDate
) VALUES (
    2,
    'Test',
    'User',
    'test@churchcrm.local',
    'Unknown',
    1,
    NOW()
);

-- 10. Verificação final
SELECT 
    'Database Setup Complete' as Status,
    DATABASE() as DatabaseName,
    USER() as CurrentUser,
    NOW() as SetupTime;

-- Mostrar tabelas criadas
SHOW TABLES;

-- Mostrar usuários criados
SELECT 
    usr_ID,
    usr_UserName,
    usr_Admin,
    usr_DateCreated
FROM user_usr;

-- Instruções pós-instalação
SELECT '=== PRÓXIMOS PASSOS ===' as Instructions;
SELECT '1. Execute ./setup-local.sh para configurar o ambiente' as Step1;
SELECT '2. Execute ./start-local.sh para iniciar o servidor' as Step2;
SELECT '3. Acesse http://localhost:8080 para completar instalação' as Step3;
SELECT '4. Faça login com admin/changeme (altere após primeiro acesso)' as Step4;
SELECT '5. Configure os módulos e preferências do sistema' as Step5;
