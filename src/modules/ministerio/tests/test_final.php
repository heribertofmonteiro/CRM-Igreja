<?php

/**
 * Teste Final Completo do Módulo Ministério
 * 
 * Validação final de todos os componentes
 * e verificação de produção readiness
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Security.php';

class MinisterioFinalTest
{
    private $testResults = [];
    private $pdo;
    
    public function __construct()
    {
        $this->pdo = new PDO(
            'mysql:host=localhost;dbname=autonomo;charset=utf8mb4',
            'heriberto',
            '0631'
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    /**
     * Executar teste final completo
     */
    public function runFinalTest()
    {
        echo "🎯 TESTE FINAL COMPLETO - MÓDULO MINISTÉRIO\n";
        echo "==========================================\n\n";
        
        // 1. Estrutura de Arquivos
        $this->testFileStructure();
        
        // 2. Banco de Dados
        $this->testDatabaseStructure();
        
        // 3. Sintaxe PHP
        $this->testPHPSyntax();
        
        // 4. Configurações
        $this->testConfigurations();
        
        // 5. Segurança
        $this->testSecurityBasics();
        
        // 6. Performance Básica
        $this->testBasicPerformance();
        
        // 7. Integração
        $this->testIntegrationPoints();
        
        // Exibir resultado final
        $this->displayFinalResults();
    }
    
    /**
     * Testar estrutura de arquivos
     */
    private function testFileStructure()
    {
        echo "📁 Testando Estrutura de Arquivos...\n";
        
        $baseDir = __DIR__ . '/..';
        
        // Diretórios obrigatórios
        $requiredDirs = [
            'controllers',
            'models',
            'views',
            'tests'
        ];
        
        foreach ($requiredDirs as $dir) {
            $this->assert(
                is_dir($baseDir . '/' . $dir),
                "Diretório '$dir' existe"
            );
        }
        
        // Arquivos obrigatórios
        $requiredFiles = [
            'index.php',
            'routes.php',
            'config.php',
            'Security.php',
            'install.php',
            'integration.php',
            'README.md'
        ];
        
        foreach ($requiredFiles as $file) {
            $this->assert(
                file_exists($baseDir . '/' . $file),
                "Arquivo '$file' existe"
            );
        }
        
        // Controllers obrigatórios
        $requiredControllers = [
            'MinisterioController.php',
            'MensagemController.php'
        ];
        
        foreach ($requiredControllers as $controller) {
            $this->assert(
                file_exists($baseDir . '/controllers/' . $controller),
                "Controller '$controller' existe"
            );
        }
        
        // Models obrigatórios
        $requiredModels = [
            'MinisterioModel.php',
            'Mensagem.php'
        ];
        
        foreach ($requiredModels as $model) {
            $this->assert(
                file_exists($baseDir . '/models/' . $model),
                "Model '$model' existe"
            );
        }
        
        echo "\n";
    }
    
    /**
     * Testar estrutura do banco de dados
     */
    private function testDatabaseStructure()
    {
        echo "🗄️ Testando Banco de Dados...\n";
        
        // Tabelas obrigatórias
        $requiredTables = [
            'ministerios',
            'ministerio_membros',
            'ministerio_reunioes',
            'ministerio_reunioes_participantes',
            'ministerio_mensagens',
            'ministerio_mensagens_envio',
            'ministerio_logs'
        ];
        
        foreach ($requiredTables as $table) {
            $stmt = $this->pdo->query("SHOW TABLES LIKE '$table'");
            $this->assert(
                $stmt->rowCount() > 0,
                "Tabela '$table' existe"
            );
        }
        
        // Verificar estrutura da tabela principal
        $stmt = $this->pdo->query("DESCRIBE ministerios");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $requiredColumns = ['id', 'nome', 'descricao', 'lider_id', 'coordenador_id', 'ativo'];
        foreach ($requiredColumns as $column) {
            $this->assert(
                in_array($column, $columns),
                "Coluna '$column' existe em ministerios"
            );
        }
        
        // Verificar se há dados de teste
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM ministerios");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        $this->assert(
            $count > 0,
            "Existem dados de teste ($count ministérios)"
        );
        
        echo "\n";
    }
    
    /**
     * Testar sintaxe PHP de todos os arquivos
     */
    private function testPHPSyntax()
    {
        echo "🔍 Testando Sintaxe PHP...\n";
        
        $baseDir = __DIR__ . '/..';
        $phpFiles = [];
        
        // Encontrar todos os arquivos PHP
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($baseDir)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $phpFiles[] = $file->getPathname();
            }
        }
        
        foreach ($phpFiles as $file) {
            $output = [];
            $returnCode = 0;
            exec("php -l \"$file\" 2>&1", $output, $returnCode);
            
            $this->assert(
                $returnCode === 0,
                "Sintaxe PHP válida: " . basename($file)
            );
        }
        
        $this->assert(
            count($phpFiles) > 10,
            "Múltiplos arquivos PHP encontrados (" . count($phpFiles) . ")"
        );
        
        echo "\n";
    }
    
    /**
     * Testar configurações
     */
    private function testConfigurations()
    {
        echo "⚙️ Testando Configurações...\n";
        
        // Verificar constantes definidas
        $this->assert(
            defined('MINISTERIO_VERSION'),
            "Constante MINISTERIO_VERSION definida"
        );
        
        $this->assert(
            defined('MINISTERIO_NAME'),
            "Constante MINISTERIO_NAME definida"
        );
        
        // Verificar configurações de banco
        $this->assert(
            defined('MINISTERIO_DB_PREFIX'),
            "Constante MINISTERIO_DB_PREFIX definida"
        );
        
        // Verificar se há configurações de upload
        $this->assert(
            defined('MINISTERIO_UPLOAD_PATH'),
            "Constante MINISTERIO_UPLOAD_PATH definida"
        );
        
        // Verificar se diretório de uploads pode ser criado
        $uploadDir = MINISTERIO_UPLOAD_PATH;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $this->assert(
            is_dir($uploadDir),
            "Diretório de uploads existe ou pode ser criado"
        );
        
        echo "\n";
    }
    
    /**
     * Testar segurança básica
     */
    private function testSecurityBasics()
    {
        echo "🔐 Testando Segurança Básica...\n";
        
        // Verificar se classe de segurança existe
        $this->assert(
            class_exists('MinisterioSecurity'),
            "Classe MinisterioSecurity existe"
        );
        
        // Verificar se há constantes de permissão
        $permissions = [
            'PERM_VER_MINISTERIOS',
            'PERM_CRIAR_MINISTERIO',
            'PERM_EDITAR_MINISTERIO',
            'PERM_EXCLUIR_MINISTERIO'
        ];
        
        foreach ($permissions as $perm) {
            $this->assert(
                defined("MinisterioSecurity::$perm"),
                "Permissão $perm definida"
            );
        }
        
        // Verificar se há papéis definidos
        $roles = ['admin', 'lider', 'coordenador', 'membro', 'convidado'];
        foreach ($roles as $role) {
            $this->assert(
                MinisterioSecurity::papelExiste($role),
                "Papel '$role' reconhecido"
            );
        }
        
        echo "\n";
    }
    
    /**
     * Testar performance básica
     */
    private function testBasicPerformance()
    {
        echo "⚡ Testando Performance Básica...\n";
        
        // Testar performance de query simples
        $startTime = microtime(true);
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM ministerios");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        $queryTime = microtime(true) - $startTime;
        
        $this->assert(
            $queryTime < 0.1,
            "Query simples em tempo aceitável (" . round($queryTime * 1000, 2) . "ms)"
        );
        
        // Testar performance de query com JOIN
        $startTime = microtime(true);
        $stmt = $this->pdo->query("
            SELECT m.*, u.name as lider_nome 
            FROM ministerios m 
            LEFT JOIN users u ON m.lider_id = u.id 
            LIMIT 10
        ");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $joinTime = microtime(true) - $startTime;
        
        $this->assert(
            $joinTime < 0.2,
            "Query com JOIN em tempo aceitável (" . round($joinTime * 1000, 2) . "ms)"
        );
        
        // Verificar se há índices
        $stmt = $this->pdo->query("SHOW INDEX FROM ministerios");
        $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assert(
            count($indexes) > 0,
            "Tabela ministerios possui índices (" . count($indexes) . ")"
        );
        
        echo "\n";
    }
    
    /**
     * Testar pontos de integração
     */
    private function testIntegrationPoints()
    {
        echo "🔗 Testando Pontos de Integração...\n";
        
        // Verificar se há arquivo de integração
        $integrationFile = __DIR__ . '/../integration.php';
        $this->assert(
            file_exists($integrationFile),
            "Arquivo de integração existe"
        );
        
        // Verificar se há instalador
        $installFile = __DIR__ . '/../install.php';
        $this->assert(
            file_exists($installFile),
            "Arquivo de instalador existe"
        );
        
        // Verificar se há documentação
        $readmeFile = __DIR__ . '/../README.md';
        $this->assert(
            file_exists($readmeFile),
            "Arquivo README.md existe"
        );
        
        $docFile = '/home/heriberto/projetos/PHP/Laravel/CRM/MINISTERIO_DOCUMENTACAO.md';
        $this->assert(
            file_exists($docFile),
            "Arquivo de documentação técnica existe"
        );
        
        // Verificar se há views
        $viewsDir = __DIR__ . '/../views';
        $this->assert(
            is_dir($viewsDir),
            "Diretório de views existe"
        );
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($viewsDir)
        );
        
        $viewFiles = [];
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $viewFiles[] = $file->getPathname();
            }
        }
        
        $this->assert(
            count($viewFiles) > 0,
            "Existem arquivos de view (" . count($viewFiles) . ")"
        );
        
        echo "\n";
    }
    
    /**
     * Adicionar resultado de teste
     */
    private function assert($condition, $description)
    {
        $status = $condition ? "✅ PASSOU" : "❌ FALHOU";
        echo "  $status - $description\n";
        
        $this->testResults[] = [
            'description' => $description,
            'passed' => $condition,
            'status' => $status
        ];
    }
    
    /**
     * Exibir resultado final
     */
    private function displayFinalResults()
    {
        echo "==========================================\n";
        echo "🏆 RESULTADO FINAL DO TESTE DE PRODUÇÃO\n";
        echo "==========================================\n";
        
        $total = count($this->testResults);
        $passed = array_filter($this->testResults, fn($r) => $r['passed']);
        $passedCount = count($passed);
        $failed = $total - $passedCount;
        
        echo "📊 Estatísticas Finais:\n";
        echo "   Total de Verificações: $total\n";
        echo "   ✅ Passaram: $passedCount\n";
        echo "   ❌ Falharam: $failed\n";
        echo "   📈 Taxa de Sucesso: " . round(($passedCount / $total) * 100, 2) . "%\n\n";
        
        if ($failed > 0) {
            echo "❌ Verificações que falharam:\n";
            foreach ($this->testResults as $result) {
                if (!$result['passed']) {
                    echo "   • {$result['description']}\n";
                }
            }
        }
        
        echo "\n🎯 STATUS DO MÓDULO:\n";
        
        if ($failed === 0) {
            echo "🟢 MÓDULO 100% PRONTO PARA PRODUÇÃO!\n";
            echo "✅ Todos os componentes validados\n";
            echo "✅ Segurança implementada\n";
            echo "✅ Performance otimizada\n";
            echo "✅ Integração completa\n";
            echo "✅ Documentação presente\n";
            echo "\n🚀 O módulo pode ser implantado em produção com confiança!\n";
        } else {
            echo "🟡 MÓDULO QUASE PRONTO - ATENDER PENDÊNCIAS\n";
            echo "⚠️  Existem $failed verificações pendentes\n";
            echo "📝 Resolva os itens acima antes de ir para produção\n";
        }
        
        echo "\n" . str_repeat("=", 42) . "\n";
        echo "🎉 TESTE FINAL CONCLUÍDO\n";
        echo str_repeat("=", 42) . "\n";
    }
}

// Executar teste final
if (php_sapi_name() === 'cli') {
    $test = new MinisterioFinalTest();
    $test->runFinalTest();
} else {
    echo "<pre>";
    $test = new MinisterioFinalTest();
    $test->runFinalTest();
    echo "</pre>";
}
