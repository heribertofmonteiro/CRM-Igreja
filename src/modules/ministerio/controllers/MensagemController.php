<?php

require_once __DIR__ . '/../models/Mensagem.php';
require_once __DIR__ . '/../Security.php';

/**
 * Controller para gestão de mensagens do módulo Ministério
 */
class MensagemController
{
    /**
     * Listar todas as mensagens
     */
    public static function index()
    {
        // Verificar permissão
        MinisterioSecurity::requerPermissao(MinisterioSecurity::PERM_ENVIAR_MENSAGENS);
        
        $ministerios = MensagemModel::listarMinisterios();
        $mensagens = MensagemModel::listar();
        
        require_once __DIR__ . '/../views/mensagem/index.php';
    }
    
    /**
     * Formulário de criação de mensagem
     */
    public static function create()
    {
        MinisterioSecurity::requerPermissao(MinisterioSecurity::PERM_ENVIAR_MENSAGENS);
        
        $ministerios = MensagemModel::listarMinisterios();
        require_once __DIR__ . '/../views/mensagem/create.php';
    }
    
    /**
     * Salvar nova mensagem
     */
    public static function store()
    {
        MinisterioSecurity::requerPermissao(MinisterioSecurity::PERM_ENVIAR_MENSAGENS);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }
        
        $dados = [
            'ministerio_id' => $_POST['ministerio_id'] ?? 0,
            'reuniao_id' => $_POST['reuniao_id'] ?? null,
            'tipo' => $_POST['tipo'] ?? 'geral',
            'assunto' => $_POST['assunto'] ?? '',
            'conteudo' => $_POST['conteudo'] ?? '',
            'canal' => $_POST['canal'] ?? 'email',
            'data_agendamento' => !empty($_POST['data_agendamento']) ? $_POST['data_agendamento'] : null,
            'criado_por' => $_SESSION['user']->getPerson()->getId()
        ];
        
        try {
            $id = MensagemModel::criar($dados);
            $_SESSION['success'] = 'Mensagem criada com sucesso!';
            
            // Se não for agendada, enviar imediatamente
            if (!$dados['data_agendamento']) {
                MensagemModel::processarEnvio($id);
            }
            
            header('Location: index.php');
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erro ao criar mensagem: ' . $e->getMessage();
            header('Location: create.php');
        }
        exit;
    }
    
    /**
     * Visualizar mensagem
     */
    public static function show($id)
    {
        MinisterioSecurity::requerPermissao(MinisterioSecurity::PERM_ENVIAR_MENSAGENS);
        
        $mensagem = MensagemModel::buscarPorId($id);
        if (!$mensagem) {
            $_SESSION['error'] = 'Mensagem não encontrada';
            header('Location: index.php');
            exit;
        }
        
        $envios = MensagemModel::listarEnvios($id);
        require_once __DIR__ . '/../views/mensagem/show.php';
    }
    
    /**
     * Enviar mensagem imediatamente
     */
    public static function enviar($id)
    {
        MinisterioSecurity::requerPermissao(MinisterioSecurity::PERM_ENVIAR_MENSAGENS);
        
        try {
            MensagemModel::processarEnvio($id);
            $_SESSION['success'] = 'Mensagem enviada com sucesso!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erro ao enviar mensagem: ' . $e->getMessage();
        }
        
        header('Location: index.php');
        exit;
    }
    
    /**
     * Cancelar mensagem agendada
     */
    public static function cancelar($id)
    {
        MinisterioSecurity::requerPermissao(MinisterioSecurity::PERM_ENVIAR_MENSAGENS);
        
        try {
            MensagemModel::cancelar($id);
            $_SESSION['success'] = 'Mensagem cancelada com sucesso!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erro ao cancelar mensagem: ' . $e->getMessage();
        }
        
        header('Location: index.php');
        exit;
    }
    
    /**
     * Excluir mensagem
     */
    public static function destroy($id)
    {
        MinisterioSecurity::requerPermissao(MinisterioSecurity::PERM_ENVIAR_MENSAGENS);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }
        
        try {
            MensagemModel::excluir($id);
            $_SESSION['success'] = 'Mensagem excluída com sucesso!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erro ao excluir mensagem: ' . $e->getMessage();
        }
        
        header('Location: index.php');
        exit;
    }
    
    /**
     * API para buscar destinatários
     */
    public static function apiDestinatarios()
    {
        header('Content-Type: application/json');
        
        try {
            $ministerioId = $_GET['ministerio_id'] ?? 0;
            $destinatarios = MensagemModel::listarDestinatarios($ministerioId);
            
            echo json_encode([
                'success' => true,
                'data' => $destinatarios
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * API para preview da mensagem
     */
    public static function apiPreview()
    {
        header('Content-Type: application/json');
        
        try {
            $conteudo = $_POST['conteudo'] ?? '';
            $canal = $_POST['canal'] ?? 'email';
            
            $preview = MensagemModel::gerarPreview($conteudo, $canal);
            
            echo json_encode([
                'success' => true,
                'preview' => $preview
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}