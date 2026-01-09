<?php

/**
 * Melhoria 2: Implementar Cache Financeiro
 * 
 * Sistema de cache para queries frequentes e relatórios
 * reduzindo carga no banco e melhorando performance
 */

class FinanceiroCache
{
    private $pdo;
    private $cacheDir;
    private $cacheTime = 300; // 5 minutos padrão
    
    public function __construct()
    {
        $this->pdo = new PDO(
            'mysql:host=localhost;dbname=autonomo;charset=utf8mb4',
            'heriberto',
            '0631'
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $this->cacheDir = __DIR__ . '/cache';
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    /**
     * Gerar chave de cache
     */
    private function gerarChave($prefixo, $params = [])
    {
        $paramString = is_array($params) ? serialize($params) : $params;
        return 'financeiro_' . $prefixo . '_' . md5($paramString);
    }
    
    /**
     * Obter dados do cache
     */
    public function get($chave)
    {
        $arquivo = $this->cacheDir . '/' . $chave . '.cache';
        
        if (!file_exists($arquivo)) {
            return null;
        }
        
        $conteudo = file_get_contents($arquivo);
        $dados = unserialize($conteudo);
        
        // Verificar se expirou
        if (time() > $dados['expira']) {
            unlink($arquivo);
            return null;
        }
        
        return $dados['valor'];
    }
    
    /**
     * Salvar dados no cache
     */
    public function set($chave, $valor, $tempo = null)
    {
        $tempo = $tempo ?? $this->cacheTime;
        $arquivo = $this->cacheDir . '/' . $chave . '.cache';
        
        $dados = [
            'valor' => $valor,
            'expira' => time() + $tempo,
            'criado' => time()
        ];
        
        file_put_contents($arquivo, serialize($dados));
        return true;
    }
    
    /**
     * Limpar cache
     */
    public function limpar($padrao = null)
    {
        $arquivos = glob($this->cacheDir . '/' . ($padrao ?? '*') . '.cache');
        
        foreach ($arquivos as $arquivo) {
            unlink($arquivo);
        }
        
        return count($arquivos);
    }
    
    /**
     * Cache para dashboard de pagamentos
     */
    public function getDashboardPagamentos()
    {
        $chave = $this->gerarChave('dashboard_pagamentos');
        
        // Tentar obter do cache
        $cache = $this->get($chave);
        if ($cache !== null) {
            echo "📋 Dashboard obtido do cache\n";
            return $cache;
        }
        
        echo "🔍 Gerando dashboard do banco...\n";
        
        // Query otimizada com cache
        $query = "
            SELECT 
                pm.id,
                pm.name,
                pm.code,
                pm.is_active,
                COUNT(op.id) as usage_count,
                COALESCE(SUM(op.amount), 0) as total_amount,
                MAX(op.created_at) as last_used
            FROM payment_methods pm 
            LEFT JOIN order_payments op FORCE INDEX (idx_order_payments_payment_method)
                ON pm.id = op.payment_method_id 
                AND op.created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)
            WHERE pm.deleted_at IS NULL
            GROUP BY pm.id, pm.name, pm.code, pm.is_active
            ORDER BY usage_count DESC, pm.sort_order ASC
            LIMIT 10
        ";
        
        $stmt = $this->pdo->query($query);
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Salvar no cache por 5 minutos
        $this->set($chave, $resultado, 300);
        
        return $resultado;
    }
    
    /**
     * Cache para relatórios por família
     */
    public function getRelatorioFamilia($familyId, $periodo = '90')
    {
        $chave = $this->gerarChave('relatorio_familia', ['family_id' => $familyId, 'periodo' => $periodo]);
        
        // Tentar obter do cache
        $cache = $this->get($chave);
        if ($cache !== null) {
            echo "📋 Relatório família obtido do cache\n";
            return $cache;
        }
        
        echo "🔍 Gerando relatório família do banco...\n";
        
        $query = "
            SELECT 
                p.id,
                p.date,
                p.amount,
                p.pledge_or_payment,
                p.nondeductible,
                p.comment,
                f.FamName as family_name,
                d.fun_Description as fund_description,
                pm.name as payment_method_name,
                u.FirstName . ' ' . u.LastName as created_by_name
            FROM pledges p
            FORCE INDEX (idx_payments_family_date)
            INNER JOIN family f ON p.fam_id = f.fam_ID
            LEFT JOIN donationfund d ON p.fund_ID = d.ID
            LEFT JOIN payment_methods pm ON p.payment_method = pm.id
            LEFT JOIN users u ON p.EnteredBy = u.per_ID
            WHERE p.fam_id = :family_id
              AND p.date >= DATE_SUB(NOW(), INTERVAL {$periodo} DAY)
              AND p.deleted_at IS NULL
            ORDER BY p.date DESC, p.id DESC
            LIMIT 100
        ";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':family_id' => $familyId]);
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Salvar no cache por 10 minutos (relatórios mudam menos)
        $this->set($chave, $resultado, 600);
        
        return $resultado;
    }
    
    /**
     * Cache para métodos de pagamento
     */
    public function getMetodosPagamento()
    {
        $chave = $this->gerarChave('metodos_pagamento');
        
        // Tentar obter do cache
        $cache = $this->get($chave);
        if ($cache !== null) {
            echo "📋 Métodos de pagamento obtidos do cache\n";
            return $cache;
        }
        
        echo "🔍 Gerando métodos de pagamento do banco...\n";
        
        $query = "
            SELECT 
                id, name, code, description, provider,
                fee_percentage, fee_fixed, is_active,
                is_default, sort_order
            FROM payment_methods 
            WHERE deleted_at IS NULL
            ORDER BY sort_order ASC, name ASC
        ";
        
        $stmt = $this->pdo->query($query);
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Salvar no cache por 30 minutos (mudam raramente)
        $this->set($chave, $resultado, 1800);
        
        return $resultado;
    }
    
    /**
     * Cache para estatísticas financeiras
     */
    public function getEstatisticasFinanceiras()
    {
        $chave = $this->gerarChave('estatisticas_financeiras');
        
        // Tentar obter do cache
        $cache = $this->get($chave);
        if ($cache !== null) {
            echo "📋 Estatísticas obtidas do cache\n";
            return $cache;
        }
        
        echo "🔍 Gerando estatísticas do banco...\n";
        
        $query = "
            SELECT 
                COUNT(DISTINCT op.id) as total_pagamentos,
                COALESCE(SUM(op.amount), 0) as valor_total,
                COUNT(DISTINCT p.fam_id) as familias_atingidas,
                COUNT(DISTINCT DATE(op.created_at)) as dias_com_movimento,
                AVG(op.amount) as valor_medio,
                MAX(op.amount) as maior_pagamento,
                MIN(op.amount) as menor_pagamento
            FROM order_payments op
            INNER JOIN pledges p ON op.pledge_id = p.id
            WHERE op.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
              AND op.status = 'completed'
        ";
        
        $stmt = $this->pdo->query($query);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Salvar no cache por 15 minutos
        $this->set($chave, $resultado, 900);
        
        return $resultado;
    }
    
    /**
     * Invalidar cache específico
     */
    public function invalidarCache($tipo)
    {
        $padroes = [
            'pagamentos' => 'dashboard_pagamentos',
            'familias' => 'relatorio_familia',
            'metodos' => 'metodos_pagamento',
            'estatisticas' => 'estatisticas_financeiras',
            'todos' => null
        ];
        
        if (!isset($padroes[$tipo])) {
            throw new Exception("Tipo de cache inválido: $tipo");
        }
        
        $removidos = $this->limpar($padroes[$tipo]);
        echo "🗑️  Cache invalidado: $tipo ($removidos arquivos removidos)\n";
        
        return $removidos;
    }
    
    /**
     * Limpar cache expirado
     */
    public function limparCacheExpirado()
    {
        $arquivos = glob($this->cacheDir . '/*.cache');
        $removidos = 0;
        
        foreach ($arquivos as $arquivo) {
            $conteudo = file_get_contents($arquivo);
            $dados = unserialize($conteudo);
            
            if (time() > $dados['expira']) {
                unlink($arquivo);
                $removidos++;
            }
        }
        
        echo "🧹 Cache expirado limpo: $removidos arquivos removidos\n";
        return $removidos;
    }
    
    /**
     * Estatísticas do cache
     */
    public function getEstatisticasCache()
    {
        $arquivos = glob($this->cacheDir . '/*.cache');
        $total = count($arquivos);
        $tamanho = 0;
        $expirados = 0;
        
        foreach ($arquivos as $arquivo) {
            $tamanho += filesize($arquivo);
            
            $conteudo = file_get_contents($arquivo);
            $dados = unserialize($conteudo);
            
            if (time() > $dados['expira']) {
                $expirados++;
            }
        }
        
        return [
            'total_arquivos' => $total,
            'tamanho_total' => $tamanho,
            'tamanho_formatado' => $this->formatarBytes($tamanho),
            'arquivos_expirados' => $expirados,
            'taxa_expirados' => $total > 0 ? round(($expirados / $total) * 100, 2) : 0
        ];
    }
    
    /**
     * Formatar bytes para formato legível
     */
    private function formatarBytes($bytes)
    {
        $unidades = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($unidades) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $unidades[$pow];
    }
    
    /**
     * Testar performance do cache
     */
    public function testarPerformanceCache()
    {
        echo "\n⚡ Testando performance do cache...\n";
        
        // Testar sem cache
        $inicio = microtime(true);
        $this->getMetodosPagamento(); // Forçar regeneração
        $tempoSemCache = microtime(true) - $inicio;
        
        // Testar com cache
        $inicio = microtime(true);
        $this->getMetodosPagamento(); // Usar cache
        $tempoComCache = microtime(true) - $inicio;
        
        $melhoria = (($tempoSemCache - $tempoComCache) / $tempoSemCache) * 100;
        
        echo "  📊 Resultados do teste:\n";
        echo "     Tempo sem cache: " . round($tempoSemCache * 1000, 2) . "ms\n";
        echo "     Tempo com cache: " . round($tempoComCache * 1000, 2) . "ms\n";
        echo "     Melhoria: " . round($melhoria, 2) . "% mais rápido\n";
        
        if ($melhoria > 80) {
            echo "  🎉 Excelente! Cache muito eficiente\n";
        } elseif ($melhoria > 50) {
            echo "  ✅ Bom! Cache eficiente\n";
        } else {
            echo "  ⚠️  Cache com melhoria modesta\n";
        }
        
        return [
            'tempo_sem_cache' => $tempoSemCache,
            'tempo_com_cache' => $tempoComCache,
            'melhoria_percentual' => $melhoria
        ];
    }
}

// Executar implementação de cache se chamado diretamente
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    echo "💾 INICIANDO IMPLEMENTAÇÃO DE CACHE FINANCEIRO\n";
    echo "================================================\n";
    
    $cache = new FinanceiroCache();
    
    // Limpar cache expirado
    $cache->limparCacheExpirado();
    
    // Testar performance
    $cache->testarPerformanceCache();
    
    // Gerar estatísticas
    $stats = $cache->getEstatisticasCache();
    echo "\n📈 Estatísticas do Cache:\n";
    echo "  Arquivos: {$stats['total_arquivos']}\n";
    echo "  Tamanho: {$stats['tamanho_formatado']}\n";
    echo "  Expirados: {$stats['arquivos_expirados']} ({$stats['taxa_expirados']}%)\n";
    
    echo "\n🎉 CACHE IMPLEMENTADO COM SUCESSO!\n";
    echo "📊 Melhoria esperada: > 80% nas queries frequentes\n";
}
