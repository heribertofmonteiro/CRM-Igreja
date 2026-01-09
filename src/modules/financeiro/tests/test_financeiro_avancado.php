<?php

/**
 * Testes Avançados do Módulo Financeiro
 * 
 * Testes completos para validar funcionalidades avançadas
 * e integração com o sistema ChurchCRM
 */

class FinanceiroAdvancedTest
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
     * Executar todos os testes avançados
     */
    public function runAdvancedTests()
    {
        echo "🚀 INICIANDO TESTES AVANÇADOS FINANCEIROS\n";
        echo "============================================\n\n";
        
        // 1. Testes de Integração com ChurchCRM
        $this->testChurchCRMIntegration();
        
        // 2. Testes de Services Financeiros
        $this->testFinancialServices();
        
        // 3. Testes de Models Financeiros
        $this->testFinancialModels();
        
        // 4. Testes de Performance
        $this->testPerformance();
        
        // 5. Testes de Validação
        $this->testValidations();
        
        // 6. Testes de Relatórios
        $this->testReports();
        
        // Exibir resultados
        $this->displayResults();
    }
    
    /**
     * Testar integração com ChurchCRM
     */
    private function testChurchCRMIntegration()
    {
        echo "🔗 Testando Integração com ChurchCRM...\n";
        
        // Verificar se usa classes do ChurchCRM
        $routesFile = '/home/heriberto/projetos/PHP/Laravel/CRM/src/v2/routes/financeiro.php';
        $routesContent = file_get_contents($routesFile);
        
        $this->assert(
            strpos($routesContent, 'use ChurchCRM\\') !== false,
            "Usa classes do namespace ChurchCRM"
        );
        
        $this->assert(
            strpos($routesContent, 'SystemConfig') !== false,
            "Usa SystemConfig do ChurchCRM"
        );
        
        $this->assert(
            strpos($routesContent, 'SystemURLs') !== false,
            "Usa SystemURLs do ChurchCRM"
        );
        
        // Verificar API deposits
        $depositsFile = '/home/heriberto/projetos/PHP/Laravel/CRM/src/api/routes/finance/finance-deposits.php';
        $depositsContent = file_get_contents($depositsFile);
        
        $this->assert(
            strpos($depositsContent, 'use ChurchCRM\\') !== false,
            "API deposits usa classes ChurchCRM"
        );
        
        $this->assert(
            strpos($depositsContent, 'DepositService') !== false,
            "Usa DepositService do ChurchCRM"
        );
        
        // Verificar API payments
        $paymentsFile = '/home/heriberto/projetos/PHP/Laravel/CRM/src/api/routes/finance/finance-payments.php';
        $paymentsContent = file_get_contents($paymentsFile);
        
        $this->assert(
            strpos($paymentsContent, 'use ChurchCRM\\') !== false,
            "API payments usa classes ChurchCRM"
        );
        
        $this->assert(
            strpos($paymentsContent, 'FinancialService') !== false,
            "Usa FinancialService do ChurchCRM"
        );
        
        echo "\n";
    }
    
    /**
     * Testar services financeiros
     */
    private function testFinancialServices()
    {
        echo "⚙️ Testando Services Financeiros...\n";
        
        // Verificar se os services são referenciados
        $depositsFile = '/home/heriberto/projetos/PHP/Laravel/CRM/src/api/routes/finance/finance-deposits.php';
        $depositsContent = file_get_contents($depositsFile);
        
        $this->assert(
            strpos($depositsContent, 'DepositService') !== false,
            "DepositService referenciado"
        );
        
        $this->assert(
            strpos($depositsContent, '$this->get(\'DepositService\')') !== false,
            "DepositService injetado via container"
        );
        
        $paymentsFile = '/home/heriberto/projetos/PHP/Laravel/CRM/src/api/routes/finance/finance-payments.php';
        $paymentsContent = file_get_contents($paymentsFile);
        
        $this->assert(
            strpos($paymentsContent, 'FinancialService') !== false,
            "FinancialService referenciado"
        );
        
        $this->assert(
            strpos($paymentsContent, '$this->get(\'FinancialService\')') !== false,
            "FinancialService injetado via container"
        );
        
        // Verificar métodos dos services
        $this->assert(
            strpos($depositsContent, 'createDeposit') !== false,
            "Método createDeposit usado"
        );
        
        $this->assert(
            strpos($paymentsContent, 'getPayments') !== false,
            "Método getPayments usado"
        );
        
        $this->assert(
            strpos($paymentsContent, 'submitPledgeOrPayment') !== false,
            "Método submitPledgeOrPayment usado"
        );
        
        echo "\n";
    }
    
    /**
     * Testar models financeiros
     */
    private function testFinancialModels()
    {
        echo "📊 Testando Models Financeiros...\n";
        
        // Verificar se usa models do ChurchCRM
        $depositsFile = '/home/heriberto/projetos/PHP/Laravel/CRM/src/api/routes/finance/finance-deposits.php';
        $depositsContent = file_get_contents($depositsFile);
        
        $this->assert(
            strpos($depositsContent, 'Deposit') !== false,
            "Model Deposit usado"
        );
        
        $this->assert(
            strpos($depositsContent, 'DepositQuery') !== false,
            "Model DepositQuery usado"
        );
        
        $paymentsFile = '/home/heriberto/projetos/PHP/Laravel/CRM/src/api/routes/finance/finance-payments.php';
        $paymentsContent = file_get_contents($paymentsFile);
        
        $this->assert(
            strpos($paymentsContent, 'PledgeQuery') !== false,
            "Model PledgeQuery usado"
        );
        
        // Verificar métodos dos models
        $this->assert(
            strpos($depositsContent, '->toArray()') !== false,
            "Método toArray() usado"
        );
        
        $this->assert(
            strpos($depositsContent, '->find()') !== false,
            "Método find() usado"
        );
        
        $this->assert(
            strpos($paymentsContent, '->filterByFamId()') !== false,
            "Método filterByFamId() usado"
        );
        
        $this->assert(
            strpos($paymentsContent, '->joinWithDonationFund()') !== false,
            "Método joinWithDonationFund() usado"
        );
        
        echo "\n";
    }
    
    /**
     * Testar performance financeira
     */
    private function testPerformance()
    {
        echo "⚡ Testando Performance Financeira...\n";
        
        // Testar performance de query simples
        $startTime = microtime(true);
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM payment_methods");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        $queryTime = microtime(true) - $startTime;
        
        $this->assert(
            $queryTime < 0.1,
            "Query payment_methods em tempo aceitável (" . round($queryTime * 1000, 2) . "ms)"
        );
        
        // Testar performance de query com JOIN
        $startTime = microtime(true);
        $stmt = $this->pdo->query("
            SELECT pm.*, COUNT(op.id) as usage_count 
            FROM payment_methods pm 
            LEFT JOIN order_payments op ON pm.id = op.payment_method_id 
            GROUP BY pm.id 
            LIMIT 10
        ");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $joinTime = microtime(true) - $startTime;
        
        $this->assert(
            $joinTime < 0.2,
            "Query com JOIN em tempo aceitável (" . round($joinTime * 1000, 2) . "ms)"
        );
        
        // Verificar índices
        $stmt = $this->pdo->query("SHOW INDEX FROM payment_methods");
        $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assert(
            count($indexes) > 0,
            "Tabela payment_methods possui índices (" . count($indexes) . ")"
        );
        
        echo "\n";
    }
    
    /**
     * Testar validações financeiras
     */
    private function testValidations()
    {
        echo "✅ Testando Validações Financeiras...\n";
        
        // Verificar validações na API de deposits
        $depositsFile = '/home/heriberto/projetos/PHP/Laravel/CRM/src/api/routes/finance/finance-deposits.php';
        $depositsContent = file_get_contents($depositsFile);
        
        $this->assert(
            strpos($depositsContent, 'InputUtils::filterString') !== false,
            "Validação de string com InputUtils"
        );
        
        $this->assert(
            strpos($depositsContent, 'in_array') !== false,
            "Validação de valores permitidos"
        );
        
        $this->assert(
            strpos($depositsContent, 'allowedTypes') !== false,
            "Lista de tipos permitidos definida"
        );
        
        $this->assert(
            strpos($depositsContent, '$response->withStatus(400)') !== false,
            "Retorno de erro 400 implementado"
        );
        
        // Verificar validações na API de payments
        $paymentsFile = '/home/heriberto/projetos/PHP/Laravel/CRM/src/api/routes/finance/finance-payments.php';
        $paymentsContent = file_get_contents($paymentsFile);
        
        $this->assert(
            strpos($paymentsContent, 'AuthenticationManager::getCurrentUser()') !== false,
            "Verificação de usuário autenticado"
        );
        
        $this->assert(
            strpos($paymentsContent, 'getShowSince()') !== false,
            "Validação de período de exibição"
        );
        
        $this->assert(
            strpos($paymentsContent, 'isShowPayments()') !== false,
            "Validação de permissão de pagamentos"
        );
        
        $this->assert(
            strpos($paymentsContent, 'isShowPledges()') !== false,
            "Validação de permissão de promessas"
        );
        
        echo "\n";
    }
    
    /**
     * Testar relatórios financeiros
     */
    private function testReports()
    {
        echo "📈 Testando Relatórios Financeiros...\n";
        
        // Verificar se há endpoints de relatórios
        $depositsFile = '/home/heriberto/projetos/PHP/Laravel/CRM/src/api/routes/finance/finance-deposits.php';
        $depositsContent = file_get_contents($depositsFile);
        
        $this->assert(
            strpos($depositsContent, '/dashboard') !== false,
            "Endpoint de dashboard de deposits existe"
        );
        
        $this->assert(
            strpos($depositsContent, 'date(\'Y-m-d\', strtotime(\'-90 days\'))') !== false,
            "Filtro de período implementado"
        );
        
        // Verificar se há Cypress tests para relatórios
        $cypressReportsFile = '/home/heriberto/projetos/PHP/Laravel/CRM/cypress/e2e/ui/finance/finance.reports.spec.js';
        $this->assert(
            file_exists($cypressReportsFile),
            "Teste Cypress para relatórios financeiros existe"
        );
        
        // Verificar se há Cypress tests para deposits
        $cypressDepositsFile = '/home/heriberto/projetos/PHP/Laravel/CRM/cypress/e2e/ui/finance/finance.deposits.spec.js';
        $this->assert(
            file_exists($cypressDepositsFile),
            "Teste Cypress para depósitos financeiros existe"
        );
        
        // Verificar se há Cypress tests para family finance
        $cypressFamilyFile = '/home/heriberto/projetos/PHP/Laravel/CRM/cypress/e2e/ui/finance/finance.family.spec.js';
        $this->assert(
            file_exists($cypressFamilyFile),
            "Teste Cypress para finanças da família existe"
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
        echo "============================================\n";
        echo "📊 RESULTADOS DOS TESTES AVANÇADOS\n";
        echo "============================================\n";
        
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
            echo "🎉 Todos os testes avançados passaram!\n";
            echo "💰 O módulo financeiro tem alta qualidade!\n";
        } else {
            echo "⚠️  Alguns testes avançados falharam.\n";
            echo "📝 Verifique os erros acima.\n";
        }
        
        // Análise final
        echo "\n🎯 ANÁLISE FINAL AVANÇADA:\n";
        
        $score = ($passedCount / $total) * 100;
        
        if ($score >= 95) {
            echo "🟢 MÓDULO FINANCEIRO EXCELENTE\n";
            echo "🏆 Qualidade enterprise-level\n";
            echo "🚀 Pronto para produção avançada\n";
        } elseif ($score >= 80) {
            echo "🟡 MÓDULO FINANCEIRO BOM\n";
            echo "✅ Funcionalidades principais ok\n";
            echo "📝 Pequenos ajustes recomendados\n";
        } else {
            echo "🔴 MÓDULO FINANCEIRO PRECISA MELHORIAS\n";
            echo "⚠️  Revisão completa necessária\n";
        }
        
        echo "\n" . str_repeat("=", 45) . "\n";
        echo "💰 TESTES AVANÇADOS CONCLUÍDOS\n";
        echo str_repeat("=", 45) . "\n";
    }
}

// Executar testes
if (php_sapi_name() === 'cli') {
    $test = new FinanceiroAdvancedTest();
    $test->runAdvancedTests();
} else {
    echo "<pre>";
    $test = new FinanceiroAdvancedTest();
    $test->runAdvancedTests();
    echo "</pre>";
}
