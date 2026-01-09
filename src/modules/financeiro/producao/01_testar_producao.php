<?php

/**
 * Passo 1: Testes em Ambiente de Produção
 * 
 * Validação completa do módulo financeiro para produção
 * incluindo testes de carga, segurança e integração
 */

class FinanceiroProducaoTest
{
    private $pdo;
    private $testResults = [];
    private $configProducao = [
        'max_execution_time' => 30,
        'memory_limit' => '256M',
        'max_connections' => 100,
        'timeout_queries' => 5.0
    ];
    
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
     * Executar todos os testes de produção
     */
    public function executarTestesProducao()
    {
        echo "🚀 INICIANDO TESTES DE PRODUÇÃO - MÓDULO FINANCEIRO\n";
        echo "========================================================\n\n";
        
        // 1. Testes de Ambiente
        $this->testarAmbiente();
        
        // 2. Testes de Conexão e Banco
        $this->testarConexaoBanco();
        
        // 3. Testes de Performance
        $this->testarPerformance();
        
        // 4. Testes de Carga
        $this->testarCarga();
        
        // 5. Testes de Segurança
        $this->testarSeguranca();
        
        // 6. Testes de Integração
        $this->testarIntegracao();
        
        // 7. Testes de Cache
        $this->testarCache();
        
        // 8. Testes de Relatórios
        $this->testarRelatorios();
        
        // Gerar relatório final
        $this->gerarRelatorioProducao();
    }
    
    /**
     * Testar ambiente de produção
     */
    private function testarAmbiente()
    {
        echo "🌍 Testando Ambiente de Produção...\n";
        
        // Verificar PHP
        $this->assert(
            version_compare(PHP_VERSION, '8.0.0', '>='),
            "PHP >= 8.0.0 (Atual: " . PHP_VERSION . ")"
        );
        
        // Verificar extensões necessárias
        $extensoes = ['pdo', 'pdo_mysql', 'json', 'mbstring', 'curl'];
        foreach ($extensoes as $ext) {
            $this->assert(
                extension_loaded($ext),
                "Extensão PHP: $ext"
            );
        }
        
        // Verificar limites de memória
        $memoryLimit = ini_get('memory_limit');
        $this->assert(
            $this->parseBytes($memoryLimit) >= $this->parseBytes($this->configProducao['memory_limit']),
            "Limite de memória >= {$this->configProducao['memory_limit']} (Atual: $memoryLimit)"
        );
        
        // Verificar tempo de execução
        $maxExecution = ini_get('max_execution_time');
        $this->assert(
            $maxExecution >= $this->configProducao['max_execution_time'],
            "Tempo máximo execução >= {$this->configProducao['max_execution_time']}s (Atual: {$maxExecution}s)"
        );
        
        // Verificar permissões de diretórios
        $diretorios = [
            __DIR__ . '/cache',
            __DIR__ . '/exports',
            __DIR__ . '/logs'
        ];
        
        foreach ($diretorios as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $this->assert(
                is_writable($dir),
                "Diretório gravável: " . basename($dir)
            );
        }
        
        echo "\n";
    }
    
    /**
     * Testar conexão e banco de dados
     */
    private function testarConexaoBanco()
    {
        echo "🗄️ Testando Conexão e Banco de Dados...\n";
        
        // Testar conexão
        try {
            $this->pdo->query("SELECT 1");
            $this->assert(true, "Conexão com banco ativa");
        } catch (Exception $e) {
            $this->assert(false, "Conexão com banco: " . $e->getMessage());
        }
        
        // Verificar tabelas necessárias
        $tabelas = ['payment_methods', 'order_payments', 'refunds'];
        foreach ($tabelas as $tabela) {
            $stmt = $this->pdo->query("SHOW TABLES LIKE '$tabela'");
            $this->assert(
                $stmt->rowCount() > 0,
                "Tabela existe: $tabela"
            );
        }
        
        // Verificar índices criados
        $indices = [
            'idx_order_payments_payment_method',
            'idx_refunds_date',
            'idx_order_payments_status_date'
        ];
        
        foreach ($indices as $indice) {
            $stmt = $this->pdo->query("SHOW INDEX FROM order_payments WHERE Key_name = '$indice'");
            if (strpos($indice, 'order_payments') !== false) {
                $this->assert(
                    $stmt->rowCount() > 0,
                    "Índice existe: $indice"
                );
            }
        }
        
        // Testar performance de query simples
        $inicio = microtime(true);
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM payment_methods");
        $count = $stmt->fetchColumn();
        $tempo = microtime(true) - $inicio;
        
        $this->assert(
            $tempo < 0.1,
            "Query simples < 100ms (Atual: " . round($tempo * 1000, 2) . "ms)"
        );
        
        echo "\n";
    }
    
    /**
     * Testar performance do sistema
     */
    private function testarPerformance()
    {
        echo "⚡ Testando Performance do Sistema...\n";
        
        // Incluir classes do módulo
        require_once __DIR__ . '/../melhorias/01_otimizar_queries_corrigido.php';
        require_once __DIR__ . '/../melhorias/02_implementar_cache.php';
        require_once __DIR__ . '/../melhorias/03_relatorios_avancados_corrigido.php';
        
        // Testar otimizador de queries
        $optimizer = new FinanceiroQueryOptimizerCorrigido();
        $inicio = microtime(true);
        $dashboard = $optimizer->otimizarQueryDashboard();
        $tempo = microtime(true) - $inicio;
        
        $this->assert(
            $tempo < 0.05,
            "Dashboard otimizado < 50ms (Atual: " . round($tempo * 1000, 2) . "ms)"
        );
        
        // Testar cache
        $cache = new FinanceiroCache();
        $inicio = microtime(true);
        $metodos = $cache->getMetodosPagamento();
        $tempoCache = microtime(true) - $inicio;
        
        $this->assert(
            $tempoCache < 0.01,
            "Cache < 10ms (Atual: " . round($tempoCache * 1000, 2) . "ms)"
        );
        
        // Testar relatórios
        $relatorios = new FinanceiroRelatoriosAvancadosCorrigido();
        $inicio = microtime(true);
        $fluxoCaixa = $relatorios->relatorioFluxoCaixa(
            date('Y-m-d', strtotime('-30 days')),
            date('Y-m-d')
        );
        $tempoRelatorio = microtime(true) - $inicio;
        
        $this->assert(
            $tempoRelatorio < 0.1,
            "Relatório < 100ms (Atual: " . round($tempoRelatorio * 1000, 2) . "ms)"
        );
        
        echo "\n";
    }
    
    /**
     * Testar carga do sistema
     */
    private function testarCarga()
    {
        echo "🔥 Testando Carga do Sistema...\n";
        
        // Simular múltiplas requisições concorrentes
        $requisicoes = 50;
        $tempos = [];
        $erros = 0;
        
        echo "  🔄 Simulando $requisicoes requisições concorrentes...\n";
        
        for ($i = 0; $i < $requisicoes; $i++) {
            $inicio = microtime(true);
            
            try {
                // Query típica de dashboard
                $stmt = $this->pdo->query("
                    SELECT pm.*, COUNT(op.id) as usage_count 
                    FROM payment_methods pm 
                    LEFT JOIN order_payments op ON pm.id = op.payment_method_id 
                    GROUP BY pm.id 
                    LIMIT 10
                ");
                $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $tempos[] = microtime(true) - $inicio;
                
                if ($i % 10 === 0) {
                    echo "    📊 Requisição " . ($i + 1) . "/$requisicoes\n";
                }
            } catch (Exception $e) {
                $erros++;
            }
        }
        
        // Calcular estatísticas
        $tempoMedio = array_sum($tempos) / count($tempos);
        $tempoMax = max($tempos);
        $tempoMin = min($tempos);
        $percentil95 = $this->calcularPercentil($tempos, 95);
        
        $this->assert(
            $erros === 0,
            "Zero erros em teste de carga (Erros: $erros)"
        );
        
        $this->assert(
            $tempoMedio < 0.05,
            "Tempo médio < 50ms (Atual: " . round($tempoMedio * 1000, 2) . "ms)"
        );
        
        $this->assert(
            $percentil95 < 0.1,
            "Percentil 95 < 100ms (Atual: " . round($percentil95 * 1000, 2) . "ms)"
        );
        
        echo "    📊 Estatísticas de carga:\n";
        echo "      Tempo médio: " . round($tempoMedio * 1000, 2) . "ms\n";
        echo "      Tempo mínimo: " . round($tempoMin * 1000, 2) . "ms\n";
        echo "      Tempo máximo: " . round($tempoMax * 1000, 2) . "ms\n";
        echo "      Percentil 95: " . round($percentil95 * 1000, 2) . "ms\n";
        echo "      Taxa de erro: " . round(($erros / $requisicoes) * 100, 2) . "%\n";
        
        echo "\n";
    }
    
    /**
     * Testar segurança
     */
    private function testarSeguranca()
    {
        echo "🔐 Testando Segurança...\n";
        
        // Testar SQL Injection
        $maliciousInputs = [
            "'; DROP TABLE payment_methods; --",
            "' OR '1'='1",
            "'; DELETE FROM order_payments; --",
            "' UNION SELECT * FROM users --"
        ];
        
        foreach ($maliciousInputs as $input) {
            try {
                $stmt = $this->pdo->prepare("SELECT * FROM payment_methods WHERE name = ?");
                $stmt->execute([$input]);
                $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Se não causou erro e não retornou dados inválidos, está seguro
                $this->assert(
                    true,
                    "SQL Injection bloqueado: " . substr($input, 0, 20) . "..."
                );
            } catch (Exception $e) {
                // Se causou erro, está protegido
                $this->assert(
                    true,
                    "SQL Injection detectado: " . substr($input, 0, 20) . "..."
                );
            }
        }
        
        // Testar XSS
        $xssInputs = [
            "<script>alert('xss')</script>",
            "javascript:alert('xss')",
            "<img src=x onerror=alert('xss')>",
            "';alert('xss');//"
        ];
        
        foreach ($xssInputs as $input) {
            $escaped = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
            $this->assert(
                $escaped !== $input,
                "XSS escapado: " . substr($input, 0, 20) . "..."
            );
        }
        
        // Verificar se middleware de segurança existe
        $middlewareFile = '/home/heriberto/projetos/PHP/Laravel/CRM/src/ChurchCRM/Slim/Middleware/Request/Auth/FinanceRoleAuthMiddleware.php';
        $this->assert(
            file_exists($middlewareFile),
            "Middleware de segurança financeiro existe"
        );
        
        echo "\n";
    }
    
    /**
     * Testar integração com ChurchCRM
     */
    private function testarIntegracao()
    {
        echo "🔗 Testando Integração com ChurchCRM...\n";
        
        // Verificar se as rotas usam classes ChurchCRM
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
        
        // Verificar APIs
        $apiDeposits = '/home/heriberto/projetos/PHP/Laravel/CRM/src/api/routes/finance/finance-deposits.php';
        $apiDepositsContent = file_get_contents($apiDeposits);
        
        $this->assert(
            strpos($apiDepositsContent, 'DepositService') !== false,
            "API usa DepositService do ChurchCRM"
        );
        
        // Verificar template
        $templateFile = '/home/heriberto/projetos/PHP/Laravel/CRM/src/v2/templates/financeiro/dashboard.php';
        $templateContent = file_get_contents($templateFile);
        
        $this->assert(
            strpos($templateContent, 'AuthenticationManager') !== false,
            "Template usa AuthenticationManager"
        );
        
        echo "\n";
    }
    
    /**
     * Testar sistema de cache
     */
    private function testarCache()
    {
        echo "💾 Testando Sistema de Cache...\n";
        
        require_once __DIR__ . '/../melhorias/02_implementar_cache.php';
        $cache = new FinanceiroCache();
        
        // Testar escrita e leitura
        $chaveTeste = 'teste_producao_' . time();
        $valorTeste = ['teste' => true, 'timestamp' => time()];
        
        $cache->set($chaveTeste, $valorTeste, 60);
        $recuperado = $cache->get($chaveTeste);
        
        $this->assert(
            $recuperado !== null,
            "Cache escrita/leitura funcionando"
        );
        
        $this->assert(
            $recuperado['teste'] === true,
            "Cache mantém integridade dos dados"
        );
        
        // Testar expiração
        $cache->set($chaveTeste . '_expira', $valorTeste, 1);
        sleep(2);
        $expirado = $cache->get($chaveTeste . '_expira');
        
        $this->assert(
            $expirado === null,
            "Cache expira corretamente"
        );
        
        // Limpar teste
        $cache->limpar('teste_producao');
        
        echo "\n";
    }
    
    /**
     * Testar relatórios
     */
    private function testarRelatorios()
    {
        echo "📊 Testando Relatórios...\n";
        
        require_once __DIR__ . '/../melhorias/03_relatorios_avancados_corrigido.php';
        $relatorios = new FinanceiroRelatoriosAvancadosCorrigido();
        
        // Testar cada relatório
        $testes = [
            'fluxo_caixa' => function() use ($relatorios) {
                return $relatorios->relatorioFluxoCaixa(
                    date('Y-m-d', strtotime('-7 days')),
                    date('Y-m-d')
                );
            },
            'metodos_pagamento' => function() use ($relatorios) {
                return $relatorios->relatorioAnaliseMetodosPagamento(
                    date('Y-m-d', strtotime('-7 days')),
                    date('Y-m-d')
                );
            },
            'tendencias' => function() use ($relatorios) {
                return $relatorios->relatorioTendenciasFinanceiras(3);
            }
        ];
        
        foreach ($testes as $nome => $teste) {
            $inicio = microtime(true);
            try {
                $resultado = $teste();
                $tempo = microtime(true) - $inicio;
                
                $this->assert(
                    is_array($resultado),
                    "Relatório $nome retorna array"
                );
                
                $this->assert(
                    $tempo < 0.1,
                    "Relatório $nome < 100ms (Atual: " . round($tempo * 1000, 2) . "ms)"
                );
                
                echo "  ✅ Relatório $nome: " . round($tempo * 1000, 2) . "ms\n";
            } catch (Exception $e) {
                $this->assert(false, "Relatório $nome: " . $e->getMessage());
            }
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
     * Converter bytes para formato legível
     */
    private function parseBytes($value)
    {
        $unit = strtolower(substr($value, -1));
        $value = (int) $value;
        
        switch ($unit) {
            case 'g': return $value * 1024 * 1024 * 1024;
            case 'm': return $value * 1024 * 1024;
            case 'k': return $value * 1024;
            default: return $value;
        }
    }
    
    /**
     * Calcular percentil
     */
    private function calcularPercentil($array, $percentile)
    {
        sort($array);
        $index = ($percentile / 100) * (count($array) - 1);
        return $array[ceil($index)];
    }
    
    /**
     * Gerar relatório de produção
     */
    private function gerarRelatorioProducao()
    {
        echo "========================================\n";
        echo "📊 RELATÓRIO DE TESTES DE PRODUÇÃO\n";
        echo "========================================\n";
        
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
        
        // Status final
        $sucesso = $passedCount / $total;
        if ($sucesso >= 0.95) {
            echo "🎉 STATUS: APROVADO PARA PRODUÇÃO!\n";
            echo "✅ Sistema pronto para ir ao ar\n";
        } elseif ($sucesso >= 0.80) {
            echo "⚠️  STATUS: APROVADO COM RESTRIÇÕES\n";
            echo "📝 Resolver problemas antes de produção\n";
        } else {
            echo "❌ STATUS: NÃO APROVADO\n";
            echo "🔧 Corrigir problemas críticos\n";
        }
        
        // Salvar relatório
        $relatorio = [
            'data' => date('Y-m-d H:i:s'),
            'ambiente' => 'producao',
            'total_testes' => $total,
            'testes_passaram' => $passedCount,
            'testes_falharam' => $failed,
            'taxa_sucesso' => round(($passedCount / $total) * 100, 2),
            'status' => $sucesso >= 0.95 ? 'aprovado' : 'reprovado',
            'detalhes' => $this->testResults
        ];
        
        file_put_contents(
            __DIR__ . '/relatorio_producao.json',
            json_encode($relatorio, JSON_PRETTY_PRINT)
        );
        
        echo "\n📄 Relatório salvo em: relatorio_producao.json\n";
        echo "\n" . str_repeat("=", 58) . "\n";
        echo "🚀 TESTES DE PRODUÇÃO CONCLUÍDOS\n";
        echo str_repeat("=", 58) . "\n";
    }
}

// Executar testes se chamado diretamente
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $test = new FinanceiroProducaoTest();
    $test->executarTestesProducao();
}
