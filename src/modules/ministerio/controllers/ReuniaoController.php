<?php
/**
 * Controller de Reuniões do módulo Ministério
 * Gerencia reuniões e participantes
 */

require_once __DIR__ . '/../models/Reuniao.php';
require_once __DIR__ . '/../models/Ministerio.php';
require_once __DIR__ . '/../models/Mensagem.php';

class ReuniaoController
{
    /**
     * Listar reuniões
     */
    public static function index()
    {
        $ministerioId = $_GET['ministerio_id'] ?? null;
        $apenasFuturas = isset($_GET['futuras']) && $_GET['futuras'] == '1';
        
        $reunioes = ReuniaoModel::listar($ministerioId, $apenasFuturas);
        $ministerios = MinisterioModel::listar();
        
        require_once __DIR__ . '/../views/reuniao/index.php';
    }
    
    /**
     * Formulário de criação
     */
    public static function create()
    {
        $ministerios = MinisterioModel::listar();
        require_once __DIR__ . '/../views/reuniao/create.php';
    }
    
    /**
     * Salvar nova reunião
     */
    public static function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /modules/ministerio/reunioes.php');
            exit;
        }
        
        $dados = [
            'ministerio_id' => $_POST['ministerio_id'] ?? 0,
            'titulo' => $_POST['titulo'] ?? '',
            'descricao' => $_POST['descricao'] ?? '',
            'data_hora' => $_POST['data_hora'] ?? '',
            'duracao_minutos' => $_POST['duracao_minutos'] ?? 60,
            'local' => $_POST['local'] ?? '',
            'tipo' => $_POST['tipo'] ?? 'presencial',
            'criado_por' => $_SESSION['user']->getPerson()->getId()
        ];
        
        try {
            $id = ReuniaoModel::criar($dados);
            
            // Adicionar participantes automaticamente (todos os membros do ministério)
            $membros = MinisterioModel::listarMembros($dados['ministerio_id']);
            foreach ($membros as $membro) {
                ReuniaoModel::adicionarParticipante($id, $membro['membro_id']);
            }
            
            $_SESSION['success'] = 'Reunião criada com sucesso!';
            header('Location: /modules/ministerio/reunioes.php?view=' . $id);
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erro ao criar reunião: ' . $e->getMessage();
            header('Location: /modules/ministerio/reunioes.php?create');
        }
        exit;
    }
    
    /**
     * Visualizar reunião
     */
    public static function show($id)
    {
        $reuniao = ReuniaoModel::buscarPorId($id);
        if (!$reuniao) {
            $_SESSION['error'] = 'Reunião não encontrada';
            header('Location: /modules/ministerio/reunioes.php');
            exit;
        }
        
        $participantes = ReuniaoModel::listarParticipantes($id);
        $ministerio = MinisterioModel::buscarPorId($reuniao['ministerio_id']);
        
        require_once __DIR__ . '/../views/reuniao/show.php';
    }
    
    /**
     * Formulário de edição
     */
    public static function edit($id)
    {
        $reuniao = ReuniaoModel::buscarPorId($id);
        if (!$reuniao) {
            $_SESSION['error'] = 'Reunião não encontrada';
            header('Location: /modules/ministerio/reunioes.php');
            exit;
        }
        
        $ministerios = MinisterioModel::listar();
        require_once __DIR__ . '/../views/reuniao/edit.php';
    }
    
    /**
     * Atualizar reunião
     */
    public static function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /modules/ministerio/reunioes.php');
            exit;
        }
        
        $dados = [
            'titulo' => $_POST['titulo'] ?? '',
            'descricao' => $_POST['descricao'] ?? '',
            'data_hora' => $_POST['data_hora'] ?? '',
            'duracao_minutos' => $_POST['duracao_minutos'] ?? 60,
            'local' => $_POST['local'] ?? '',
            'tipo' => $_POST['tipo'] ?? 'presencial'
        ];
        
        try {
            ReuniaoModel::atualizar($id, $dados);
            $_SESSION['success'] = 'Reunião atualizada com sucesso!';
            header('Location: /modules/ministerio/reunioes.php?view=' . $id);
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erro ao atualizar reunião: ' . $e->getMessage();
            header('Location: /modules/ministerio/reunioes.php?edit=' . $id);
        }
        exit;
    }
    
    /**
     * Excluir reunião
     */
    public static function destroy($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /modules/ministerio/reunioes.php');
            exit;
        }
        
        try {
            ReuniaoModel::excluir($id);
            $_SESSION['success'] = 'Reunião excluída com sucesso!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erro ao excluir reunião: ' . $e->getMessage();
        }
        
        header('Location: /modules/ministerio/reunioes.php');
        exit;
    }
    
    /**
     * Gerenciar participantes
     */
    public static function participantes($id)
    {
        $reuniao = ReuniaoModel::buscarPorId($id);
        if (!$reuniao) {
            $_SESSION['error'] = 'Reunião não encontrada';
            header('Location: /modules/ministerio/reunioes.php');
            exit;
        }
        
        $participantes = ReuniaoModel::listarParticipantes($id);
        $membrosMinisterio = MinisterioModel::listarMembros($reuniao['ministerio_id']);
        
        // Filtrar membros que não são participantes ainda
        $membrosDisponiveis = [];
        $participantesIds = array_column($participantes, 'membro_id');
        
        foreach ($membrosMinisterio as $membro) {
            if (!in_array($membro['membro_id'], $participantesIds)) {
                $membrosDisponiveis[] = $membro;
            }
        }
        
        require_once __DIR__ . '/../views/reuniao/participantes.php';
    }
    
    /**
     * Adicionar participante
     */
    public static function adicionarParticipante($reuniaoId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /modules/ministerio/reunioes.php?participantes=' . $reuniaoId);
            exit;
        }
        
        $membroId = $_POST['membro_id'] ?? 0;
        
        try {
            ReuniaoModel::adicionarParticipante($reuniaoId, $membroId);
            $_SESSION['success'] = 'Participante adicionado com sucesso!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erro ao adicionar participante: ' . $e->getMessage();
        }
        
        header('Location: /modules/ministerio/reunioes.php?participantes=' . $reuniaoId);
        exit;
    }
    
    /**
     * Remover participante
     */
    public static function removerParticipante($reuniaoId, $participanteId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /modules/ministerio/reunioes.php?participantes=' . $reuniaoId);
            exit;
        }
        
        try {
            ReuniaoModel::removerParticipante($participanteId);
            $_SESSION['success'] = 'Participante removido com sucesso!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erro ao remover participante: ' . $e->getMessage();
        }
        
        header('Location: /modules/ministerio/reunioes.php?participantes=' . $reuniaoId);
        exit;
    }
    
    /**
     * Confirmação de presença via token (RSVP)
     */
    public static function rsvp($token)
    {
        $participante = ReuniaoModel::buscarParticipantePorToken($token);
        
        if (!$participante) {
            $_SESSION['error'] = 'Token inválido ou expirado';
            require_once __DIR__ . '/../views/reuniao/rsvp_erro.php';
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = $_POST['status'] ?? 'confirmado';
            $observacoes = $_POST['observacoes'] ?? '';
            
            try {
                ReuniaoModel::confirmarRSVP($token, $status, $observacoes);
                $_SESSION['success'] = 'Presença confirmada com sucesso!';
                require_once __DIR__ . '/../views/reuniao/rsvp_sucesso.php';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Erro ao confirmar presença: ' . $e->getMessage();
                require_once __DIR__ . '/../views/reuniao/rsvp_erro.php';
            }
            exit;
        }
        
        $reuniao = ReuniaoModel::buscarPorId($participante['reuniao_id']);
        require_once __DIR__ . '/../views/reuniao/rsvp.php';
    }
}