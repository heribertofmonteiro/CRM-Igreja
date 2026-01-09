<?php

/**
 * Sistema de RBAC (Role-Based Access Control) para o Módulo Ministério
 * 
 * Define permissões e verificações de acesso para as funcionalidades do módulo
 */

class MinisterioSecurity
{
    // Definição de permissões
    const PERM_VER_MINISTERIOS = 'ministerio_ver';
    const PERM_CRIAR_MINISTERIO = 'ministerio_criar';
    const PERM_EDITAR_MINISTERIO = 'ministerio_editar';
    const PERM_EXCLUIR_MINISTERIO = 'ministerio_excluir';
    const PERM_GERENCIAR_MEMBROS = 'ministerio_membros';
    const PERM_ADICIONAR_MEMBRO = 'ministerio_adicionar_membro';
    const PERM_REMOVER_MEMBRO = 'ministerio_remover_membro';
    const PERM_GERENCIAR_REUNIOES = 'ministerio_reunioes';
    const PERM_ENVIAR_MENSAGENS = 'ministerio_mensagens';
    const PERM_VER_DASHBOARD = 'ministerio_dashboard';
    
    // Definição de papéis
    const ROLE_ADMIN = 'admin';
    const ROLE_LIDER = 'lider';
    const ROLE_COORDENADOR = 'coordenador';
    const ROLE_MEMBRO = 'membro';
    const ROLE_CONVIDADO = 'convidado';
    
    /**
     * Mapeamento de permissões por papel
     */
    private static $permissoesPorPapel = [
        self::ROLE_ADMIN => [
            self::PERM_VER_MINISTERIOS,
            self::PERM_CRIAR_MINISTERIO,
            self::PERM_EDITAR_MINISTERIO,
            self::PERM_EXCLUIR_MINISTERIO,
            self::PERM_GERENCIAR_MEMBROS,
            self::PERM_ADICIONAR_MEMBRO,
            self::PERM_REMOVER_MEMBRO,
            self::PERM_GERENCIAR_REUNIOES,
            self::PERM_ENVIAR_MENSAGENS,
            self::PERM_VER_DASHBOARD
        ],
        self::ROLE_LIDER => [
            self::PERM_VER_MINISTERIOS,
            self::PERM_EDITAR_MINISTERIO,
            self::PERM_GERENCIAR_MEMBROS,
            self::PERM_ADICIONAR_MEMBRO,
            self::PERM_REMOVER_MEMBRO,
            self::PERM_GERENCIAR_REUNIOES,
            self::PERM_ENVIAR_MENSAGENS,
            self::PERM_VER_DASHBOARD
        ],
        self::ROLE_COORDENADOR => [
            self::PERM_VER_MINISTERIOS,
            self::PERM_GERENCIAR_MEMBROS,
            self::PERM_GERENCIAR_REUNIOES,
            self::PERM_ENVIAR_MENSAGENS,
            self::PERM_VER_DASHBOARD
        ],
        self::ROLE_MEMBRO => [
            self::PERM_VER_MINISTERIOS,
            self::PERM_VER_DASHBOARD
        ],
        self::ROLE_CONVIDADO => [
            self::PERM_VER_MINISTERIOS,
            self::PERM_VER_DASHBOARD
        ]
    ];
    
    /**
     * Verificar se um papel existe
     */
    public static function papelExiste($papel)
    {
        $papeisValidos = [
            self::ROLE_ADMIN,
            self::ROLE_LIDER,
            self::ROLE_COORDENADOR,
            self::ROLE_MEMBRO,
            self::ROLE_CONVIDADO
        ];
        
        return in_array($papel, $papeisValidos);
    }
    
    /**
     * Verificar se o usuário pode acessar um recurso específico
     */
    public static function podeAcessar($recurso, $acao = null)
    {
        // Mapear recursos para permissões
        $mapeamento = [
            'ministerio' => [
                'ver' => self::PERM_VER_MINISTERIOS,
                'criar' => self::PERM_CRIAR_MINISTERIO,
                'editar' => self::PERM_EDITAR_MINISTERIO,
                'excluir' => self::PERM_EXCLUIR_MINISTERIO
            ],
            'mensagem' => [
                'ver' => self::PERM_VER_MINISTERIOS,
                'enviar' => self::PERM_ENVIAR_MENSAGENS
            ],
            'dashboard' => [
                'ver' => self::PERM_VER_DASHBOARD
            ]
        ];
        
        if (!isset($mapeamento[$recurso])) {
            return false;
        }
        
        if ($acao && isset($mapeamento[$recurso][$acao])) {
            return self::temPermissao($mapeamento[$recurso][$acao]);
        }
        
        // Se não especificar ação, verificar qualquer permissão do recurso
        foreach ($mapeamento[$recurso] as $permissao) {
            if (self::temPermissao($permissao)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Verifica se o usuário atual tem permissão para uma ação
     */
    public static function temPermissao($permissao)
    {
        if (!isset($_SESSION['user'])) {
            return false;
        }
        
        $usuarioId = $_SESSION['user']->getPerson()->getId();
        $papelUsuario = self::getPapelUsuario($usuarioId);
        
        if (!isset(self::$permissoesPorPapel[$papelUsuario])) {
            return false;
        }
        
        return in_array($permissao, self::$permissoesPorPapel[$papelUsuario]);
    }
    
    /**
     * Obtém o papel do usuário no sistema
     */
    public static function getPapelUsuario($usuarioId)
    {
        // Simulação - em um sistema real, isso viria do banco
        if ($usuarioId == 1) {
            return self::ROLE_ADMIN;
        } elseif ($usuarioId == 2) {
            return self::ROLE_LIDER;
        } elseif ($usuarioId == 3) {
            return self::ROLE_COORDENADOR;
        } else {
            return self::ROLE_MEMBRO;
        }
    }
    
    /**
     * Verifica se o usuário pode gerenciar um ministério específico
     */
    public static function podeGerenciarMinisterio($ministerioId, $usuarioId = null)
    {
        if ($usuarioId === null) {
            $usuarioId = $_SESSION['user']->getPerson()->getId();
        }
        
        // Admin pode gerenciar qualquer ministério
        if (self::temPermissao(self::PERM_EXCLUIR_MINISTERIO)) {
            return true;
        }
        
        // Verificar se é líder do ministério
        $pdo = new PDO(
            'mysql:host=localhost;dbname=autonomo;charset=utf8mb4',
            'heriberto',
            '0631'
        );
        
        $stmt = $pdo->prepare("
            SELECT lider_id, coordenador_id 
            FROM ministerios 
            WHERE id = :id AND ativo = 1
        ");
        $stmt->execute([':id' => $ministerioId]);
        $ministerio = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$ministerio) {
            return false;
        }
        
        return $ministerio['lider_id'] == $usuarioId || 
               $ministerio['coordenador_id'] == $usuarioId;
    }
    
    /**
     * Verifica se o usuário é membro de um ministério
     */
    public static function ehMembroMinisterio($ministerioId, $usuarioId = null)
    {
        if ($usuarioId === null) {
            $usuarioId = $_SESSION['user']->getPerson()->getId();
        }
        
        $pdo = new PDO(
            'mysql:host=localhost;dbname=autonomo;charset=utf8mb4',
            'heriberto',
            '0631'
        );
        
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total 
            FROM ministerio_membros 
            WHERE ministerio_id = :ministerio_id 
              AND membro_id = :usuario_id 
              AND ativo = 1
        ");
        $stmt->execute([
            ':ministerio_id' => $ministerioId,
            ':usuario_id' => $usuarioId
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }
    
    /**
     * Middleware para verificar permissão antes de executar ação
     */
    public static function requerPermissao($permissao, $redirecionarPara = '/v2/login.php')
    {
        if (!self::temPermissao($permissao)) {
            $_SESSION['error'] = 'Você não tem permissão para acessar esta funcionalidade.';
            header('Location: ' . $redirecionarPara);
            exit;
        }
    }
    
    /**
     * Middleware para verificar se usuário pode gerenciar ministério
     */
    public static function requerGerenciamentoMinisterio($ministerioId, $redirecionarPara = null)
    {
        if (!self::podeGerenciarMinisterio($ministerioId)) {
            $_SESSION['error'] = 'Você não tem permissão para gerenciar este ministério.';
            if ($redirecionarPara) {
                header('Location: ' . $redirecionarPara);
            } else {
                header('Location: index.php');
            }
            exit;
        }
    }
    
    /**
     * Middleware para verificar se usuário é membro do ministério
     */
    public static function requerMembroMinisterio($ministerioId, $redirecionarPara = null)
    {
        if (!self::ehMembroMinisterio($ministerioId)) {
            $_SESSION['error'] = 'Você não é membro deste ministério.';
            if ($redirecionarPara) {
                header('Location: ' . $redirecionarPara);
            } else {
                header('Location: index.php');
            }
            exit;
        }
    }
    
    /**
     * Obtém todas as permissões de um usuário
     */
    public static function getPermissoesUsuario($usuarioId = null)
    {
        if ($usuarioId === null) {
            $usuarioId = $_SESSION['user']->getPerson()->getId();
        }
        
        $papel = self::getPapelUsuario($usuarioId);
        return self::$permissoesPorPapel[$papel] ?? [];
    }
    
    /**
     * Retorna o nome formatado do papel
     */
    public static function getNomePapel($papel)
    {
        $nomes = [
            self::ROLE_ADMIN => 'Administrador',
            self::ROLE_LIDER => 'Líder de Ministério',
            self::ROLE_COORDENADOR => 'Coordenador',
            self::ROLE_MEMBRO => 'Membro',
            self::ROLE_CONVIDADO => 'Convidado'
        ];
        
        return $nomes[$papel] ?? 'Desconhecido';
    }
}