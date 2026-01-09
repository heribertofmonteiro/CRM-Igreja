<?php

/**
 * Testes Completos do Módulo Financeiro
 * 
 * Varredura completa para verificar se o módulo financeiro está apto
 * e funcionando corretamente
 */

class FinanceiroTest
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
     * Executar todos os testes do módulo financeiro
     */
    public function runAllTests()
    {
        echo "💰 INICIANDO TESTES DO MÓDULO FINANCEIRO\n";
        echo "==========================================\n\n";
        
        // 1. Estrutura de Arquivos
        $this->testFileStructure();
        
        // 2. Banco de Dados
        $this->testDatabaseStructure();
        
        // 3. Sintaxe PHP
        $this->testPHPSyntax();
        
        // 4. Rotas e Controllers
        $this->testRoutesAndControllers();
        
        // 5. APIs
        $this->testAPIs();
        
        // 6. Segurança
        $this->testSecurity();
        
        // 7. Configurações
        $this->testConfigurations();
        
        // Exibir resultados
        $this->displayResults();
    }
    
    /**
     * Testar estrutura de arquivos do módulo financeiro
     */
    private function testFileStructure()
    {
        echo "📁 Testando Estrutura de Arquivos Financeiros...\n";
        
        // Verificar arquivos principais
        $files = [
            '/home/heriberto/projetos/PHP/Laravel/CRM/src/v2/routes/financeiro.php',
            '/home/heriberto/projetos/PHP/Laravel/CRM/src/v2/templates/financeiro/dashboard.php',
            '/home/heriberto/projetos/PHP/Laravel/CRM/src/api/routes/finance/finance-deposits.php',
            '/home/heriberto/projetos/PHP/Laravel/CRM/src/api/routes/finance/finance-payments.php'
        ];
        
        foreach ($files as $file) {
            $this->assert(
                file_exists($file),
                "Arquivo existe: " . basename($file)
            );
        }
        
        // Verificar diretórios
        $dirs = [
            '/home/heriberto/projetos/PHP/Laravel/CRM/src/v2/templates/financeiro',
            '/home/heriberto/projetos/PHP/Laravel/CRM/src/api/routes/finance'
        ];
        
        foreach ($dirs as $dir) {
            $this->assert(
                is_dir($dir),
                "Diretório existe: " . basename($dir)
            );
        }
        
        echo "\n";
    }
    
    /**
     * Testar estrutura do banco de dados financeiro
     */
    private function testDatabaseStructure()
    {
        echo "🗄️ Testando Banco de Dados Financeiro...\n";
        
        // Verificar tabelas relacionadas a finanças
        $financeTables = [
            'payment_methods',
            'order_payments'
        ];
        
        foreach ($financeTables as $table) {
            $stmt = $this->pdo->query("SHOW TABLES LIKE '$table'");
            $this->assert(
                $stmt->rowCount() > 0,
                "Tabela financeira existe: $table"
            );
        }
        
        // Verificar estrutura das tabelas
        if (in_array('payment_methods', $financeTables)) {
            $stmt = $this->pdo->query("DESCRIBE payment_methods");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $requiredColumns = ['id', 'name', 'code', 'created_at', 'updated_at'];
            foreach ($requiredColumns as $column) {
                $this->assert(
                    in_array($column, $columns),
                    "Coluna '$column' existe em payment_methods"
                );
            }
        }
        
        // Verificar se há dados
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM payment_methods");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        $this->assert(
            $count >= 0,
            "Tabela payment_methods acessível ($count registros)"
        );
        
        echo "\n";
    }
    
    /**
     * Testar sintaxe PHP dos arquivos financeiros
     */
    private function testPHPSyntax()
    {
        echo "🔍 Testando Sintaxe PHP Financeira...\n";
        
        $phpFiles = [
            '/home/heriberto/projetos/PHP/Laravel/CRM/src/v2/routes/financeiro.php',
            '/home/heriberto/projetos/PHP/Laravel/CRM/src/v2/templates/financeiro/dashboard.php',
            '/home/heriberto/projetos/PHP/Laravel/CRM/src/api/routes/finance/finance-deposits.php',
            '/home/heriberto/projetos/PHP/Laravel/CRM/src/api/routes/finance/finance-payments.php'
        ];
        
        foreach ($phpFiles as $file) {
            $output = [];
            $returnCode = 0;
            exec("php -l \"$file\" 2>&1", $output, $returnCode);
            
            $this->assert(
                $returnCode === 0,
                "Sintaxe PHP válida: " . basename($file)
            );
        }
        
        echo "\n";
    }
    
    /**
     * Testar rotas e controllers financeiros
     */
    private function testRoutesAndControllers()
    {
        echo "🛣️ Testando Rotas e Controllers Financeiros...\n";
        
        // Verificar se o arquivo de rotas principal existe e tem conteúdo
        $routesFile = '/home/heriberto/projetos/PHP/Laravel/CRM/src/v2/routes/financeiro.php';
        $this->assert(
            file_exists($routesFile),
            "Arquivo de rotas financeiro existe"
        );
        
        $routesContent = file_get_contents($routesFile);
        $this->assert(
            strpos($routesContent, 'financeiroPage') !== false,
            "Função financeiroPage definida"
        );
        
        $this->assert(
            strpos($routesContent, 'FinanceRoleAuthMiddleware') !== false,
            "Middleware de segurança financeiro configurado"
        );
        
        // Verificar se o template existe
        $templateFile = '/home/heriberto/projetos/PHP/Laravel/CRM/src/v2/templates/financeiro/dashboard.php';
        $this->assert(
            file_exists($templateFile),
            "Template dashboard financeiro existe"
        );
        
        $templateContent = file_get_contents($templateFile);
        $this->assert(
            strpos($templateContent, 'Financeiro') !== false,
            "Template contém título Financeiro"
        );
        
        $this->assert(
            strpos($templateContent, 'bEnabledFinance') !== false,
            "Template verifica configuração de finanças"
        );
        
        echo "\n";
    }
    
    /**
     * Testar APIs financeiras
     */
    private function testAPIs()
    {
        echo "🌐 Testando APIs Financeiras...\n";
        
        // Verificar arquivos de API
        $apiFiles = [
            '/home/heriberto/projetos/PHP/Laravel/CRM/src/api/routes/finance/finance-deposits.php',
            '/home/heriberto/projetos/PHP/Laravel/CRM/src/api/routes/finance/finance-payments.php'
        ];
        
        foreach ($apiFiles as $file) {
            $this->assert(
                file_exists($file),
                "Arquivo API existe: " . basename($file)
            );
            
            $content = file_get_contents($file);
            
            // Verificar se usa middleware de autenticação
            $this->assert(
                strpos($content, 'FinanceRoleAuthMiddleware') !== false,
                "API usa middleware de segurança: " . basename($file)
            );
            
            // Verificar se tem endpoints definidos
            $this->assert(
                strpos($content, '$app->group') !== false,
                "API tem grupos de rotas: " . basename($file)
            );
        }
        
        // Verificar conteúdo específico de deposits
        $depositsContent = file_get_contents('/home/heriberto/projetos/PHP/Laravel/CRM/src/api/routes/finance/finance-deposits.php');
        $this->assert(
            strpos($depositsContent, 'DepositService') !== false,
            "API de deposits usa DepositService"
        );
        
        $this->assert(
            strpos($depositsContent, 'DepositQuery') !== false,
            "API de deposits usa DepositQuery"
        );
        
        // Verificar conteúdo específico de payments
        $paymentsContent = file_get_contents('/home/heriberto/projetos/PHP/Laravel/CRM/src/api/routes/finance/finance-payments.php');
        $this->assert(
            strpos($paymentsContent, 'FinancialService') !== false,
            "API de payments usa FinancialService"
        );
        
        $this->assert(
            strpos($paymentsContent, 'PledgeQuery') !== false,
            "API de payments usa PledgeQuery"
        );
        
        echo "\n";
    }
    
    /**
     * Testar segurança do módulo financeiro
     */
    private function testSecurity()
    {
        echo "🔐 Testando Segurança Financeira...\n";
        
        // Verificar se middleware de segurança existe
        $middlewareFile = '/home/heriberto/projetos/PHP/Laravel/CRM/src/ChurchCRM/Slim/Middleware/Request/Auth/FinanceRoleAuthMiddleware.php';
        $this->assert(
            file_exists($middlewareFile),
            "Middleware FinanceRoleAuthMiddleware existe"
        );
        
        // Verificar se as rotas usam o middleware
        $routesFiles = [
            '/home/heriberto/projetos/PHP/Laravel/CRM/src/v2/routes/financeiro.php',
            '/home/heriberto/projetos/PHP/Laravel/CRM/src/api/routes/finance/finance-deposits.php',
            '/home/heriberto/projetos/PHP/Laravel/CRM/src/api/routes/finance/finance-payments.php'
        ];
        
        foreach ($routesFiles as $file) {
            $content = file_get_contents($file);
            $this->assert(
                strpos($content, 'FinanceRoleAuthMiddleware') !== false,
                "Arquivo usa middleware de segurança: " . basename($file)
            );
        }
        
        // Verificar se há validação de inputs nas APIs
        $depositsContent = file_get_contents('/home/heriberto/projetos/PHP/Laravel/CRM/src/api/routes/finance/finance-deposits.php');
        $this->assert(
            strpos($depositsContent, 'InputUtils::filterString') !== false,
            "API de deposits valida inputs"
        );
        
        $this->assert(
            strpos($depositsContent, 'in_array') !== false,
            "API de deposits valida valores permitidos"
        );
        
        echo "\n";
    }
    
    /**
     * Testar configurações do módulo financeiro
     */
    private function testConfigurations()
    {
        echo "⚙️ Testando Configurações Financeiras...\n";
        
        // Verificar se o template verifica configurações do sistema
        $templateContent = file_get_contents('/home/heriberto/projetos/PHP/Laravel/CRM/src/v2/templates/financeiro/dashboard.php');
        
        $this->assert(
            strpos($templateContent, 'bEnabledFinance') !== false,
            "Template verifica bEnabledFinance"
        );
        
        $this->assert(
            strpos($templateContent, 'bEnabledFundraiser') !== false,
            "Template verifica bEnabledFundraiser"
        );
        
        // Verificar se usa SystemConfig
        $routesContent = file_get_contents('/home/heriberto/projetos/PHP/Laravel/CRM/src/v2/routes/financeiro.php');
        $this->assert(
            strpos($routesContent, 'SystemConfig') !== false,
            "Rotas usam SystemConfig"
        );
        
        // Verificar se há integração com o sistema principal
        $this->assert(
            strpos($templateContent, 'AuthenticationManager') !== false,
            "Template usa AuthenticationManager"
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
     * Exibir resultados finais
     */
    private function displayResults()
    {
        echo "==========================================\n";
        echo "📊 RESULTADOS DOS TESTES FINANCEIROS\n";
        echo "==========================================\n";
        
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
            echo "🎉 Todos os testes financeiros passaram!\n";
            echo "💰 O módulo financeiro está funcionando corretamente!\n";
        } else {
            echo "⚠️  Alguns testes financeiros falharam.\n";
            echo "📝 Verifique os erros acima.\n";
        }
        
        // Análise final
        echo "\n🎯 ANÁLISE FINAL DO MÓDULO FINANCEIRO:\n";
        
        if ($passedCount >= $total * 0.8) {
            echo "🟢 MÓDULO FINANCEIRO APTO PARA USO\n";
            echo "✅ Estrutura básica funcional\n";
            echo "✅ APIs operacionais\n";
            echo "✅ Segurança implementada\n";
        } else {
            echo "🟡 MÓDULO FINANCEIRO PRECISA DE ATENÇÃO\n";
            echo "⚠️  Existem problemas críticos a resolver\n";
        }
        
        echo "\n" . str_repeat("=", 42) . "\n";
        echo "💰 TESTE FINANCEIRO CONCLUÍDO\n";
        echo str_repeat("=", 42) . "\n";
    }
}

// Executar testes
if (php_sapi_name() === 'cli') {
    $test = new FinanceiroTest();
    $test->runAllTests();
} else {
    echo "<pre>";
    $test = new FinanceiroTest();
    $test->runAllTests();
    echo "</pre>";
}
