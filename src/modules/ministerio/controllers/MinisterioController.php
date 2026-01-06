<?php
/**
 * Controller principal do módulo Ministério
 * Gerencia a interface administrativa de ministérios
 */

require_once __DIR__ . '/../models/Ministerio.php';
require_once __DIR__ . '/../models/Reuniao.php';
require_once __DIR__ . '/../models/Mensagem.php';

class MinisterioController
{
    /**
     * Listar todos os ministérios
     */
    public static function index()
    {
        $ministerios = MinisterioModel::listar();
        require_once __DIR__ . '/../views/ministerio/index.php';
    }
    
    /**
     * Formulário de criação de ministério
     */
    public static function create()
    {
        require_once __DIR__ . '/../views/ministerio/create.php';
    }
    
    /**
     * Salvar novo ministério
     */
    public static function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /modules/ministerio/index.php');
            exit;
        }
        
        $dados = [
            'nome' => $_POST['nome'] ?? '',
            'descricao' => $_POST['descricao'] ?? '',
            'lider_id' => $_SESSION['user']->getPerson()->getId(),
            'status' => $_POST['status'] ?? 'ativo'
        ];
        
        try {
            $id = MinisterioModel::criar($dados);
            $_SESSION['success'] = 'Ministério criado com sucesso!';
            header('Location: /modules/ministerio/views.php?id=' . $id);
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erro ao criar ministério: ' . $e->getMessage();
            header('Location: /modules/ministerio/create.php');
        }
        exit;
    }
    
    /**
     * Visualizar detalhes do ministério
     */
    public static function show($id)
    {
        $ministerio = MinisterioModel::buscarPorId($id);
        if (!$ministerio) {
            $_SESSION['error'] = 'Ministério não encontrado';
            header('Location: /modules/ministerio/index.php');
            exit;
        }
        
        $membros = MinisterioModel::listarMembros($id);
        $reunioes = ReuniaoModel::listar($id, true); // apenas futuras
        $mensagens = MensagemModel::listar(['ministerio_id' => $id]);
        
        require_once __DIR__ . '/../views/ministerio/show.php';
    }
    
    /**
     * Formulário de edição
     */
    public static function edit($id)
    {
        $ministerio = MinisterioModel::buscarPorId($id);
        if (!$ministerio) {
            $_SESSION['error'] = 'Ministério não encontrado';
            header('Location: /modules/ministerio/index.php');
            exit;
        }
        
        require_once __DIR__ . '/../views/ministerio/edit.php';
    }
    
    /**
     * Atualizar ministério
     */
    public static function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /modules/ministerio/index.php');
            exit;
        }
        
        $dados = [
            'nome' => $_POST['nome'] ?? '',
            'descricao' => $_POST['descricao'] ?? '',
            'status' => $_POST['status'] ?? 'ativo'
        ];
        
        try {
            MinisterioModel::atualizar($id, $dados);
            $_SESSION['success'] = 'Ministério atualizado com sucesso!';
            header('Location: /modules/ministerio/views.php?id=' . $id);
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erro ao atualizar ministério: ' . $e->getMessage();
            header('Location: /modules/ministerio/edit.php?id=' . $id);
        }
        exit;
    }
    
    /**
     * Excluir ministério
     */
    public static function destroy($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /modules/ministerio/index.php');
            exit;
        }
        
        try {
            MinisterioModel::excluir($id);
            $_SESSION['success'] = 'Ministério excluído com sucesso!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erro ao excluir ministério: ' . $e->getMessage();
        }
        
        header('Location: /modules/ministerio/index.php');
        exit;
    }
    
    /**
     * Gerenciar membros do ministério
     */
    public static function membros($id)
    {
        $ministerio = MinisterioModel::buscarPorId($id);
        if (!$ministerio) {
            $_SESSION['error'] = 'Ministério não encontrado';
            header('Location: /modules/ministerio/index.php');
            exit;
        }
        
        $membros = MinisterioModel::listarMembros($id);
        $pessoasDisponiveis = MinisterioModel::listarPessoasNaoMembros($id);
        
        require_once __DIR__ . '/../views/ministerio/membros.php';
    }
    
    /**
     * Adicionar membro
     */
    public static function adicionarMembro($ministerioId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /modules/ministerio/membros.php?id=' . $ministerioId);
            exit;
        }
        
        $membroId = $_POST['membro_id'] ?? 0;
        $funcao = $_POST['funcao'] ?? 'membro';
        
        try {
            MinisterioModel::adicionarMembro($ministerioId, $membroId, $funcao);
            $_SESSION['success'] = 'Membro adicionado com sucesso!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erro ao adicionar membro: ' . $e->getMessage();
        }
        
        header('Location: /modules/ministerio/membros.php?id=' . $ministerioId);
        exit;
    }
    
    /**
     * Remover membro
     */
    public static function removerMembro($ministerioId, $membroId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /modules/ministerio/membros.php?id=' . $ministerioId);
            exit;
        }
        
        try {
            MinisterioModel::removerMembro($ministerioId, $membroId);
            $_SESSION['success'] = 'Membro removido com sucesso!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erro ao remover membro: ' . $e->getMessage();
        }
        
        header('Location: /modules/ministerio/membros.php?id=' . $ministerioId);
        exit;
    }
    
    /**
     * Dashboard do ministério
     */
    public static function dashboard()
    {
        $estatisticas = [
            'total_ministerios' => MinisterioModel::contarTotal(),
            'total_membros' => MinisterioModel::contarTotalMembros(),
            'proximas_reunioes' => ReuniaoModel::contarProximas(),
            'mensagens_pendentes' => MensagemModel::contarPendentes()
        ];
        
        require_once __DIR__ . '/../views/ministerio/dashboard.php';
    }
}