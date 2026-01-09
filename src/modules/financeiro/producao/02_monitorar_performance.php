<?php

/**
 * Passo 2: Monitoramento de Performance em Tempo Real
 * 
 * Sistema de monitoramento contínuo para identificar
 * problemas de performance e otimizar proativamente
 */

class FinanceiroPerformanceMonitor
{
    private $pdo;
    private $logFile;
    private $metrics = [];
    private $alertas = [];
    
    public function __construct()
    {
        $this->pdo = new PDO(
            'mysql:host=localhost;dbname=autonomo;charset=utf8mb4',
            'heriberto',
            '0631'
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $this->logFile = __DIR__ . '/logs/performance.log';
        $this->criarDiretorioLogs();
    }
    
    /**
     * Iniciar monitoramento contínuo
     */
    public function iniciarMonitoramento()
    {
        echo "📊 INICIANDO MONITORAMENTO DE PERFORMANCE\n";
        echo "==========================================\n\n";
        
        // Coletar métricas atuais
        $this->coletarMetricasBanco();
        $this->coletarMetricasSistema();
        $this->coletarMetricasAplicacao();
        
        // Analisar performance
        $this->analisarPerformance();
        
        // Gerar alertas
        $this->gerarAlertas();
        
        // Salvar métricas
        $this->salvarMetricas();
        
        // Exibir dashboard
        $this->exibirDashboard();
    }
    
    /**
     * Coletar métricas do banco de dados
     */
    private function coletarMetricasBanco()
    {
        echo "🗄️ Coletando métricas do banco de dados...\n";
        
        // Performance de queries
        $queries = [
            'dashboard' => "
                SELECT pm.*, COUNT(op.id) as usage_count 
                FROM payment_methods pm 
                LEFT JOIN order_payments op ON pm.id = op.payment_method_id 
                GROUP BY pm.id 
                LIMIT 10
            ",
            'relatorio' => "
                SELECT DATE(op.created_at) as data, COUNT(*) as total
                FROM order_payments op
                WHERE op.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                GROUP BY DATE(op.created_at)
            ",
            'metodos' => "
                SELECT * FROM payment_methods 
                WHERE deleted_at IS NULL
                ORDER BY sort_order ASC
            "
        ];
        
        foreach ($queries as $nome => $sql) {
            $tempos = [];
            
            // Executar query 5 vezes para média
            for ($i = 0; $i < 5; $i++) {
                $inicio = microtime(true);
                $stmt = $this->pdo->query($sql);
                $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $tempos[] = microtime(true) - $inicio;
            }
            
            $tempoMedio = array_sum($tempos) / count($tempos);
            $tempoMax = max($tempos);
            $tempoMin = min($tempos);
            
            $this->metrics['banco'][$nome] = [
                'tempo_medio' => $tempoMedio,
                'tempo_max' => $tempoMax,
                'tempo_min' => $tempoMin,
                'registros' => count($resultado),
                'execucoes' => 5
            ];
            
            echo "  📊 Query $nome: " . round($tempoMedio * 1000, 2) . "ms (média)\n";
        }
        
        // Status do MySQL
        $stmt = $this->pdo->query("SHOW STATUS LIKE 'Connections'");
        $connections = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $stmt = $this->pdo->query("SHOW STATUS LIKE 'Slow_queries'");
        $slowQueries = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->metrics['banco']['status'] = [
            'connections' => (int) $connections['Value'],
            'slow_queries' => (int) $slowQueries['Value']
        ];
        
        echo "  🔗 Conexões ativas: {$this->metrics['banco']['status']['connections']}\n";
        echo "  ⚠️  Queries lentas: {$this->metrics['banco']['status']['slow_queries']}\n";
        
        echo "\n";
    }
    
    /**
     * Coletar métricas do sistema
     */
    private function coletarMetricasSistema()
    {
        echo "💻 Coletando métricas do sistema...\n";
        
        // Uso de memória
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);
        
        $this->metrics['sistema']['memoria'] = [
            'atual' => $memoryUsage,
            'pico' => $memoryPeak,
            'atual_formatado' => $this->formatarBytes($memoryUsage),
            'pico_formatado' => $this->formatarBytes($memoryPeak)
        ];
        
        // CPU (se disponível)
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            $this->metrics['sistema']['cpu'] = [
                'load_1min' => $load[0],
                'load_5min' => $load[1],
                'load_15min' => $load[2]
            ];
            
            echo "  🖥️  Load CPU: {$load[0]} (1min), {$load[1]} (5min), {$load[2]} (15min)\n";
        }
        
        // Espaço em disco
        $diretorioCache = __DIR__ . '/../melhorias/cache';
        if (is_dir($diretorioCache)) {
            $tamanhoCache = $this->calcularTamanhoDiretorio($diretorioCache);
            $this->metrics['sistema']['disco'] = [
                'cache_size' => $tamanhoCache,
                'cache_size_formatado' => $this->formatarBytes($tamanhoCache)
            ];
            
            echo "  💾 Cache: {$this->metrics['sistema']['disco']['cache_size_formatado']}\n";
        }
        
        // Tempo de execução
        $this->metrics['sistema']['tempo_execucao'] = [
            'atual' => microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'] ?? time(),
            'limite_php' => ini_get('max_execution_time')
        ];
        
        echo "  ⏱️  Memória: {$this->metrics['sistema']['memoria']['atual_formatado']}\n";
        echo "  📈 Pico: {$this->metrics['sistema']['memoria']['pico_formatado']}\n";
        
        echo "\n";
    }
    
    /**
     * Coletar métricas da aplicação
     */
    private function coletarMetricasAplicacao()
    {
        echo "🚀 Coletando métricas da aplicação...\n";
        
        // Incluir classes do módulo
        require_once __DIR__ . '/../melhorias/02_implementar_cache.php';
        require_once __DIR__ . '/../melhorias/03_relatorios_avancados_corrigido.php';
        
        // Métricas do cache
        $cache = new FinanceiroCache();
        $statsCache = $cache->getEstatisticasCache();
        
        $this->metrics['aplicacao']['cache'] = $statsCache;
        
        echo "  💾 Cache: {$statsCache['total_arquivos']} arquivos ({$statsCache['tamanho_formatado']})\n";
        echo "  ⏰ Expirados: {$statsCache['arquivos_expirados']} ({$statsCache['taxa_expirados']}%)\n";
        
        // Performance dos relatórios
        $relatorios = new FinanceiroRelatoriosAvancadosCorrigido();
        
        $inicio = microtime(true);
        $dashboard = $relatorios->dashboardCompleto();
        $tempoDashboard = microtime(true) - $inicio;
        
        $this->metrics['aplicacao']['relatorios'] = [
            'dashboard_tempo' => $tempoDashboard,
            'dashboard_cache_hit' => $tempoDashboard < 0.01
        ];
        
        echo "  📊 Dashboard: " . round($tempoDashboard * 1000, 2) . "ms\n";
        echo "  🎯 Cache Hit: " . ($this->metrics['aplicacao']['relatorios']['dashboard_cache_hit'] ? 'Sim' : 'Não') . "\n";
        
        // Estatísticas de uso
        $stmt = $this->pdo->query("
            SELECT 
                COUNT(DISTINCT DATE(created_at)) as dias_ativos,
                COUNT(*) as total_transacoes,
                COALESCE(SUM(amount), 0) as valor_total
            FROM order_payments 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        
        $uso = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->metrics['aplicacao']['uso'] = [
            'dias_ativos' => (int) $uso['dias_ativos'],
            'total_transacoes' => (int) $uso['total_transacoes'],
            'valor_total' => (float) $uso['valor_total'],
            'media_diaria' => $uso['dias_ativos'] > 0 ? round($uso['total_transacoes'] / $uso['dias_ativos'], 2) : 0
        ];
        
        echo "  📈 Transações (30 dias): {$this->metrics['aplicacao']['uso']['total_transacoes']}\n";
        echo "  💰 Valor total: R$ " . number_format($this->metrics['aplicacao']['uso']['valor_total'], 2, ',', '.') . "\n";
        echo "  📊 Média diária: {$this->metrics['aplicacao']['uso']['media_diaria']} transações\n";
        
        echo "\n";
    }
    
    /**
     * Analisar performance e identificar problemas
     */
    private function analisarPerformance()
    {
        echo "🔍 Analisando performance...\n";
        
        $problemas = [];
        
        // Analisar queries lentas
        foreach ($this->metrics['banco'] as $nome => $query) {
            if ($nome === 'status') continue;
            
            if ($query['tempo_medio'] > 0.1) {
                $problemas[] = [
                    'tipo' => 'query_lenta',
                    'descricao' => "Query $nome com tempo médio de " . round($query['tempo_medio'] * 1000, 2) . "ms",
                    'severidade' => $query['tempo_medio'] > 0.5 ? 'alta' : 'media'
                ];
            }
        }
        
        // Analisar uso de memória
        $memoryLimit = $this->parseBytes(ini_get('memory_limit'));
        $memoryUsage = $this->metrics['sistema']['memoria']['atual'];
        $memoryPercent = ($memoryUsage / $memoryLimit) * 100;
        
        if ($memoryPercent > 80) {
            $problemas[] = [
                'tipo' => 'memoria_alta',
                'descricao' => "Uso de memória em {$memoryPercent}% do limite",
                'severidade' => $memoryPercent > 90 ? 'alta' : 'media'
            ];
        }
        
        // Analisar cache
        $taxaExpirados = $this->metrics['aplicacao']['cache']['taxa_expirados'];
        if ($taxaExpirados > 50) {
            $problemas[] = [
                'tipo' => 'cache_ineficiente',
                'descricao' => "Taxa de cache expirado muito alta: {$taxaExpirados}%",
                'severidade' => 'media'
            ];
        }
        
        // Analisar queries lentas do MySQL
        $slowQueries = $this->metrics['banco']['status']['slow_queries'];
        if ($slowQueries > 100) {
            $problemas[] = [
                'tipo' => 'slow_queries',
                'descricao' => "Muitas queries lentas detectadas: $slowQueries",
                'severidade' => $slowQueries > 1000 ? 'alta' : 'media'
            ];
        }
        
        $this->metrics['analise'] = [
            'problemas' => $problemas,
            'status_geral' => empty($problemas) ? 'otimo' : 'atencao'
        ];
        
        if (empty($problemas)) {
            echo "  ✅ Nenhum problema de performance detectado\n";
        } else {
            echo "  ⚠️  Problemas detectados: " . count($problemas) . "\n";
            foreach ($problemas as $problema) {
                $icone = $problema['severidade'] === 'alta' ? '🔴' : '🟡';
                echo "    $icone {$problema['descricao']}\n";
            }
        }
        
        echo "\n";
    }
    
    /**
     * Gerar alertas automáticos
     */
    private function gerarAlertas()
    {
        echo "🚨 Gerando alertas...\n";
        
        $this->alertas = [];
        
        // Alerta de queries lentas
        foreach ($this->metrics['banco'] as $nome => $query) {
            if ($nome === 'status') continue;
            
            if ($query['tempo_medio'] > 0.2) {
                $this->alertas[] = [
                    'tipo' => 'performance',
                    'mensagem' => "Query $nome ultrapassou 200ms: " . round($query['tempo_medio'] * 1000, 2) . "ms",
                    'acao' => 'verificar_índices',
                    'timestamp' => date('Y-m-d H:i:s')
                ];
            }
        }
        
        // Alerta de memória
        $memoryLimit = $this->parseBytes(ini_get('memory_limit'));
        $memoryUsage = $this->metrics['sistema']['memoria']['atual'];
        $memoryPercent = ($memoryUsage / $memoryLimit) * 100;
        
        if ($memoryPercent > 85) {
            $this->alertas[] = [
                'tipo' => 'recursos',
                'mensagem' => "Uso de memória crítico: {$memoryPercent}%",
                'acao' => 'aumentar_limite_memoria',
                'timestamp' => date('Y-m-d H:i:s')
            ];
        }
        
        // Alerta de cache
        $totalArquivos = $this->metrics['aplicacao']['cache']['total_arquivos'];
        if ($totalArquivos > 1000) {
            $this->alertas[] = [
                'tipo' => 'manutencao',
                'mensagem' => "Cache com muitos arquivos: $totalArquivos",
                'acao' => 'limpar_cache',
                'timestamp' => date('Y-m-d H:i:s')
            ];
        }
        
        // Salvar alertas
        if (!empty($this->alertas)) {
            file_put_contents(
                __DIR__ . '/logs/alertas.json',
                json_encode($this->alertas, JSON_PRETTY_PRINT)
            );
            
            echo "  🚨 " . count($this->alertas) . " alerta(s) gerado(s)\n";
            foreach ($this->alertas as $alerta) {
                echo "    🔴 {$alerta['mensagem']}\n";
            }
        } else {
            echo "  ✅ Nenhum alerta gerado\n";
        }
        
        echo "\n";
    }
    
    /**
     * Salvar métricas para histórico
     */
    private function salvarMetricas()
    {
        $timestamp = date('Y-m-d H:i:s');
        
        // Adicionar timestamp às métricas
        $this->metrics['timestamp'] = $timestamp;
        
        // Salvar em JSON
        $arquivoMetricas = __DIR__ . '/logs/metrics_' . date('Y-m-d_H-i-s') . '.json';
        file_put_contents($arquivoMetricas, json_encode($this->metrics, JSON_PRETTY_PRINT));
        
        // Salvar no log
        $logEntry = "[$timestamp] Performance Monitor - ";
        $logEntry .= "Queries: " . count($this->metrics['banco']) . " ";
        $logEntry .= "Memória: {$this->metrics['sistema']['memoria']['atual_formatado']} ";
        $logEntry .= "Cache: {$this->metrics['aplicacao']['cache']['total_arquivos']} arquivos ";
        $logEntry .= "Alertas: " . count($this->alertas) . "\n";
        
        file_put_contents($this->logFile, $logEntry, FILE_APPEND);
        
        echo "📄 Métricas salvas em: " . basename($arquivoMetricas) . "\n";
        echo "📝 Log atualizado: " . basename($this->logFile) . "\n\n";
    }
    
    /**
     * Exibir dashboard de monitoramento
     */
    private function exibirDashboard()
    {
        echo "📊 DASHBOARD DE MONITORAMENTO\n";
        echo "==============================\n\n";
        
        // Status geral
        $status = $this->metrics['analise']['status_geral'];
        $statusIcon = $status === 'otimo' ? '🟢' : '🟡';
        echo "🎯 Status Geral: $statusIcon " . strtoupper($status) . "\n\n";
        
        // Performance do banco
        echo "🗄️ PERFORMANCE DO BANCO:\n";
        foreach ($this->metrics['banco'] as $nome => $query) {
            if ($nome === 'status') continue;
            
            $tempo = round($query['tempo_medio'] * 1000, 2);
            $icon = $tempo < 50 ? '🟢' : ($tempo < 100 ? '🟡' : '🔴');
            echo "  $icon $nome: {$tempo}ms\n";
        }
        echo "\n";
        
        // Recursos do sistema
        echo "💻 RECURSOS DO SISTEMA:\n";
        $memoryPercent = ($this->metrics['sistema']['memoria']['atual'] / $this->parseBytes(ini_get('memory_limit'))) * 100;
        $memoryIcon = $memoryPercent < 70 ? '🟢' : ($memoryPercent < 85 ? '🟡' : '🔴');
        echo "  $memoryIcon Memória: {$this->metrics['sistema']['memoria']['atual_formatado']} ({$memoryPercent}%)\n";
        
        if (isset($this->metrics['sistema']['disco'])) {
            echo "  💾 Cache: {$this->metrics['sistema']['disco']['cache_size_formatado']}\n";
        }
        echo "\n";
        
        // Métricas da aplicação
        echo "🚀 MÉTRICAS DA APLICAÇÃO:\n";
        echo "  📊 Transações (30 dias): {$this->metrics['aplicacao']['uso']['total_transacoes']}\n";
        echo "  💰 Valor total: R$ " . number_format($this->metrics['aplicacao']['uso']['valor_total'], 2, ',', '.') . "\n";
        echo "  💾 Cache: {$this->metrics['aplicacao']['cache']['total_arquivos']} arquivos\n";
        echo "  ⏰ Dashboard: " . round($this->metrics['aplicacao']['relatorios']['dashboard_tempo'] * 1000, 2) . "ms\n";
        echo "\n";
        
        // Alertas ativos
        if (!empty($this->alertas)) {
            echo "🚨 ALERTAS ATIVOS:\n";
            foreach ($this->alertas as $alerta) {
                echo "  🔴 {$alerta['mensagem']}\n";
            }
            echo "\n";
        }
        
        // Recomendações
        echo "💡 RECOMENDAÇÕES:\n";
        $recomendacoes = $this->gerarRecomendacoes();
        foreach ($recomendacoes as $rec) {
            echo "  📝 $rec\n";
        }
        
        echo "\n" . str_repeat("=", 58) . "\n";
        echo "📊 MONITORAMENTO CONCLUÍDO\n";
        echo str_repeat("=", 58) . "\n";
    }
    
    /**
     * Gerar recomendações baseadas nas métricas
     */
    private function gerarRecomendacoes()
    {
        $recomendacoes = [];
        
        // Verificar performance de queries
        foreach ($this->metrics['banco'] as $nome => $query) {
            if ($nome === 'status') continue;
            
            if ($query['tempo_medio'] > 0.1) {
                $recomendacoes[] = "Otimizar query $nome (tempo: " . round($query['tempo_medio'] * 1000, 2) . "ms)";
            }
        }
        
        // Verificar uso de memória
        $memoryPercent = ($this->metrics['sistema']['memoria']['atual'] / $this->parseBytes(ini_get('memory_limit'))) * 100;
        if ($memoryPercent > 70) {
            $recomendacoes[] = "Monitorar uso de memória (atual: {$memoryPercent}%)";
        }
        
        // Verificar cache
        if ($this->metrics['aplicacao']['cache']['taxa_expirados'] > 30) {
            $recomendacoes[] = "Ajustar tempo de expiração do cache";
        }
        
        // Verificar slow queries
        if ($this->metrics['banco']['status']['slow_queries'] > 50) {
            $recomendacoes[] = "Investigar slow queries do MySQL";
        }
        
        if (empty($recomendacoes)) {
            $recomendacoes[] = "Sistema operando dentro dos parâmetros normais";
        }
        
        return $recomendacoes;
    }
    
    /**
     * Utilitários
     */
    private function criarDiretorioLogs()
    {
        $dir = dirname($this->logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
    
    private function formatarBytes($bytes)
    {
        $unidades = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($unidades) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $unidades[$pow];
    }
    
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
    
    private function calcularTamanhoDiretorio($dir)
    {
        $tamanho = 0;
        $arquivos = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        
        foreach ($arquivos as $arquivo) {
            $tamanho += $arquivo->getSize();
        }
        
        return $tamanho;
    }
}

// Executar monitoramento se chamado diretamente
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $monitor = new FinanceiroPerformanceMonitor();
    $monitor->iniciarMonitoramento();
}
