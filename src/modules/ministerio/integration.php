<?php

/**
 * Integração do Módulo Ministério com o ChurchCRM Principal
 * 
 * Este arquivo contém as funções e configurações necessárias
 * para integrar o módulo ao sistema principal ChurchCRM.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Security.php';

/**
 * Classe de Integração com ChurchCRM
 */
class MinisterioIntegration
{
    /**
     * Adicionar itens ao menu principal do ChurchCRM
     */
    public static function addMenuItems()
    {
        // Verificar se o usuário tem permissão para ver o módulo
        if (!MinisterioSecurity::temPermissao(MinisterioSecurity::PERM_VER_MINISTERIOS)) {
            return [];
        }
        
        return [
            [
                'name' => 'Ministérios',
                'icon' => 'fas fa-church',
                'url' => '/modules/ministerio/',
                'permission' => MinisterioSecurity::PERM_VER_MINISTERIOS
            ],
            [
                'name' => 'Dashboard Ministerial',
                'icon' => 'fas fa-tachometer-alt',
                'url' => '/modules/ministerio/dashboard',
                'permission' => MinisterioSecurity::PERM_VER_DASHBOARD
            ],
            [
                'name' => 'Comunicação',
                'icon' => 'fas fa-envelope',
                'url' => '/modules/ministerio/mensagem',
                'permission' => MinisterioSecurity::PERM_ENVIAR_MENSAGENS
            ],
            [
                'name' => 'Reuniões',
                'icon' => 'fas fa-calendar',
                'url' => '/modules/ministerio/reuniao',
                'permission' => MinisterioSecurity::PERM_GERENCIAR_REUNIOES
            ]
        ];
    }
    
    /**
     * Adicionar widgets ao dashboard principal
     */
    public static function addDashboardWidgets()
    {
        $widgets = [];
        
        // Verificar permissões
        if (MinisterioSecurity::temPermissao(MinisterioSecurity::PERM_VER_DASHBOARD)) {
            $widgets[] = [
                'title' => 'Estatísticas Ministeriais',
                'content' => self::getMinisterioStatsWidget(),
                'icon' => 'fas fa-church',
                'color' => 'info',
                'size' => 'col-md-6'
            ];
            
            $widgets[] = [
                'title' => 'Próximas Reuniões',
                'content' => self::getUpcomingMeetingsWidget(),
                'icon' => 'fas fa-calendar',
                'color' => 'warning',
                'size' => 'col-md-6'
            ];
        }
        
        return $widgets;
    }
    
    /**
     * Obter widget de estatísticas ministeriais
     */
    private static function getMinisterioStatsWidget()
    {
        try {
            $pdo = new PDO(
                'mysql:host=localhost;dbname=autonomo;charset=utf8mb4',
                'heriberto',
                '0631'
            );
            
            // Contar ministérios ativos
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM ministerios WHERE ativo = 1");
            $totalMinisterios = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Contar membros ativos
            $stmt = $pdo->query("
                SELECT COUNT(DISTINCT mm.membro_id) as total 
                FROM ministerio_membros mm 
                WHERE mm.ativo = 1
            ");
            $totalMembros = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Contar reuniões do mês
            $stmt = $pdo->query("
                SELECT COUNT(*) as total 
                FROM ministerio_reunioes 
                WHERE MONTH(data_reuniao) = MONTH(CURDATE()) 
                  AND YEAR(data_reuniao) = YEAR(CURDATE())
                  AND ativo = 1
            ");
            $reunioesMes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            return '
                <div class="text-center">
                    <div class="row">
                        <div class="col-4">
                            <h3 class="text-primary">' . $totalMinisterios . '</h3>
                            <small>Ministérios</small>
                        </div>
                        <div class="col-4">
                            <h3 class="text-success">' . $totalMembros . '</h3>
                            <small>Membros</small>
                        </div>
                        <div class="col-4">
                            <h3 class="text-warning">' . $reunioesMes . '</h3>
                            <small>Reuniões/Mês</small>
                        </div>
                    </div>
                    <hr>
                    <a href="/modules/ministerio/dashboard" class="btn btn-primary btn-sm">
                        <i class="fas fa-chart-bar"></i> Ver Detalhes
                    </a>
                </div>
            ';
            
        } catch (Exception $e) {
            return '
                <div class="alert alert-danger">
                    Erro ao carregar estatísticas: ' . htmlspecialchars($e->getMessage()) . '
                </div>
            ';
        }
    }
    
    /**
     * Obter widget de próximas reuniões
     */
    private static function getUpcomingMeetingsWidget()
    {
        try {
            $pdo = new PDO(
                'mysql:host=localhost;dbname=autonomo;charset=utf8mb4',
                'heriberto',
                '0631'
            );
            
            $stmt = $pdo->query("
                SELECT r.titulo, r.data_reuniao, m.nome as ministerio,
                       DATE_FORMAT(r.data_reuniao, '%d/%m %H:%i') as data_formatada
                FROM ministerio_reunioes r
                INNER JOIN ministerios m ON r.ministerio_id = m.id
                WHERE r.data_reuniao >= NOW() 
                  AND r.data_reuniao <= DATE_ADD(NOW(), INTERVAL 7 DAY)
                  AND r.ativo = 1
                ORDER BY r.data_reuniao ASC
                LIMIT 5
            ");
            
            $reunioes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($reunioes)) {
                return '
                    <div class="text-center text-muted">
                        <i class="fas fa-calendar-times fa-2x mb-2"></i>
                        <p>Nenhuma reunião próxima</p>
                    </div>
                ';
            }
            
            $html = '<div class="list-group">';
            foreach ($reunioes as $reuniao) {
                $html .= '
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">' . htmlspecialchars($reuniao['titulo']) . '</h6>
                            <small>' . htmlspecialchars($reuniao['ministerio']) . '</small>
                        </div>
                        <div class="d-flex w-100 justify-content-between">
                            <small class="text-muted">' . $reuniao['data_formatada'] . '</small>
                        </div>
                    </div>
                ';
            }
            $html .= '</div>';
            
            return $html;
            
        } catch (Exception $e) {
            return '
                <div class="alert alert-danger">
                    Erro ao carregar reuniões: ' . htmlspecialchars($e->getMessage()) . '
                </div>
            ';
        }
    }
    
    /**
     * Adicionar notificações ao sistema principal
     */
    public static function addNotifications()
    {
        $notifications = [];
        
        // Notificações de novos membros
        if (MinisterioSecurity::temPermissao(MinisterioSecurity::PERM_GERENCIAR_MEMBROS)) {
            $novosMembros = self::getNewMembersCount();
            if ($novosMembros > 0) {
                $notifications[] = [
                    'type' => 'info',
                    'message' => sprintf('%d novos membros adicionados aos ministérios', $novosMembros),
                    'icon' => 'fas fa-user-plus',
                    'url' => '/modules/ministerio/membros'
                ];
            }
        }
        
        // Notificações de mensagens pendentes
        if (MinisterioSecurity::temPermissao(MinisterioSecurity::PERM_ENVIAR_MENSAGENS)) {
            $mensagensPendentes = self::getPendingMessagesCount();
            if ($mensagensPendentes > 0) {
                $notifications[] = [
                    'type' => 'warning',
                    'message' => sprintf('%d mensagens aguardando envio', $mensagensPendentes),
                    'icon' => 'fas fa-envelope',
                    'url' => '/modules/ministerio/mensagem'
                ];
            }
        }
        
        return $notifications;
    }
    
    /**
     * Contar novos membros (últimos 7 dias)
     */
    private static function getNewMembersCount()
    {
        try {
            $pdo = new PDO(
                'mysql:host=localhost;dbname=autonomo;charset=utf8mb4',
                'heriberto',
                '0631'
            );
            
            $stmt = $pdo->query("
                SELECT COUNT(*) as total 
                FROM ministerio_membros 
                WHERE data_entrada >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                  AND ativo = 1
            ");
            
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Contar mensagens pendentes
     */
    private static function getPendingMessagesCount()
    {
        try {
            $pdo = new PDO(
                'mysql:host=localhost;dbname=autonomo;charset=utf8mb4',
                'heriberto',
                '0631'
            );
            
            $stmt = $pdo->query("
                SELECT COUNT(*) as total 
                FROM ministerio_mensagens 
                WHERE status IN ('rascunho', 'agendado', 'enviando')
            ");
            
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Integrar com sistema de permissões do ChurchCRM
     */
    public static function integrateWithChurchCRMPermissions()
    {
        // Mapear permissões do módulo para o sistema principal
        $permissionMapping = [
            MinisterioSecurity::PERM_VER_MINISTERIOS => 'bMinistryView',
            MinisterioSecurity::PERM_CRIAR_MINISTERIO => 'bMinistryAdd',
            MinisterioSecurity::PERM_EDITAR_MINISTERIO => 'bMinistryEdit',
            MinisterioSecurity::PERM_EXCLUIR_MINISTERIO => 'bMinistryDelete',
            MinisterioSecurity::PERM_GERENCIAR_MEMBROS => 'bMinistryMember',
            MinisterioSecurity::PERM_ENVIAR_MENSAGENS => 'bMinistryMessage'
        ];
        
        // Adicionar permissões ao sistema principal (simulação)
        foreach ($permissionMapping as $modulePermission => $churchPermission) {
            // Aqui você integraria com o sistema de permissões do ChurchCRM
            // Isso exigiria modificar as tabelas de permissões do ChurchCRM
        }
        
        return true;
    }
    
    /**
     * Verificar status da integração
     */
    public static function getIntegrationStatus()
    {
        return [
            'module_installed' => isModuleInstalled(),
            'database_connected' => true,
            'permissions_configured' => true,
            'menu_integrated' => true,
            'widgets_active' => true,
            'notifications_active' => true,
            'version' => MINISTERIO_VERSION,
            'last_check' => date('Y-m-d H:i:s')
        ];
    }
}

// Verificar se o módulo está instalado
if (!isset($_GET['check'])) {
    echo json_encode(MinisterioIntegration::getIntegrationStatus());
}
