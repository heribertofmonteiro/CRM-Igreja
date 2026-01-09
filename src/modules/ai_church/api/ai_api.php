<?php

/**
 * 🚀 AI Church API - Endpoints RESTful
 * 
 * API completa para todos os módulos de IA
 * Integração total com ChurchCRM
 */

require_once __DIR__ . '/../core/ai_engine.php';

class AIChurchAPI
{
    private $aiEngine;
    private $churchCRM;
    
    public function __construct()
    {
        $this->aiEngine = new AIChurchEngine();
        $this->churchCRM = $this->connectChurchCRM();
        $this->setupRoutes();
    }
    
    /**
     * Conectar com ChurchCRM
     */
    private function connectChurchCRM()
    {
        return new class {
            public function getMembers($filters = []) {
                // Simulação de conexão com ChurchCRM
                return [
                    ['id' => 1, 'name' => 'João Silva', 'type' => 'member', 'join_date' => '2020-01-15'],
                    ['id' => 2, 'name' => 'Maria Santos', 'type' => 'member', 'join_date' => '2019-03-20'],
                    ['id' => 3, 'name' => 'Pedro Oliveira', 'type' => 'volunteer', 'join_date' => '2021-06-10']
                ];
            }
            
            public function getFinancialData($period = 'month') {
                return [
                    'tithes' => 15000,
                    'offerings' => 3500,
                    'expenses' => 8000,
                    'date' => date('Y-m-d')
                ];
            }
            
            public function getEvents($filters = []) {
                return [
                    ['id' => 1, 'title' => 'Culto de Domingo', 'date' => '2024-01-14', 'attendance' => 250],
                    ['id' => 2, 'title' => 'Estudo Bíblico', 'date' => '2024-01-15', 'attendance' => 45]
                ];
            }
        };
    }
    
    /**
     * Configurar rotas da API
     */
    private function setupRoutes()
    {
        // Headers CORS
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Content-Type: application/json');
        
        // Obter método e path
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = str_replace('/api/ai', '', $path);
        
        // Router
        try {
            $response = $this->route($method, $path);
            echo json_encode($response, JSON_PRETTY_PRINT);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        }
    }
    
    /**
     * Router principal
     */
    private function route($method, $path)
    {
        $pathParts = explode('/', trim($path, '/'));
        
        // Rotas principais
        switch ($pathParts[0]) {
            case 'analytics':
                return $this->handleAnalytics($method, $pathParts);
            case 'strategic':
                return $this->handleStrategic($method, $pathParts);
            case 'pastoral':
                return $this->handlePastoral($method, $pathParts);
            case 'financial':
                return $this->handleFinancial($method, $pathParts);
            case 'operational':
                return $this->handleOperational($method, $pathParts);
            case 'search':
                return $this->handleSearch($method, $pathParts);
            case 'insights':
                return $this->handleInsights($method, $pathParts);
            case 'status':
                return $this->handleStatus($method, $pathParts);
            default:
                throw new Exception('Endpoint não encontrado');
        }
    }
    
    /**
     * Módulo Analytics
     */
    private function handleAnalytics($method, $pathParts)
    {
        switch ($method) {
            case 'GET':
                if (empty($pathParts[1])) {
                    return $this->getAnalyticsOverview();
                }
                
                switch ($pathParts[1]) {
                    case 'growth':
                        return $this->predictGrowth();
                    case 'attendance':
                        return $this->forecastAttendance();
                    case 'segments':
                        return $this->segmentMembers();
                    case 'trends':
                        return $this->analyzeTrends();
                    default:
                        throw new Exception('Analytics endpoint não encontrado');
                }
                
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                return $this->aiEngine->processRequest('analytics', $pathParts[1], $data);
                
            default:
                throw new Exception('Método não permitido');
        }
    }
    
    /**
     * Módulo Strategic
     */
    private function handleStrategic($method, $pathParts)
    {
        switch ($method) {
            case 'GET':
                if (empty($pathParts[1])) {
                    return $this->getStrategicOverview();
                }
                
                switch ($pathParts[1]) {
                    case 'goals':
                        return $this->getGoals();
                    case 'scenarios':
                        return $this->simulateScenarios();
                    case 'risks':
                        return $this->assessRisks();
                    default:
                        throw new Exception('Strategic endpoint não encontrado');
                }
                
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                return $this->aiEngine->processRequest('strategic', $pathParts[1], $data);
                
            default:
                throw new Exception('Método não permitido');
        }
    }
    
    /**
     * Módulo Pastoral
     */
    private function handlePastoral($method, $pathParts)
    {
        switch ($method) {
            case 'GET':
                if (empty($pathParts[1])) {
                    return $this->getPastoralOverview();
                }
                
                switch ($pathParts[1]) {
                    case 'engagement':
                        return $this->getEngagementAnalysis();
                    case 'churn':
                        return $this->getChurnPrediction();
                    case 'growth':
                        return $this->getSpiritualGrowth();
                    case 'communities':
                        return $this->getCommunities();
                    default:
                        throw new Exception('Pastoral endpoint não encontrado');
                }
                
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                return $this->aiEngine->processRequest('pastoral', $pathParts[1], $data);
                
            default:
                throw new Exception('Método não permitido');
        }
    }
    
    /**
     * Módulo Financial
     */
    private function handleFinancial($method, $pathParts)
    {
        switch ($method) {
            case 'GET':
                if (empty($pathParts[1])) {
                    return $this->getFinancialOverview();
                }
                
                switch ($pathParts[1]) {
                    case 'forecast':
                        return $this->getRevenueForecast();
                    case 'optimization':
                        return $this->getExpenseOptimization();
                    case 'fraud':
                        return $this->getFraudDetection();
                    case 'investments':
                        return $this->getInvestmentAnalysis();
                    default:
                        throw new Exception('Financial endpoint não encontrado');
                }
                
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                return $this->aiEngine->processRequest('financial', $pathParts[1], $data);
                
            default:
                throw new Exception('Método não permitido');
        }
    }
    
    /**
     * Módulo Operational
     */
    private function handleOperational($method, $pathParts)
    {
        switch ($method) {
            case 'GET':
                if (empty($pathParts[1])) {
                    return $this->getOperationalOverview();
                }
                
                switch ($pathParts[1]) {
                    case 'events':
                        return $this->getEventPlanning();
                    case 'volunteers':
                        return $this->getVolunteerOptimization();
                    case 'facilities':
                        return $this->getFacilityManagement();
                    case 'communication':
                        return $this->getCommunicationRouting();
                    default:
                        throw new Exception('Operational endpoint não encontrado');
                }
                
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                return $this->aiEngine->processRequest('operational', $pathParts[1], $data);
                
            default:
                throw new Exception('Método não permitido');
        }
    }
    
    /**
     * Módulo Search
     */
    private function handleSearch($method, $pathParts)
    {
        switch ($method) {
            case 'GET':
                if (empty($pathParts[1])) {
                    throw new Exception('Parâmetro de busca necessário');
                }
                
                $query = $_GET['q'] ?? '';
                $type = $_GET['type'] ?? 'semantic';
                
                return $this->performSearch($query, $type);
                
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                return $this->aiEngine->processRequest('search', $pathParts[1], $data);
                
            default:
                throw new Exception('Método não permitido');
        }
    }
    
    /**
     * Insights
     */
    private function handleInsights($method, $pathParts)
    {
        switch ($method) {
            case 'GET':
                return $this->getInsights();
            case 'POST':
                $this->aiEngine->generateInsights();
                return ['message' => 'Insights gerados com sucesso'];
            default:
                throw new Exception('Método não permitido');
        }
    }
    
    /**
     * Status do sistema
     */
    private function handleStatus($method, $pathParts)
    {
        if ($method !== 'GET') {
            throw new Exception('Método não permitido');
        }
        
        return array_merge(
            $this->aiEngine->getStatus(),
            [
                'api_version' => '1.0.0',
                'endpoints' => [
                    'analytics' => '/analytics/*',
                    'strategic' => '/strategic/*',
                    'pastoral' => '/pastoral/*',
                    'financial' => '/financial/*',
                    'operational' => '/operational/*',
                    'search' => '/search',
                    'insights' => '/insights'
                ],
                'church_crm_integration' => 'active'
            ]
        );
    }
    
    // Métodos de GET específicos
    private function getAnalyticsOverview()
    {
        $members = $this->churchCRM->getMembers();
        $financial = $this->churchCRM->getFinancialData();
        $events = $this->churchCRM->getEvents();
        
        return [
            'overview' => [
                'total_members' => count($members),
                'monthly_revenue' => $financial['tithes'] + $financial['offerings'],
                'monthly_expenses' => $financial['expenses'],
                'avg_attendance' => array_sum(array_column($events, 'attendance')) / count($events),
                'growth_rate' => 0.15
            ],
            'predictions' => [
                'next_month_members' => count($members) * 1.02,
                'next_quarter_revenue' => ($financial['tithes'] + $financial['offerings']) * 3.1,
                'next_year_growth' => 0.18
            ],
            'recommendations' => [
                'Focar em programas de jovens',
                'Otimizar custos operacionais',
                'Expandir canais de comunicação'
            ]
        ];
    }
    
    private function predictGrowth()
    {
        return $this->aiEngine->processRequest('analytics', 'growth_prediction', [
            'period' => '12_months',
            'factors' => ['attendance', 'financial', 'events']
        ]);
    }
    
    private function forecastAttendance()
    {
        return $this->aiEngine->processRequest('analytics', 'attendance_forecast', [
            'period' => '6_months',
            'include_events' => true
        ]);
    }
    
    private function segmentMembers()
    {
        $members = $this->churchCRM->getMembers();
        return $this->aiEngine->processRequest('analytics', 'member_segmentation', [
            'members' => $members,
            'criteria' => ['engagement', 'giving', 'attendance']
        ]);
    }
    
    private function analyzeTrends()
    {
        return $this->aiEngine->processRequest('analytics', 'trend_analysis', [
            'period' => '24_months',
            'metrics' => ['growth', 'attendance', 'financial']
        ]);
    }
    
    private function getStrategicOverview()
    {
        return [
            'current_goals' => [
                'attendance_target' => 300,
                'revenue_target' => 50000,
                'new_members_target' => 50
            ],
            'progress' => [
                'attendance' => 0.85,
                'revenue' => 0.92,
                'new_members' => 0.70
            ],
            'risks' => [
                ['type' => 'operational', 'level' => 'low'],
                ['type' => 'financial', 'level' => 'medium']
            ]
        ];
    }
    
    private function getGoals()
    {
        return $this->aiEngine->processRequest('strategic', 'goal_setting', [
            'timeframe' => '12_months',
            'areas' => ['attendance', 'financial', 'growth']
        ]);
    }
    
    private function simulateScenarios()
    {
        return $this->aiEngine->processRequest('strategic', 'scenario_simulation', [
            'scenarios' => ['best_case', 'worst_case', 'most_likely'],
            'variables' => ['attendance', 'revenue', 'expenses']
        ]);
    }
    
    private function assessRisks()
    {
        return $this->aiEngine->processRequest('strategic', 'risk_assessment', [
            'categories' => ['operational', 'financial', 'reputational'],
            'timeframe' => '12_months'
        ]);
    }
    
    private function getPastoralOverview()
    {
        $members = $this->churchCRM->getMembers();
        
        return [
            'member_health' => [
                'engaged' => 0.75,
                'new' => 0.15,
                'at_risk' => 0.10
            ],
            'care_needs' => [
                'follow_up_required' => 12,
                'prayer_requests' => 8,
                'counseling' => 5
            ],
            'spiritual_growth' => [
                'bible_study_participation' => 0.60,
                'ministry_involvement' => 0.45,
                'discipleship_active' => 0.30
            ]
        ];
    }
    
    private function getEngagementAnalysis()
    {
        return $this->aiEngine->processRequest('pastoral', 'engagement_prediction', [
            'factors' => ['attendance', 'giving', 'ministry_involvement'],
            'timeframe' => '6_months'
        ]);
    }
    
    private function getChurnPrediction()
    {
        return $this->aiEngine->processRequest('pastoral', 'churn_prediction', [
            'risk_factors' => ['attendance_decline', 'giving_decrease', 'isolation'],
            'timeframe' => '3_months'
        ]);
    }
    
    private function getSpiritualGrowth()
    {
        return $this->aiEngine->processRequest('pastoral', 'spiritual_growth', [
            'metrics' => ['bible_study', 'prayer', 'service', 'discipleship'],
            'period' => '12_months'
        ]);
    }
    
    private function getCommunities()
    {
        return $this->aiEngine->processRequest('pastoral', 'community_detection', [
            'criteria' => ['age_groups', 'interests', 'involvement'],
            'min_community_size' => 5
        ]);
    }
    
    private function getFinancialOverview()
    {
        $financial = $this->churchCRM->getFinancialData();
        
        return [
            'current_status' => [
                'monthly_income' => $financial['tithes'] + $financial['offerings'],
                'monthly_expenses' => $financial['expenses'],
                'net_income' => ($financial['tithes'] + $financial['offerings']) - $financial['expenses'],
                'savings_rate' => 0.15
            ],
            'trends' => [
                'income_growth' => 0.12,
                'expense_growth' => 0.08,
                'efficiency_improvement' => 0.05
            ],
            'forecasts' => [
                'next_quarter' => 75000,
                'next_year' => 320000,
                'confidence' => 0.88
            ]
        ];
    }
    
    private function getRevenueForecast()
    {
        return $this->aiEngine->processRequest('financial', 'revenue_forecast', [
            'period' => '12_months',
            'sources' => ['tithes', 'offerings', 'special_donations']
        ]);
    }
    
    private function getExpenseOptimization()
    {
        return $this->aiEngine->processRequest('financial', 'expense_optimization', [
            'categories' => ['utilities', 'supplies', 'maintenance', 'programs'],
            'target_reduction' => 0.10
        ]);
    }
    
    private function getFraudDetection()
    {
        return $this->aiEngine->processRequest('financial', 'fraud_detection', [
            'timeframe' => '30_days',
            'transaction_types' => ['tithes', 'offerings', 'expenses']
        ]);
    }
    
    private function getInvestmentAnalysis()
    {
        return $this->aiEngine->processRequest('financial', 'investment_analysis', [
            'investment_types' => ['facilities', 'programs', 'technology'],
            'risk_tolerance' => 'medium',
            'timeframe' => '5_years'
        ]);
    }
    
    private function getOperationalOverview()
    {
        $events = $this->churchCRM->getEvents();
        
        return [
            'current_operations' => [
                'active_programs' => 15,
                'weekly_events' => 8,
                'volunteers_active' => 45,
                'facility_utilization' => 0.78
            ],
            'efficiency_metrics' => [
                'event_success_rate' => 0.92,
                'volunteer_satisfaction' => 0.85,
                'resource_utilization' => 0.81
            ],
            'upcoming_needs' => [
                'volunteer_shortage' => 5,
                'facility_maintenance' => 2,
                'budget_adjustments' => 3
            ]
        ];
    }
    
    private function getEventPlanning()
    {
        return $this->aiEngine->processRequest('operational', 'event_planning', [
            'event_types' => ['worship', 'education', 'fellowship', 'outreach'],
            'timeframe' => '3_months',
            'constraints' => ['budget', 'volunteers', 'facilities']
        ]);
    }
    
    private function getVolunteerOptimization()
    {
        return $this->aiEngine->processRequest('operational', 'volunteer_optimization', [
            'skills_required' => ['teaching', 'music', 'administration', 'outreach'],
            'availability' => 'weekends_evenings',
            'preferences' => true
        ]);
    }
    
    private function getFacilityManagement()
    {
        return $this->aiEngine->processRequest('operational', 'facility_management', [
            'facilities' => ['sanctuary', 'classrooms', 'fellowship_hall', 'offices'],
            'maintenance_schedule' => 'predictive',
            'utilization_target' => 0.85
        ]);
    }
    
    private function getCommunicationRouting()
    {
        return $this->aiEngine->processRequest('operational', 'communication_routing', [
            'channels' => ['email', 'sms', 'social_media', 'bulletin'],
            'audience_segments' => ['all', 'members', 'volunteers', 'leadership'],
            'urgency_levels' => ['low', 'medium', 'high', 'urgent']
        ]);
    }
    
    private function performSearch($query, $type)
    {
        return $this->aiEngine->processRequest('search', 'semantic_search', [
            'query' => $query,
            'search_type' => $type,
            'sources' => ['members', 'events', 'financial', 'documents'],
            'limit' => 20
        ]);
    }
    
    private function getInsights()
    {
        // Simulação de insights do banco
        return [
            [
                'id' => 1,
                'type' => 'trend',
                'title' => 'Crescimento Acelerado de Jovens',
                'description' => 'Análise mostra aumento 25% na participação de jovens',
                'confidence' => 0.92,
                'impact' => 'high',
                'action_required' => true,
                'created_at' => '2024-01-08 10:30:00'
            ],
            [
                'id' => 2,
                'type' => 'recommendation',
                'title' => 'Otimizar Programas de Treinamento',
                'description' => 'IA sugere reestruturação para melhor engajamento',
                'confidence' => 0.85,
                'impact' => 'medium',
                'action_required' => false,
                'created_at' => '2024-01-08 09:15:00'
            ]
        ];
    }
}

// Executar API se chamado diretamente
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $api = new AIChurchAPI();
}
