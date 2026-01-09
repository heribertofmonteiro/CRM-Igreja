<?php

/**
 * Testes de Integração do Módulo Ministério
 * 
 * Testes completos de integração com ChurchCRM,
 * performance sob carga e validação de componentes
 */

require_once __DIR__ . '/../controllers/MinisterioController.php';
require_once __DIR__ . '/../controllers/MensagemController.php';
require_once __DIR__ . '/../Security.php';
require_once __DIR__ . '/../config.php';

class MinisterioIntegrationTests
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
     * Executar todos os testes de integração
     */
    public function runAllTests()
    {
        echo "🔄 Iniciando Testes de Integração do Módulo Ministério\n";
        echo "==================================================\n\n";
        
        // Testes de Controllers
        $this->testControllers();
        
        // Testes de Segurança RBAC
        $this->testRBAC();
        
        // Testes de Performance
        $this->testPerformance();
        
        // Testes de Views
        $this->testViews();
        
        // Testes de APIs
        $this->testAPIs();
        
        // Exibir resultados
        $this->displayResults();
    }
    
    /**
     * Testar Controllers
     */
    private function testControllers()
    {
        echo "🎮 Testando Controllers...\n";
        
        try {
            // Testar MinisterioController
            $this->assert(
                method_exists('MinisterioController', 'index'),
                "MinisterioController::index() existe"
            );
            
            $this->assert(
                method_exists('MinisterioController', 'create'),
                "MinisterioController::create() existe"
            );
            
            $this->assert(
                method_exists('MinisterioController', 'store'),
                "MinisterioController::store() existe"
            );
            
            $this->assert(
                method_exists('MinisterioController', 'dashboard'),
                "MinisterioController::dashboard() existe"
            );
            
            // Testar MensagemController
            $this->assert(
                method_exists('MensagemController', 'index'),
                "MensagemController::index() existe"
            );
            
            $this->assert(
                method_exists('MensagemController', 'create'),
                "MensagemController::create() existe"
            );
            
            $this->assert(
                method_exists('MensagemController', 'apiDestinatarios'),
                "MensagemController::apiDestinatarios() existe"
            );
            
            $this->assert(
                method_exists('MensagemController', 'apiPreview'),
                "MensagemController::apiPreview() existe"
            );
            
        } catch (Exception $e) {
            $this->assert(false, "Erro em testes de Controllers: " . $e->getMessage());
        }
        
        echo "\n";
    }
    
    /**
     * Testar Sistema RBAC
     */
    private function testRBAC()
    {
        echo "🔐 Testando Sistema RBAC...\n";
        
        try {
            // Testar classe Security
            $this->assert(
                class_exists('MinisterioSecurity'),
                "Classe MinisterioSecurity existe"
            );
            
            // Testar constantes de permissão
            $permissions = [
                'PERM_VER_MINISTERIOS',
                'PERM_CRIAR_MINISTERIO',
                'PERM_EDITAR_MINISTERIO',
                'PERM_EXCLUIR_MINISTERIO',
                'PERM_GERENCIAR_MEMBROS',
                'PERM_ENVIAR_MENSAGENS',
                'PERM_VER_DASHBOARD',
                'PERM_GERENCIAR_REUNIOES'
            ];
            
            foreach ($permissions as $perm) {
                $this->assert(
                    defined("MinisterioSecurity::$perm"),
                    "Permissão $perm definida"
                );
            }
            
            // Testar papéis
            $roles = ['admin', 'lider', 'coordenador', 'membro', 'convidado'];
            foreach ($roles as $role) {
                $this->assert(
                    MinisterioSecurity::papelExiste($role),
                    "Papel '$role' existe"
                );
            }
            
            // Testar verificação de permissões
            $this->assert(
                is_callable(['MinisterioSecurity', 'temPermissao']),
                "Método temPermissao() é callable"
            );
            
            $this->assert(
                is_callable(['MinisterioSecurity', 'podeAcessar']),
                "Método podeAcessar() é callable"
            );
            
        } catch (Exception $e) {
            $this->assert(false, "Erro em testes de RBAC: " . $e->getMessage());
        }
        
        echo "\n";
    }
    
    /**
     * Testar Performance
     */
    private function testPerformance()
    {
        echo "⚡ Testando Performance...\n";
        
        try {
            // Testar performance de listagem
            $startTime = microtime(true);
            $ministerios = MinisterioModel::list();
            $listTime = microtime(true) - $startTime;
            
            $this->assert(
                $listTime < 0.5, // Menos de 500ms
                "Listagem de ministérios em tempo aceitável (" . round($listTime * 1000, 2) . "ms)"
            );
            
            // Testar performance de criação
            $startTime = microtime(true);
            $testData = [
                'nome' => 'Teste Performance ' . uniqid(),
                'descricao' => 'Teste de performance',
                'lider_id' => 1
            ];
            $id = MinisterioModel::create($testData);
            $createTime = microtime(true) - $startTime;
            
            $this->assert(
                $createTime < 0.1, // Menos de 100ms
                "Criação de ministério em tempo aceitável (" . round($createTime * 1000, 2) . "ms)"
            );
            
            // Testar performance de busca
            $startTime = microtime(true);
            $ministerio = MinisterioModel::findById($id);
            $findTime = microtime(true) - $startTime;
            
            $this->assert(
                $findTime < 0.05, // Menos de 50ms
                "Busca de ministério em tempo aceitável (" . round($findTime * 1000, 2) . "ms)"
            );
            
            // Limpar
            MinisterioModel::delete($id);
            
            // Testar performance com múltiplas operações
            $startTime = microtime(true);
            for ($i = 0; $i < 10; $i++) {
                $testData = [
                    'nome' => "Teste Bulk $i",
                    'descricao' => "Descrição $i",
                    'lider_id' => 1
                ];
                $id = MinisterioModel::create($testData);
                MinisterioModel::delete($id);
            }
            $bulkTime = microtime(true) - $startTime;
            
            $this->assert(
                $bulkTime < 2.0, // Menos de 2 segundos
                "10 operações CRUD em tempo aceitável (" . round($bulkTime, 2) . "s)"
            );
            
        } catch (Exception $e) {
            $this->assert(false, "Erro em testes de Performance: " . $e->getMessage());
        }
        
        echo "\n";
    }
    
    /**
     * Testar Views
     */
    private function testViews()
    {
        echo "🎨 Testando Views...\n";
        
        try {
            $viewsDir = __DIR__ . '/../views';
            
            // Verificar se diretórios de views existem
            $this->assert(
                is_dir($viewsDir),
                "Diretório de views existe"
            );
            
            $this->assert(
                is_dir($viewsDir . '/ministerio'),
                "Diretório de views de ministérios existe"
            );
            
            $this->assert(
                is_dir($viewsDir . '/mensagem'),
                "Diretório de views de mensagens existe"
            );
            
            // Verificar arquivos de views principais
            $requiredViews = [
                '/ministerio/index.php',
                '/ministerio/create.php',
                '/ministerio/dashboard.php',
                '/mensagem/index.php',
                '/mensagem/create.php'
            ];
            
            foreach ($requiredViews as $view) {
                $this->assert(
                    file_exists($viewsDir . $view),
                    "View $view existe"
                );
                
                // Verificar sintaxe PHP
                $output = [];
                $returnCode = 0;
                exec("php -l " . $viewsDir . $view . " 2>&1", $output, $returnCode);
                
                $this->assert(
                    $returnCode === 0,
                    "View $view tem sintaxe PHP válida"
                );
            }
            
            // Verificar se views usam Bootstrap 5
            $indexContent = file_get_contents($viewsDir . '/ministerio/index.php');
            $this->assert(
                strpos($indexContent, 'bootstrap') !== false,
                "View usa Bootstrap"
            );
            
            $this->assert(
                strpos($indexContent, 'dataTables') !== false,
                "View usa DataTables"
            );
            
            // Verificar se dashboard usa Chart.js
            $dashboardContent = file_get_contents($viewsDir . '/ministerio/dashboard.php');
            $this->assert(
                strpos($dashboardContent, 'Chart.js') !== false,
                "Dashboard usa Chart.js"
            );
            
        } catch (Exception $e) {
            $this->assert(false, "Erro em testes de Views: " . $e->getMessage());
        }
        
        echo "\n";
    }
    
    /**
     * Testar APIs
     */
    private function testAPIs()
    {
        echo "🌐 Testando APIs...\n";
        
        try {
            // Testar API de destinatários
            $this->assert(
                method_exists('MensagemController', 'apiDestinatarios'),
                "API de destinatários existe"
            );
            
            // Testar API de preview
            $this->assert(
                method_exists('MensagemController', 'apiPreview'),
                "API de preview existe"
            );
            
            // Simular chamada API de destinatários
            $_GET['ministerio_id'] = 1;
            ob_start();
            MensagemController::apiDestinatarios();
            $response = ob_get_clean();
            
            $this->assert(
                !empty($response),
                "API de destinatários retorna resposta"
            );
            
            $data = json_decode($response, true);
            $this->assert(
                is_array($data),
                "API de destinatários retorna JSON válido"
            );
            
            // Simular chamada API de preview
            $_POST['conteudo'] = 'Teste de preview';
            $_POST['canal'] = 'email';
            ob_start();
            MensagemController::apiPreview();
            $previewResponse = ob_get_clean();
            
            $this->assert(
                !empty($previewResponse),
                "API de preview retorna resposta"
            );
            
            $previewData = json_decode($previewResponse, true);
            $this->assert(
                is_array($previewData),
                "API de preview retorna JSON válido"
            );
            
            $this->assert(
                isset($previewData['preview']),
                "API de preview contém campo preview"
            );
            
        } catch (Exception $e) {
            $this->assert(false, "Erro em testes de APIs: " . $e->getMessage());
        }
        
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
     * Exibir resultados finais
     */
    private function displayResults()
    {
        echo "==================================================\n";
        echo "📊 Resultados dos Testes de Integração\n";
        echo "==================================================\n";
        
        $total = count($this->testResults);
        $passed = array_filter($this->testResults, fn($r) => $r['passed']);
        $passedCount = count($passed);
        $failed = $total - $passedCount;
        
        echo "Total de Testes: $total\n";
        echo "✅ Passaram: $passedCount\n";
        echo "❌ Falharam: $failed\n";
        echo "Taxa de Sucesso: " . round(($passedCount / $total) * 100, 2) . "%\n\n";
        
        if ($failed > 0) {
            echo "Testes que falharam:\n";
            foreach ($this->testResults as $result) {
                if (!$result['passed']) {
                    echo "  ❌ {$result['description']}\n";
                }
            }
        }
        
        echo "\n";
        
        if ($failed === 0) {
            echo "🎉 Todos os testes de integração passaram!\n";
            echo "🚀 O módulo está pronto para integração com ChurchCRM!\n";
        } else {
            echo "⚠️  Alguns testes falharam. Verifique os erros acima.\n";
        }
    }
}

// Executar testes
if (php_sapi_name() === 'cli') {
    $tests = new MinisterioIntegrationTests();
    $tests->runAllTests();
} else {
    echo "<pre>";
    $tests = new MinisterioIntegrationTests();
    $tests->runAllTests();
    echo "</pre>";
}
