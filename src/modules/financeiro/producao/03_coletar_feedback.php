<?php

/**
 * Passo 3: Sistema de Coleta de Feedback dos Usuários
 * 
 * Sistema completo para coletar, analisar e processar
 * feedback dos usuários sobre o módulo financeiro
 */

class FinanceiroFeedbackCollector
{
    private $pdo;
    private $feedbackFile;
    private $surveyData = [];
    
    public function __construct()
    {
        $this->pdo = new PDO(
            'mysql:host=localhost;dbname=autonomo;charset=utf8mb4',
            'heriberto',
            '0631'
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $this->feedbackFile = __DIR__ . '/data/feedback.json';
        $this->criarDiretorioDados();
    }
    
    /**
     * Iniciar sistema de feedback
     */
    public function iniciarSistemaFeedback()
    {
        echo "💬 INICIANDO SISTEMA DE FEEDBACK\n";
        echo "==================================\n\n";
        
        // 1. Criar tabelas de feedback
        $this->criarTabelasFeedback();
        
        // 2. Gerar formulário de feedback
        $this->gerarFormularioFeedback();
        
        // 3. Coletar feedback simulado
        $this->coletarFeedbackSimulado();
        
        // 4. Analisar feedback recebido
        $this->analisarFeedback();
        
        // 5. Gerar relatório de insights
        $this->gerarRelatorioInsights();
        
        // 6. Criar plano de ação
        $this->criarPlanoAcao();
    }
    
    /**
     * Criar tabelas para armazenar feedback
     */
    private function criarTabelasFeedback()
    {
        echo "🗄️ Criando tabelas de feedback...\n";
        
        // Tabela de feedback principal
        $sqlFeedback = "
            CREATE TABLE IF NOT EXISTS financeiro_feedback (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NOT NULL,
                user_name VARCHAR(255) NOT NULL,
                user_email VARCHAR(255) NOT NULL,
                user_role VARCHAR(100) NOT NULL,
                rating TINYINT UNSIGNED NOT NULL CHECK (rating BETWEEN 1 AND 5),
                ease_of_use TINYINT UNSIGNED NOT NULL CHECK (ease_of_use BETWEEN 1 AND 5),
                performance TINYINT UNSIGNED NOT NULL CHECK (performance BETWEEN 1 AND 5),
                features TINYINT UNSIGNED NOT NULL CHECK (features BETWEEN 1 AND 5),
                overall_satisfaction TINYINT UNSIGNED NOT NULL CHECK (overall_satisfaction BETWEEN 1 AND 5),
                most_used_feature VARCHAR(255),
                least_used_feature VARCHAR(255),
                would_recommend BOOLEAN DEFAULT NULL,
                comments TEXT,
                suggestions TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_user_id (user_id),
                INDEX idx_rating (rating),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $this->pdo->exec($sqlFeedback);
        echo "  ✅ Tabela financeiro_feedback criada\n";
        
        // Tabela de feature requests
        $sqlFeatures = "
            CREATE TABLE IF NOT EXISTS financeiro_feature_requests (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NOT NULL,
                user_name VARCHAR(255) NOT NULL,
                feature_title VARCHAR(255) NOT NULL,
                feature_description TEXT NOT NULL,
                priority ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
                category ENUM('reports', 'payments', 'methods', 'ui', 'performance', 'other') DEFAULT 'other',
                status ENUM('pending', 'under_review', 'approved', 'rejected', 'implemented') DEFAULT 'pending',
                votes INT UNSIGNED DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_user_id (user_id),
                INDEX idx_priority (priority),
                INDEX idx_status (status),
                INDEX idx_category (category)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $this->pdo->exec($sqlFeatures);
        echo "  ✅ Tabela financeiro_feature_requests criada\n";
        
        // Tabela de bugs reportados
        $sqlBugs = "
            CREATE TABLE IF NOT EXISTS financeiro_bug_reports (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NOT NULL,
                user_name VARCHAR(255) NOT NULL,
                user_email VARCHAR(255) NOT NULL,
                bug_title VARCHAR(255) NOT NULL,
                bug_description TEXT NOT NULL,
                severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
                category ENUM('ui', 'performance', 'calculation', 'integration', 'other') DEFAULT 'other',
                browser VARCHAR(100),
                operating_system VARCHAR(100),
                steps_to_reproduce TEXT,
                expected_behavior TEXT,
                actual_behavior TEXT,
                status ENUM('open', 'investigating', 'fixed', 'closed', 'not_a_bug') DEFAULT 'open',
                assigned_to VARCHAR(255),
                resolution TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_user_id (user_id),
                INDEX idx_severity (severity),
                INDEX idx_status (status),
                INDEX idx_category (category)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $this->pdo->exec($sqlBugs);
        echo "  ✅ Tabela financeiro_bug_reports criada\n";
        
        echo "\n";
    }
    
    /**
     * Gerar formulário HTML de feedback
     */
    private function gerarFormularioFeedback()
    {
        echo "📝 Gerando formulário de feedback...\n";
        
        $formHTML = '
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - Módulo Financeiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        .rating-star { cursor: pointer; color: #ddd; font-size: 1.5rem; }
        .rating-star.active { color: #ffc107; }
        .feature-card { transition: transform 0.2s; }
        .feature-card:hover { transform: translateY(-2px); }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-comment-dots me-2"></i>
                            Feedback - Módulo Financeiro
                        </h4>
                    </div>
                    <div class="card-body">
                        <form id="feedbackForm">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Seu Nome</label>
                                    <input type="text" class="form-control" name="user_name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Seu Email</label>
                                    <input type="email" class="form-control" name="user_email" required>
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Seu Cargo</label>
                                    <select class="form-select" name="user_role" required>
                                        <option value="">Selecione...</option>
                                        <option value="admin">Administrador</option>
                                        <option value="financeiro">Financeiro</option>
                                        <option value="pastor">Pastor</option>
                                        <option value="secretaria">Secretaria</option>
                                        <option value="membro">Membro</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tempo de Uso</label>
                                    <select class="form-select" name="usage_time">
                                        <option value="">Selecione...</option>
                                        <option value="1_semana">Menos de 1 semana</option>
                                        <option value="1_mes">1-3 meses</option>
                                        <option value="6_meses">3-6 meses</option>
                                        <option value="1_ano">6 meses - 1 ano</option>
                                        <option value="mais_1_ano">Mais de 1 ano</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <h5 class="mb-3">Avalie o Módulo Financeiro</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Facilidade de Uso</label>
                                        <div class="rating-group" data-field="ease_of_use">
                                            <i class="fas fa-star rating-star" data-value="1"></i>
                                            <i class="fas fa-star rating-star" data-value="2"></i>
                                            <i class="fas fa-star rating-star" data-value="3"></i>
                                            <i class="fas fa-star rating-star" data-value="4"></i>
                                            <i class="fas fa-star rating-star" data-value="5"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Performance</label>
                                        <div class="rating-group" data-field="performance">
                                            <i class="fas fa-star rating-star" data-value="1"></i>
                                            <i class="fas fa-star rating-star" data-value="2"></i>
                                            <i class="fas fa-star rating-star" data-value="3"></i>
                                            <i class="fas fa-star rating-star" data-value="4"></i>
                                            <i class="fas fa-star rating-star" data-value="5"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Funcionalidades</label>
                                        <div class="rating-group" data-field="features">
                                            <i class="fas fa-star rating-star" data-value="1"></i>
                                            <i class="fas fa-star rating-star" data-value="2"></i>
                                            <i class="fas fa-star rating-star" data-value="3"></i>
                                            <i class="fas fa-star rating-star" data-value="4"></i>
                                            <i class="fas fa-star rating-star" data-value="5"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Satisfação Geral</label>
                                        <div class="rating-group" data-field="overall_satisfaction">
                                            <i class="fas fa-star rating-star" data-value="1"></i>
                                            <i class="fas fa-star rating-star" data-value="2"></i>
                                            <i class="fas fa-star rating-star" data-value="3"></i>
                                            <i class="fas fa-star rating-star" data-value="4"></i>
                                            <i class="fas fa-star rating-star" data-value="5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Funcionalidade Mais Usada</label>
                                    <select class="form-select" name="most_used_feature">
                                        <option value="">Selecione...</option>
                                        <option value="dashboard">Dashboard Financeiro</option>
                                        <option value="metodos_pagamento">Métodos de Pagamento</option>
                                        <option value="relatorios">Relatórios</option>
                                        <option value="fluxo_caixa">Fluxo de Caixa</option>
                                        <option value="tendencias">Análise de Tendências</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Funcionalidade Menos Usada</label>
                                    <select class="form-select" name="least_used_feature">
                                        <option value="">Selecione...</option>
                                        <option value="dashboard">Dashboard Financeiro</option>
                                        <option value="metodos_pagamento">Métodos de Pagamento</option>
                                        <option value="relatorios">Relatórios</option>
                                        <option value="fluxo_caixa">Fluxo de Caixa</option>
                                        <option value="tendencias">Análise de Tendências</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="would_recommend" id="wouldRecommend">
                                    <label class="form-check-label" for="wouldRecommend">
                                        Recomendaria este módulo para outras igrejas?
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Comentários</label>
                                <textarea class="form-control" name="comments" rows="4" 
                                    placeholder="O que você mais gostou no módulo?"></textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Sugestões de Melhoria</label>
                                <textarea class="form-control" name="suggestions" rows="4" 
                                    placeholder="O que poderia ser melhorado?"></textarea>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Enviar Feedback
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sistema de avaliação por estrelas
        document.querySelectorAll(".rating-group").forEach(group => {
            const stars = group.querySelectorAll(".rating-star");
            const field = group.dataset.field;
            
            stars.forEach((star, index) => {
                star.addEventListener("click", () => {
                    const value = parseInt(star.dataset.value);
                    stars.forEach((s, i) => {
                        s.classList.toggle("active", i < value);
                    });
                    
                    // Criar input hidden
                    let input = document.querySelector(`input[name="${field}"]`);
                    if (!input) {
                        input = document.createElement("input");
                        input.type = "hidden";
                        input.name = field;
                        document.getElementById("feedbackForm").appendChild(input);
                    }
                    input.value = value;
                });
                
                star.addEventListener("mouseenter", () => {
                    const value = parseInt(star.dataset.value);
                    stars.forEach((s, i) => {
                        s.classList.toggle("active", i < value);
                    });
                });
            });
            
            group.addEventListener("mouseleave", () => {
                const input = document.querySelector(`input[name="${field}"]`);
                const value = input ? parseInt(input.value) : 0;
                stars.forEach((s, i) => {
                    s.classList.toggle("active", i < value);
                });
            });
        });
        
        // Submit do formulário
        document.getElementById("feedbackForm").addEventListener("submit", (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);
            
            // Converter checkbox
            data.would_recommend = formData.has("would_recommend");
            
            // Enviar para API
            fetch("/api/financeiro/feedback", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert("Feedback enviado com sucesso! Obrigado pela sua contribuição.");
                    e.target.reset();
                    document.querySelectorAll(".rating-star").forEach(s => s.classList.remove("active"));
                } else {
                    alert("Erro ao enviar feedback: " + result.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Erro ao enviar feedback. Tente novamente.");
            });
        });
    </script>
</body>
</html>';
        
        file_put_contents(__DIR__ . '/feedback_form.html', $formHTML);
        echo "  ✅ Formulário criado: feedback_form.html\n";
        
        echo "\n";
    }
    
    /**
     * Coletar feedback simulado para demonstração
     */
    private function coletarFeedbackSimulado()
    {
        echo "📊 Coletando feedback simulado...\n";
        
        // Dados simulados de feedback
        $feedbackSimulado = [
            [
                'user_name' => 'João Silva',
                'user_email' => 'joao@igreja.com',
                'user_role' => 'financeiro',
                'rating' => 5,
                'ease_of_use' => 4,
                'performance' => 5,
                'features' => 4,
                'overall_satisfaction' => 5,
                'most_used_feature' => 'relatorios',
                'least_used_feature' => 'tendencias',
                'would_recommend' => 1,
                'comments' => 'Excelente módulo! Os relatórios são muito detalhados e úteis.',
                'suggestions' => 'Poderia ter mais opções de exportação.'
            ],
            [
                'user_name' => 'Maria Santos',
                'user_email' => 'maria@igreja.com',
                'user_role' => 'admin',
                'rating' => 4,
                'ease_of_use' => 3,
                'performance' => 5,
                'features' => 4,
                'overall_satisfaction' => 4,
                'most_used_feature' => 'dashboard',
                'least_used_feature' => 'fluxo_caixa',
                'would_recommend' => 1,
                'comments' => 'Muito bom para gestão financeira. Performance excelente.',
                'suggestions' => 'Interface poderia ser mais intuitiva para novos usuários.'
            ],
            [
                'user_name' => 'Pedro Oliveira',
                'user_email' => 'pedro@igreja.com',
                'user_role' => 'pastor',
                'rating' => 3,
                'ease_of_use' => 3,
                'performance' => 4,
                'features' => 3,
                'overall_satisfaction' => 3,
                'most_used_feature' => 'tendencias',
                'least_used_feature' => 'metodos_pagamento',
                'would_recommend' => 0,
                'comments' => 'Funciona bem, mas falta algumas funcionalidades.',
                'suggestions' => 'Precisa de mais gráficos visuais e relatórios comparativos.'
            ]
        ];
        
        // Inserir feedback simulado
        foreach ($feedbackSimulado as $feedback) {
            $sql = "
                INSERT INTO financeiro_feedback 
                (user_id, user_name, user_email, user_role, rating, ease_of_use, performance, 
                 features, overall_satisfaction, most_used_feature, least_used_feature, 
                 would_recommend, comments, suggestions)
                VALUES 
                (1, :user_name, :user_email, :user_role, :rating, :ease_of_use, :performance,
                 :features, :overall_satisfaction, :most_used_feature, :least_used_feature,
                 :would_recommend, :comments, :suggestions)
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($feedback);
            
            echo "  ✅ Feedback de {$feedback['user_name']} inserido\n";
        }
        
        // Feature requests simulados
        $featureRequests = [
            [
                'user_name' => 'Ana Costa',
                'feature_title' => 'Integração com Bancos',
                'feature_description' => 'Sincronização automática com contas bancárias',
                'priority' => 'high',
                'category' => 'reports'
            ],
            [
                'user_name' => 'Carlos Mendes',
                'feature_title' => 'Relatórios Personalizados',
                'feature_description' => 'Permitir criar e salvar relatórios personalizados',
                'priority' => 'medium',
                'category' => 'reports'
            ]
        ];
        
        foreach ($featureRequests as $request) {
            $sql = "
                INSERT INTO financeiro_feature_requests 
                (user_id, user_name, feature_title, feature_description, priority, category)
                VALUES 
                (1, :user_name, :feature_title, :feature_description, :priority, :category)
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($request);
            
            echo "  ✅ Feature request de {$request['user_name']} inserido\n";
        }
        
        echo "\n";
    }
    
    /**
     * Analisar feedback recebido
     */
    private function analisarFeedback()
    {
        echo "📈 Analisando feedback recebido...\n";
        
        // Estatísticas gerais
        $stmt = $this->pdo->query("
            SELECT 
                COUNT(*) as total_feedback,
                AVG(rating) as avg_rating,
                AVG(ease_of_use) as avg_ease,
                AVG(performance) as avg_performance,
                AVG(features) as avg_features,
                AVG(overall_satisfaction) as avg_satisfaction,
                SUM(CASE WHEN would_recommend = 1 THEN 1 ELSE 0 END) as would_recommend,
                COUNT(*) as total_responses
            FROM financeiro_feedback
        ");
        
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->surveyData['estatisticas_gerais'] = $stats;
        
        echo "  📊 Total de feedbacks: {$stats['total_feedback']}\n";
        echo "  ⭐ Avaliação média: " . round($stats['avg_rating'], 2) . "/5\n";
        echo "  🎯 Satisfação geral: " . round($stats['avg_satisfaction'], 2) . "/5\n";
        echo "  👍 Recomendariam: {$stats['would_recommend']}/{$stats['total_responses']} (" . 
             round(($stats['would_recommend'] / $stats['total_responses']) * 100, 1) . "%)\n";
        
        // Análise por funcionalidade
        $stmt = $this->pdo->query("
            SELECT 
                most_used_feature,
                COUNT(*) as usage_count,
                ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM financeiro_feedback WHERE most_used_feature IS NOT NULL), 2) as percentage
            FROM financeiro_feedback 
            WHERE most_used_feature IS NOT NULL
            GROUP BY most_used_feature
            ORDER BY usage_count DESC
        ");
        
        $mostUsed = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->surveyData['funcionalidades_mais_usadas'] = $mostUsed;
        
        echo "\n  🔥 Funcionalidades mais usadas:\n";
        foreach ($mostUsed as $feature) {
            echo "    📊 {$feature['most_used_feature']}: {$feature['usage_count']} ({$feature['percentage']}%)\n";
        }
        
        // Análise por cargo
        $stmt = $this->pdo->query("
            SELECT 
                user_role,
                COUNT(*) as count,
                AVG(rating) as avg_rating,
                AVG(overall_satisfaction) as avg_satisfaction
            FROM financeiro_feedback
            GROUP BY user_role
            ORDER BY count DESC
        ");
        
        $byRole = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->surveyData['analise_por_cargo'] = $byRole;
        
        echo "\n  👥 Análise por cargo:\n";
        foreach ($byRole as $role) {
            echo "    👤 {$role['user_role']}: {$role['count']} usuários, " .
                 "avaliação " . round($role['avg_rating'], 2) . "/5\n";
        }
        
        // Sugestões mais comuns
        $stmt = $this->pdo->query("
            SELECT suggestions, COUNT(*) as frequency
            FROM financeiro_feedback 
            WHERE suggestions IS NOT NULL AND suggestions != ''
            GROUP BY suggestions
            HAVING frequency > 1
            ORDER BY frequency DESC
            LIMIT 5
        ");
        
        $suggestions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->surveyData['sugestoes_comuns'] = $suggestions;
        
        if (!empty($suggestions)) {
            echo "\n  💡 Sugestões mais comuns:\n";
            foreach ($suggestions as $suggestion) {
                echo "    📝 " . substr($suggestion['suggestions'], 0, 50) . "... ({$suggestion['frequency']} vezes)\n";
            }
        }
        
        echo "\n";
    }
    
    /**
     * Gerar relatório de insights
     */
    private function gerarRelatorioInsights()
    {
        echo "🧠 Gerando insights do feedback...\n";
        
        $insights = [];
        
        // Insight 1: Satisfação vs Performance
        $avgSatisfaction = $this->surveyData['estatisticas_gerais']['avg_satisfaction'];
        $avgPerformance = $this->surveyData['estatisticas_gerais']['avg_performance'];
        
        if ($avgPerformance > $avgSatisfaction) {
            $insights[] = [
                'tipo' => 'oportunidade',
                'descricao' => 'Performance é mais bem avaliada que satisfação geral. Foco em melhorar experiência do usuário.',
                'acao' => 'melhorar_ui_ux',
                'prioridade' => 'alta'
            ];
        }
        
        // Insight 2: Taxa de recomendação
        $recommendRate = ($this->surveyData['estatisticas_gerais']['would_recommend'] / 
                         $this->surveyData['estatisticas_gerais']['total_responses']) * 100;
        
        if ($recommendRate < 70) {
            $insights[] = [
                'tipo' => 'critico',
                'descricao' => 'Taxa de recomendação baixa. Usuários não estão satisfeitos o suficiente.',
                'acao' => 'investigar_causas_insatisfacao',
                'prioridade' => 'critica'
            ];
        } elseif ($recommendRate > 85) {
            $insights[] = [
                'tipo' => 'fortaleza',
                'descricao' => 'Excelente taxa de recomendação. Usuários estão muito satisfeitos.',
                'acao' => 'capitalizar_sucesso',
                'prioridade' => 'manter'
            ];
        }
        
        // Insight 3: Funcionalidades mais usadas
        $topFeature = $this->surveyData['funcionalidades_mais_usadas'][0]['most_used_feature'] ?? null;
        if ($topFeature === 'relatorios') {
            $insights[] = [
                'tipo' => 'fortaleza',
                'descricao' => 'Relatórios são a funcionalidade mais valorizada. Investir em expandir esta área.',
                'acao' => 'expandir_relatorios',
                'prioridade' => 'alta'
            ];
        }
        
        // Insight 4: Análise por cargo
        $byRole = $this->surveyData['analise_por_cargo'];
        $lowestRating = null;
        $lowestScore = 5;
        
        foreach ($byRole as $role) {
            if ($role['avg_rating'] < $lowestScore) {
                $lowestScore = $role['avg_rating'];
                $lowestRating = $role['user_role'];
            }
        }
        
        if ($lowestRating && $lowestScore < 3.5) {
            $insights[] = [
                'tipo' => 'oportunidade',
                'descricao' => "Cargo '$lowestRating' com avaliação mais baixa. Necessita atenção específica.",
                'acao' => 'treinamento_especifico',
                'prioridade' => 'media'
            ];
        }
        
        $this->surveyData['insights'] = $insights;
        
        echo "  🧠 Insights gerados: " . count($insights) . "\n";
        foreach ($insights as $insight) {
            $icon = $insight['tipo'] === 'critico' ? '🔴' : 
                   ($insight['tipo'] === 'oportunidade' ? '🟡' : '🟢');
            echo "    $icon {$insight['descricao']}\n";
        }
        
        echo "\n";
    }
    
    /**
     * Criar plano de ação baseado no feedback
     */
    private function criarPlanoAcao()
    {
        echo "📋 Criando plano de ação...\n";
        
        $planoAcao = [
            'versao' => '2.0',
            'periodo' => 'Próximos 3 meses',
            'acoes' => []
        ];
        
        // Ação 1: Melhorar UI/UX (se necessário)
        $avgSatisfaction = $this->surveyData['estatisticas_gerais']['avg_satisfaction'];
        if ($avgSatisfaction < 4.0) {
            $planoAcao['acoes'][] = [
                'id' => 'ui_ux_improvement',
                'titulo' => 'Melhorar Interface e Experiência do Usuário',
                'descricao' => 'Redesenhar interface com base no feedback dos usuários',
                'prioridade' => 'alta',
                'responsavel' => 'UI/UX Team',
                'prazo' => '6 semanas',
                'kpi' => 'Aumentar satisfação geral para > 4.2',
                'recursos' => 'Designer, Frontend Developer',
                'dependencias' => 'Análise detalhada do feedback'
            ];
        }
        
        // Ação 2: Expandir relatórios
        $planoAcao['acoes'][] = [
            'id' => 'expand_reports',
            'titulo' => 'Expandir Funcionalidades de Relatórios',
            'descricao' => 'Adicionar novos tipos de relatórios e personalização',
            'prioridade' => 'alta',
            'responsavel' => 'Backend Team',
            'prazo' => '8 semanas',
            'kpi' => 'Aumentar uso de relatórios em 25%',
            'recursos' => 'Backend Developer, Database Specialist',
            'dependencias' => 'Definição de novos relatórios'
        ];
        
        // Ação 3: Implementar feature requests
        $planoAcao['acoes'][] = [
            'id' => 'feature_requests',
            'titulo' => 'Implementar Feature Requests Mais Votados',
            'descricao' => 'Priorizar e implementar as funcionalidades mais solicitadas',
            'prioridade' => 'media',
            'responsavel' => 'Product Team',
            'prazo' => '10 semanas',
            'kpi' => 'Implementar top 5 feature requests',
            'recursos' => 'Full Stack Developer',
            'dependencias' => 'Análise de viabilidade técnica'
        ];
        
        // Ação 4: Treinamento e documentação
        $planoAcao['acoes'][] = [
            'id' => 'training_docs',
            'titulo' => 'Criar Treinamento e Documentação',
            'descricao' => 'Desenvolver materiais de treinamento e documentação detalhada',
            'prioridade' => 'media',
            'responsavel' => 'Content Team',
            'prazo' => '4 semanas',
            'kpi' => 'Reduzir dúvidas de usuários em 40%',
            'recursos' => 'Technical Writer, Instructional Designer',
            'dependencias' => 'Nenhuma'
        ];
        
        // Ação 5: Monitoramento contínuo
        $planoAcao['acoes'][] = [
            'id' => 'continuous_monitoring',
            'titulo' => 'Implementar Monitoramento Contínuo',
            'descricao' => 'Sistema automatizado para coletar feedback e métricas',
            'prioridade' => 'baixa',
            'responsavel' => 'DevOps Team',
            'prazo' => '3 semanas',
            'kpi' => 'Coletar feedback de 100% dos usuários ativos',
            'recursos' => 'DevOps Engineer',
            'dependencias' => 'Definição de métricas'
        ];
        
        // Salvar plano de ação
        file_put_contents(
            __DIR__ . '/plano_acao_v2.json',
            json_encode($planoAcao, JSON_PRETTY_PRINT)
        );
        
        echo "  📋 Plano de ação criado: plano_acao_v2.json\n";
        echo "  🎯 Total de ações: " . count($planoAcao['acoes']) . "\n";
        
        echo "\n  📅 Cronograma Resumo:\n";
        foreach ($planoAcao['acoes'] as $acao) {
            $icon = $acao['prioridade'] === 'alta' ? '🔴' : 
                   ($acao['prioridade'] === 'media' ? '🟡' : '🟢');
            echo "    $icon {$acao['titulo']} ({$acao['prazo']})\n";
        }
        
        echo "\n";
    }
    
    /**
     * Utilitários
     */
    private function criarDiretorioDados()
    {
        $dir = dirname($this->feedbackFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}

// Executar sistema de feedback se chamado diretamente
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $feedback = new FinanceiroFeedbackCollector();
    $feedback->iniciarSistemaFeedback();
}
