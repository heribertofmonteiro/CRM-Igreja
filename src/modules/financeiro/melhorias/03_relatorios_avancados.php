<?php

/**
 * Melhoria 3: Relatórios Financeiros Avançados
 * 
 * Implementação de relatórios detalhados para análise
 * financeira completa e tomada de decisões
 */

class FinanceiroRelatoriosAvancados
{
    private $pdo;
    private $cache;
    
    public function __construct()
    {
        $this->pdo = new PDO(
            'mysql:host=localhost;dbname=autonomo;charset=utf8mb4',
            'heriberto',
            '0631'
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Incluir sistema de cache
        require_once __DIR__ . '/02_implementar_cache.php';
        $this->cache = new FinanceiroCache();
    }
    
    /**
     * Relatório de Fluxo de Caixa
     */
    public function relatorioFluxoCaixa($dataInicio, $dataFim)
    {
        echo "📊 Gerando Relatório de Fluxo de Caixa...\n";
        
        $chave = 'fluxo_caixa_' . md5($dataInicio . $dataFim);
        $cache = $this->cache->get($chave);
        
        if ($cache !== null) {
            echo "📋 Relatório obtido do cache\n";
            return $cache;
        }
        
        $query = "
            SELECT 
                DATE(op.created_at) as data,
                SUM(CASE WHEN op.status = 'completed' THEN op.amount ELSE 0 END) as entradas,
                SUM(CASE WHEN op.status = 'refunded' THEN op.amount ELSE 0 END) as saidas,
                SUM(CASE WHEN op.status = 'completed' THEN op.amount ELSE 0 END) - 
                SUM(CASE WHEN op.status = 'refunded' THEN op.amount ELSE 0 END) as saldo_dia,
                COUNT(CASE WHEN op.status = 'completed' THEN 1 END) as qtd_transacoes,
                COUNT(DISTINCT op.payment_method_id) as metodos_utilizados
            FROM order_payments op
            WHERE DATE(op.created_at) BETWEEN :data_inicio AND :data_fim
            GROUP BY DATE(op.created_at)
            ORDER BY DATE(op.created_at) ASC
        ";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':data_inicio' => $dataInicio,
            ':data_fim' => $dataFim
        ]);
        
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calcular totais
        $totais = [
            'total_entradas' => array_sum(array_column($resultado, 'entradas')),
            'total_saidas' => array_sum(array_column($resultado, 'saidas')),
            'saldo_final' => 0,
            'dias_periodo' => count($resultado)
        ];
        
        $totais['saldo_final'] = $totais['total_entradas'] - $totais['total_saidas'];
        
        $relatorio = [
            'periodo' => [
                'inicio' => $dataInicio,
                'fim' => $dataFim,
                'dias' => $totais['dias_periodo']
            ],
            'totais' => $totais,
            'detalhe_dia' => $resultado
        ];
        
        // Salvar no cache por 1 hora
        $this->cache->set($chave, $relatorio, 3600);
        
        return $relatorio;
    }
    
    /**
     * Relatório de Análise de Métodos de Pagamento
     */
    public function relatorioAnaliseMetodosPagamento($dataInicio, $dataFim)
    {
        echo "💳 Gerando Relatório de Análise de Métodos de Pagamento...\n";
        
        $chave = 'analise_metodos_' . md5($dataInicio . $dataFim);
        $cache = $this->cache->get($chave);
        
        if ($cache !== null) {
            echo "📋 Relatório obtido do cache\n";
            return $cache;
        }
        
        $query = "
            SELECT 
                pm.id,
                pm.name as metodo_nome,
                pm.code as metodo_codigo,
                pm.fee_percentage,
                pm.fee_fixed,
                COUNT(op.id) as total_transacoes,
                COALESCE(SUM(op.amount), 0) as valor_total,
                COALESCE(AVG(op.amount), 0) as valor_medio,
                MIN(op.amount) as valor_minimo,
                MAX(op.amount) as valor_maximo,
                COALESCE(SUM(op.amount * (pm.fee_percentage / 100) + pm.fee_fixed), 0) as total_taxas,
                COUNT(DISTINCT DATE(op.created_at)) as dias_utilizados,
                MAX(op.created_at) as ultima_utilizacao
            FROM payment_methods pm
            LEFT JOIN order_payments op ON pm.id = op.payment_method_id
                AND op.status = 'completed'
                AND DATE(op.created_at) BETWEEN :data_inicio AND :data_fim
            WHERE pm.deleted_at IS NULL
            GROUP BY pm.id, pm.name, pm.code, pm.fee_percentage, pm.fee_fixed
            ORDER BY valor_total DESC
        ";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':data_inicio' => $dataInicio,
            ':data_fim' => $dataFim
        ]);
        
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calcular estatísticas gerais
        $totais = [
            'valor_total_geral' => array_sum(array_column($resultado, 'valor_total')),
            'total_transacoes_geral' => array_sum(array_column($resultado, 'total_transacoes')),
            'total_taxas_geral' => array_sum(array_column($resultado, 'total_taxas')),
            'metodos_ativos' => count(array_filter($resultado, fn($r) => $r['total_transacoes'] > 0))
        ];
        
        // Calcular percentuais
        foreach ($resultado as &$metodo) {
            $metodo['percentual_valor'] = $totais['valor_total_geral'] > 0 
                ? round(($metodo['valor_total'] / $totais['valor_total_geral']) * 100, 2)
                : 0;
                
            $metodo['percentual_transacoes'] = $totais['total_transacoes_geral'] > 0
                ? round(($metodo['total_transacoes'] / $totais['total_transacoes_geral']) * 100, 2)
                : 0;
                
            $metodo['taxa_efetiva_percentual'] = $metodo['valor_total'] > 0
                ? round(($metodo['total_taxas'] / $metodo['valor_total']) * 100, 2)
                : 0;
        }
        
        $relatorio = [
            'periodo' => [
                'inicio' => $dataInicio,
                'fim' => $dataFim
            ],
            'totais' => $totais,
            'metodos' => $resultado
        ];
        
        // Salvar no cache por 2 horas
        $this->cache->set($chave, $relatorio, 7200);
        
        return $relatorio;
    }
    
    /**
     * Relatório de Inadimplência
     */
    public function relatorioInadimplencia($diasAtraso = 30)
    {
        echo "⚠️ Gerando Relatório de Inadimplência...\n";
        
        $chave = 'inadimplencia_' . $diasAtraso;
        $cache = $this->cache->get($chave);
        
        if ($cache !== null) {
            echo "📋 Relatório obtido do cache\n";
            return $cache;
        }
        
        $query = "
            SELECT 
                p.id as pledge_id,
                p.fam_id,
                f.FamName as familia_nome,
                p.amount as valor_pendente,
                p.date as data_vencimento,
                DATEDIFF(NOW(), p.date) as dias_atraso,
                CASE 
                    WHEN DATEDIFF(NOW(), p.date) <= 7 THEN 'A Vencer'
                    WHEN DATEDIFF(NOW(), p.date) <= 30 THEN '1-30 dias'
                    WHEN DATEDIFF(NOW(), p.date) <= 60 THEN '31-60 dias'
                    WHEN DATEDIFF(NOW(), p.date) <= 90 THEN '61-90 dias'
                    ELSE '90+ dias'
                END as faixa_atraso,
                u.FirstName . ' ' . u.LastName as responsavel,
                u.Email as email_responsavel,
                f.HomePhone as telefone_familia
            FROM pledges p
            INNER JOIN family f ON p.fam_id = f.fam_ID
            LEFT JOIN users u ON f.EnteredBy = u.per_ID
            WHERE p.pledge_or_payment = 'Pledge'
              AND p.status = 'pending'
              AND p.date < DATE_SUB(NOW(), INTERVAL :dias_atraso DAY)
              AND p.deleted_at IS NULL
            ORDER BY dias_atraso DESC, p.amount DESC
        ";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':dias_atraso' => $diasAtraso]);
        
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Agrupar por faixa de atraso
        $faixas = [];
        foreach ($resultado as $registro) {
            $faixa = $registro['faixa_atraso'];
            if (!isset($faixas[$faixa])) {
                $faixas[$faixa] = [
                    'quantidade' => 0,
                    'valor_total' => 0,
                    'valor_medio' => 0
                ];
            }
            
            $faixas[$faixa]['quantidade']++;
            $faixas[$faixa]['valor_total'] += $registro['valor_pendente'];
        }
        
        // Calcular médias
        foreach ($faixas as &$faixa) {
            $faixa['valor_medio'] = $faixa['quantidade'] > 0 
                ? round($faixa['valor_total'] / $faixa['quantidade'], 2)
                : 0;
        }
        
        $totais = [
            'total_inadimplentes' => count($resultado),
            'valor_total_inadimplencia' => array_sum(array_column($resultado, 'valor_pendente')),
            'valor_medio_inadimplencia' => count($resultado) > 0 
                ? round(array_sum(array_column($resultado, 'valor_pendente')) / count($resultado), 2)
                : 0,
            'dias_medio_atraso' => count($resultado) > 0
                ? round(array_sum(array_column($resultado, 'dias_atraso')) / count($resultado), 0)
                : 0
        ];
        
        $relatorio = [
            'parametros' => [
                'dias_atraso_minimo' => $diasAtraso,
                'data_geracao' => date('Y-m-d H:i:s')
            ],
            'totais' => $totais,
            'faixas_atraso' => $faixas,
            'detalhe' => $resultado
        ];
        
        // Salvar no cache por 6 horas
        $this->cache->set($chave, $relatorio, 21600);
        
        return $relatorio;
    }
    
    /**
     * Relatório de Tendências Financeiras
     */
    public function relatorioTendenciasFinanceiras($meses = 12)
    {
        echo "📈 Gerando Relatório de Tendências Financeiras...\n";
        
        $chave = 'tendencias_' . $meses;
        $cache = $this->cache->get($chave);
        
        if ($cache !== null) {
            echo "📋 Relatório obtido do cache\n";
            return $cache;
        }
        
        $query = "
            SELECT 
                DATE_FORMAT(op.created_at, '%Y-%m') as mes_ano,
                YEAR(op.created_at) as ano,
                MONTH(op.created_at) as mes,
                COUNT(op.id) as total_transacoes,
                COALESCE(SUM(op.amount), 0) as valor_total,
                COALESCE(AVG(op.amount), 0) as valor_medio,
                COUNT(DISTINCT op.payment_method_id) as metodos_utilizados,
                COUNT(DISTINCT p.fam_id) as familias_atingidas,
                COUNT(DISTINCT DATE(op.created_at)) as dias_com_movimento
            FROM order_payments op
            INNER JOIN pledges p ON op.pledge_id = p.id
            WHERE op.status = 'completed'
              AND op.created_at >= DATE_SUB(NOW(), INTERVAL :meses MONTH)
            GROUP BY DATE_FORMAT(op.created_at, '%Y-%m'), YEAR(op.created_at), MONTH(op.created_at)
            ORDER BY ano DESC, mes DESC
        ";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':meses' => $meses]);
        
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calcular tendências
        $tendencias = [];
        for ($i = 1; $i < count($resultado); $i++) {
            $atual = $resultado[$i-1];
            $anterior = $resultado[$i];
            
            $tendencias[] = [
                'periodo' => $atual['mes_ano'],
                'variacao_valor' => $atual['valor_total'] - $anterior['valor_total'],
                'variacao_percentual' => $anterior['valor_total'] > 0 
                    ? round((($atual['valor_total'] - $anterior['valor_total']) / $anterior['valor_total']) * 100, 2)
                    : 0,
                'variacao_transacoes' => $atual['total_transacoes'] - $anterior['total_transacoes'],
                'variacao_percentual_transacoes' => $anterior['total_transacoes'] > 0
                    ? round((($atual['total_transacoes'] - $anterior['total_transacoes']) / $anterior['total_transacoes']) * 100, 2)
                    : 0
            ];
        }
        
        // Calcular médias móveis
        foreach ($resultado as &$mes) {
            $mes['media_movel_3'] = 0;
            $mes['media_movel_6'] = 0;
        }
        
        for ($i = 2; $i < count($resultado); $i++) {
            // Média móvel de 3 meses
            $resultado[$i]['media_movel_3'] = round((
                $resultado[$i-1]['valor_total'] + 
                $resultado[$i-2]['valor_total'] + 
                $resultado[$i-3]['valor_total']
            ) / 3, 2);
            
            // Média móvel de 6 meses
            if ($i >= 5) {
                $resultado[$i]['media_movel_6'] = 0;
                for ($j = $i-5; $j <= $i; $j++) {
                    $resultado[$i]['media_movel_6'] += $resultado[$j]['valor_total'];
                }
                $resultado[$i]['media_movel_6'] = round($resultado[$i]['media_movel_6'] / 6, 2);
            }
        }
        
        $relatorio = [
            'parametros' => [
                'meses_analisados' => $meses,
                'periodo' => date('Y-m-d', strtotime("-$meses months")) . ' a ' . date('Y-m-d')
            ],
            'dados_mensais' => $resultado,
            'tendencias' => $tendencias,
            'estatisticas' => [
                'valor_total_periodo' => array_sum(array_column($resultado, 'valor_total')),
                'transacoes_totais' => array_sum(array_column($resultado, 'total_transacoes')),
                'valor_medio_mensal' => count($resultado) > 0 
                    ? round(array_sum(array_column($resultado, 'valor_total')) / count($resultado), 2)
                    : 0,
                'mes_crescimento' => 0,
                'taxa_crescimento' => 0
            ]
        ];
        
        // Calcular taxa de crescimento geral
        if (count($tendencias) > 0) {
            $crescimentoPositivo = array_filter($tendencias, fn($t) => $t['variacao_percentual'] > 0);
            $relatorio['estatisticas']['meses_crescimento'] = count($crescimentoPositivo);
            $relatorio['estatisticas']['taxa_crescimento'] = count($tendencias) > 0
                ? round(array_sum(array_column($tendencias, 'variacao_percentual')) / count($tendencias), 2)
                : 0;
        }
        
        // Salvar no cache por 4 horas
        $this->cache->set($chave, $relatorio, 14400);
        
        return $relatorio;
    }
    
    /**
     * Exportar relatório para Excel
     */
    public function exportarExcel($dados, $nomeArquivo)
    {
        echo "📊 Exportando relatório para Excel...\n";
        
        // Criar CSV simples (pode ser aberto no Excel)
        $csv = '';
        $cabecalho = false;
        
        foreach ($dados as $linha) {
            if (!$cabecalho) {
                $csv .= implode(';', array_keys($linha)) . "\n";
                $cabecalho = true;
            }
            
            // Limpar e formatar valores
            $linhaFormatada = [];
            foreach ($linha as $valor) {
                if (is_numeric($valor)) {
                    $linhaFormatada[] = number_format($valor, 2, ',', '.');
                } else {
                    $linhaFormatada[] = '"' . str_replace('"', '""', $valor) . '"';
                }
            }
            
            $csv .= implode(';', $linhaFormatada) . "\n";
        }
        
        $arquivo = __DIR__ . '/exports/' . $nomeArquivo . '.csv';
        $diretorio = dirname($arquivo);
        
        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0755, true);
        }
        
        file_put_contents($arquivo, $csv);
        
        echo "  📄 Arquivo exportado: $arquivo\n";
        echo "  📊 Registros exportados: " . count($dados) . "\n";
        
        return $arquivo;
    }
    
    /**
     * Gerar dashboard completo
     */
    public function dashboardCompleto()
    {
        echo "📊 Gerando Dashboard Financeiro Completo...\n";
        
        $chave = 'dashboard_completo';
        $cache = $this->cache->get($chave);
        
        if ($cache !== null) {
            echo "📋 Dashboard obtido do cache\n";
            return $cache;
        }
        
        // Obter dados dos diferentes relatórios
        $hoje = date('Y-m-d');
        $mesPassado = date('Y-m-d', strtotime('-1 month'));
        
        $dashboard = [
            'resumo_geral' => $this->cache->getEstatisticasFinanceiras(),
            'fluxo_caixa' => $this->relatorioFluxoCaixa($mesPassado, $hoje),
            'metodos_pagamento' => $this->relatorioAnaliseMetodosPagamento($mesPassado, $hoje),
            'inadimplencia' => $this->relatorioInadimplencia(30),
            'tendencias' => $this->relatorioTendenciasFinanceiras(6),
            'metodos_disponiveis' => $this->cache->getMetodosPagamento()
        ];
        
        // Salvar no cache por 10 minutos
        $this->cache->set($chave, $dashboard, 600);
        
        return $dashboard;
    }
}

// Executar relatórios se chamado diretamente
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    echo "📊 INICIANDO RELATÓRIOS FINANCEIROS AVANÇADOS\n";
    echo "================================================\n";
    
    $relatorios = new FinanceiroRelatoriosAvancados();
    
    // Gerar dashboard completo
    $dashboard = $relatorios->dashboardCompleto();
    
    echo "\n📈 DASHBOARD FINANCEIRO GERADO:\n";
    echo "  📊 Resumo: R$ " . number_format($dashboard['resumo_geral']['valor_total'], 2, ',', '.') . "\n";
    echo "  💳 Métodos: " . count($dashboard['metodos_pagamento']['metodos']) . "\n";
    echo "  ⚠️ Inadimplentes: " . $dashboard['inadimplencia']['totais']['total_inadimplentes'] . "\n";
    echo "  📈 Tendência: " . $dashboard['tendencias']['estatisticas']['taxa_crescimento'] . "%\n";
    
    echo "\n🎉 RELATÓRIOS AVANÇADOS IMPLEMENTADOS!\n";
    echo "📊 Total de relatórios: 5 principais\n";
    echo "📈 Análise de tendências e padrões\n";
    echo "📄 Exportação para Excel disponível\n";
}
