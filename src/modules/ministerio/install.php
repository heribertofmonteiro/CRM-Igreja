<?php

/**
 * Instalador do Módulo Ministério & Comunicação
 * 
 * Este arquivo guia o processo de instalação do módulo,
 * verificando pré-requisitos e criando as tabelas necessárias.
 */

require_once __DIR__ . '/config.php';

// Verificar pré-requisitos
function checkPrerequisites() {
    $errors = [];
    
    // Verificar versão do PHP
    if (version_compare(PHP_VERSION, '8.2.0', '<')) {
        $errors[] = 'PHP 8.2+ é requerido. Versão atual: ' . PHP_VERSION;
    }
    
    // Verificar extensões necessárias
    $requiredExtensions = ['pdo', 'json', 'mbstring', 'mysqli'];
    foreach ($requiredExtensions as $ext) {
        if (!extension_loaded($ext)) {
            $errors[] = "Extensão PHP '{$ext}' não está instalada.";
        }
    }
    
    // Verificar conexão com banco de dados
    try {
        $pdo = new PDO(
            'mysql:host=localhost;dbname=autonomo;charset=utf8mb4',
            'heriberto',
            '0631'
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $e) {
        $errors[] = 'Não foi possível conectar ao banco de dados: ' . $e->getMessage();
    }
    
    return $errors;
}

// Verificar se o módulo já está instalado
function isModuleInstalled() {
    try {
        $pdo = new PDO(
            'mysql:host=localhost;dbname=autonomo;charset=utf8mb4',
            'heriberto',
            '0631'
        );
        $stmt = $pdo->query("SHOW TABLES LIKE 'ministerios'");
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

// Executar instalação
function installModule() {
    try {
        // Ler arquivo SQL
        $sqlFile = __DIR__ . '/../mysql/upgrade/ministerio-module-simple.sql';
        if (!file_exists($sqlFile)) {
            throw new Exception('Arquivo SQL de instalação não encontrado.');
        }
        
        $sql = file_get_contents($sqlFile);
        
        // Conectar ao banco e executar SQL
        $pdo = new PDO(
            'mysql:host=localhost;dbname=autonomo;charset=utf8mb4',
            'heriberto',
            '0631'
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Executar SQL em múltiplas partes (para evitar limites)
        $statements = explode(';', $sql);
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
        
        // Criar diretórios necessários
        $directories = [
            MINISTERIO_UPLOAD_PATH,
            MINISTERIO_LOG_PATH
        ];
        
        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
        
        // Criar arquivo de configuração inicial
        $configFile = __DIR__ . '/config.json';
        if (!file_exists($configFile)) {
            $initialConfig = [
                'installed_at' => date('Y-m-d H:i:s'),
                'version' => MINISTERIO_VERSION,
                'db_version' => '1.0',
                'debug_mode' => MINISTERIO_DEBUG_MODE
            ];
            file_put_contents($configFile, json_encode($initialConfig, JSON_PRETTY_PRINT));
        }
        
        return true;
        
    } catch (Exception $e) {
        throw new Exception('Erro durante a instalação: ' . $e->getMessage());
    }
}

// Processar instalação
$step = $_GET['step'] ?? '1';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação - <?= MINISTERIO_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        .step-indicator {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            margin-bottom: 20px;
        }
        .step-active {
            background: linear-gradient(45deg, #28a745, #20c997);
        }
        .step-completed {
            background: linear-gradient(45deg, #6c757d, #495057);
        }
        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .check-icon {
            color: #28a745;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>
                            <i class="fas fa-church"></i>
                            <?= MINISTERIO_NAME ?>
                        </h3>
                        <p class="text-muted mb-0">Instalador v<?= MINISTERIO_VERSION ?></p>
                    </div>
                    <div class="card-body">
                        <?php if ($step == '1'): ?>
                            <!-- Etapa 1: Verificação de Pré-requisitos -->
                            <div class="step-indicator step-active">
                                <i class="fas fa-search"></i>
                                <strong>Etapa 1: Verificando Pré-requisitos</strong>
                            </div>
                            
                            <?php
                            $errors = checkPrerequisites();
                            if (!empty($errors)):
                            ?>
                                <div class="alert alert-danger">
                                    <h5><i class="fas fa-exclamation-triangle"></i> Erros Encontrados</h5>
                                    <ul>
                                        <?php foreach ($errors as $error): ?>
                                            <li><?= htmlspecialchars($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="install.php?step=1" class="btn btn-warning">
                                        <i class="fas fa-redo"></i> Verificar Novamente
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-success">
                                    <h5><i class="fas fa-check-circle"></i> Pré-requisitos OK</h5>
                                    <p>Todos os pré-requisitos estão instalados e funcionando.</p>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="install.php?step=2" class="btn btn-primary btn-lg">
                                        <i class="fas fa-arrow-right"></i> Continuar para Etapa 2
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                        <?php elseif ($step == '2'): ?>
                            <!-- Etapa 2: Instalação do Módulo -->
                            <div class="step-indicator step-active">
                                <i class="fas fa-cogs"></i>
                                <strong>Etapa 2: Instalando Módulo</strong>
                            </div>
                            
                            <?php
                            if (isModuleInstalled()):
                            ?>
                                <div class="alert alert-warning">
                                    <h5><i class="fas fa-exclamation-triangle"></i> Módulo Já Instalado</h5>
                                    <p>O módulo já está instalado no sistema.</p>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="../" class="btn btn-success">
                                        <i class="fas fa-check"></i> Acessar Módulo
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="text-center">
                                    <p class="mb-3">Clique no botão abaixo para instalar o módulo:</p>
                                    <form method="POST">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-download"></i> Instalar <?= MINISTERIO_NAME ?>
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                            
                        <?php elseif ($step == '3'): ?>
                            <!-- Etapa 3: Instalação Concluída -->
                            <div class="step-indicator step-completed">
                                <i class="fas fa-check check-icon"></i>
                                <strong>Etapa 3: Instalação Concluída</strong>
                            </div>
                            
                            <div class="alert alert-success">
                                <h5><i class="fas fa-check-circle"></i> Instalação Concluída com Sucesso!</h5>
                                <p>O módulo <?= MINISTERIO_NAME ?> foi instalado com sucesso.</p>
                                <ul>
                                    <li><i class="fas fa-check text-success"></i> Tabelas criadas no banco de dados</li>
                                    <li><i class="fas fa-check text-success"></i> Diretórios de uploads e logs criados</li>
                                    <li><i class="fas fa-check text-success"></i> Configurações iniciais aplicadas</li>
                                    <li><i class="fas fa-check text-success"></i> Sistema pronto para uso</li>
                                </ul>
                            </div>
                            <div class="text-center mt-4">
                                <a href="index.php" class="btn btn-success btn-lg">
                                    <i class="fas fa-tachometer-alt"></i> Acessar Módulo
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Processar instalação se formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step == '2') {
    try {
        installModule();
        header('Location: install.php?step=3');
        exit;
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">Erro: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}
?>
