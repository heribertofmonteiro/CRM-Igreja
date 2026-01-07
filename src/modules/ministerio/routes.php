<?php

/**
 * Rotas do Módulo Ministério & Comunicação
 * Define as URLs e middlewares para o módulo
 */

// Incluir controllers
require_once __DIR__ . '/controllers/MinisterioController.php';
require_once __DIR__ . '/controllers/MensagemController.php';
require_once __DIR__ . '/controllers/ReuniaoController.php';

// Middleware de verificação de autenticação
function requireAuth() {
    if (!isset($_SESSION['user'])) {
        header('Location: /v2/login.php');
        exit;
    }
}

// Mapeamento de rotas
$routes = [
    // Rotas de Ministérios
    'ministerio' => [
        'GET' => 'MinisterioController::index',
        'POST' => 'MinisterioController::store'
    ],
    'ministerio/create' => [
        'GET' => 'MinisterioController::create'
    ],
    'ministerio/show' => [
        'GET' => 'MinisterioController::show'
    ],
    'ministerio/edit' => [
        'GET' => 'MinisterioController::edit'
    ],
    'ministerio/update' => [
        'POST' => 'MinisterioController::update'
    ],
    'ministerio/destroy' => [
        'POST' => 'MinisterioController::destroy'
    ],
    'ministerio/membros' => [
        'GET' => 'MinisterioController::membros'
    ],
    'ministerio/adicionar-membro' => [
        'POST' => 'MinisterioController::adicionarMembro'
    ],
    'ministerio/remover-membro' => [
        'POST' => 'MinisterioController::removerMembro'
    ],
    'ministerio/dashboard' => [
        'GET' => 'MinisterioController::dashboard'
    ],
    
    // Rotas de Mensagens
    'mensagem' => [
        'GET' => 'MensagemController::index',
        'POST' => 'MensagemController::store'
    ],
    'mensagem/create' => [
        'GET' => 'MensagemController::create'
    ],
    'mensagem/show' => [
        'GET' => 'MensagemController::show'
    ],
    'mensagem/enviar' => [
        'POST' => 'MensagemController::enviar'
    ],
    'mensagem/cancelar' => [
        'POST' => 'MensagemController::cancelar'
    ],
    'mensagem/destroy' => [
        'POST' => 'MensagemController::destroy'
    ],
    'mensagem/api/destinatarios' => [
        'GET' => 'MensagemController::apiDestinatarios'
    ],
    'mensagem/api/preview' => [
        'POST' => 'MensagemController::apiPreview'
    ],
    
    // Rotas de Reuniões
    'reuniao' => [
        'GET' => 'ReuniaoController::index',
        'POST' => 'ReuniaoController::store'
    ],
    'reuniao/create' => [
        'GET' => 'ReuniaoController::create'
    ],
    'reuniao/show' => [
        'GET' => 'ReuniaoController::show'
    ],
    'reuniao/edit' => [
        'GET' => 'ReuniaoController::edit'
    ],
    'reuniao/update' => [
        'POST' => 'ReuniaoController::update'
    ],
    'reuniao/destroy' => [
        'POST' => 'ReuniaoController::destroy'
    ],
    'reuniao/participantes' => [
        'GET' => 'ReuniaoController::participantes'
    ],
    'reuniao/rsvp' => [
        'POST' => 'ReuniaoController::rsvp'
    ]
];

// Função para processar as rotas
function processRoute($requestUri, $requestMethod) {
    global $routes;
    
    // Limpar URI
    $requestUri = parse_url($requestUri, PHP_URL_PATH);
    $requestUri = rtrim($requestUri, '/');
    
    foreach ($routes as $route => $methods) {
        if ($requestUri === $route) {
            if (isset($methods[$requestMethod])) {
                $controllerMethod = $methods[$requestMethod];
                
                // Extrair parâmetros da URL
                $params = [];
                if ($requestMethod === 'GET' && strpos($requestUri, '/show/') !== false) {
                    $parts = explode('/', $requestUri);
                    $params['id'] = end($parts);
                } elseif ($requestMethod === 'GET' && strpos($requestUri, '/edit/') !== false) {
                    $parts = explode('/', $requestUri);
                    $params['id'] = end($parts);
                } elseif ($requestMethod === 'POST' && strpos($requestUri, '/update/') !== false) {
                    $parts = explode('/', $requestUri);
                    $params['id'] = end($parts);
                } elseif ($requestMethod === 'POST' && strpos($requestUri, '/destroy/') !== false) {
                    $parts = explode('/', $requestUri);
                    $params['id'] = end($parts);
                } elseif ($requestMethod === 'POST' && strpos($requestUri, '/membros/') !== false) {
                    $parts = explode('/', $requestUri);
                    $params['id'] = end($parts);
                } elseif ($requestMethod === 'POST' && strpos($requestUri, '/adicionar-membro/') !== false) {
                    $parts = explode('/', $requestUri);
                    $params['id'] = end($parts);
                } elseif ($requestMethod === 'POST' && strpos($requestUri, '/remover-membro/') !== false) {
                    $parts = explode('/', $requestUri);
                    $params['id'] = end($parts);
                    $params['membroId'] = $_POST['membro_id'] ?? 0;
                } elseif ($requestMethod === 'POST' && strpos($requestUri, '/enviar/') !== false) {
                    $parts = explode('/', $requestUri);
                    $params['id'] = end($parts);
                } elseif ($requestMethod === 'POST' && strpos($requestUri, '/cancelar/') !== false) {
                    $parts = explode('/', $requestUri);
                    $params['id'] = end($parts);
                } elseif ($requestMethod === 'POST' && strpos($requestUri, '/destroy/') !== false) {
                    $parts = explode('/', $requestUri);
                    $params['id'] = end($parts);
                }
                
                // Executar método do controller com parâmetros
                if (!empty($params)) {
                    call_user_func($controllerMethod, $params);
                } else {
                    call_user_func($controllerMethod);
                }
                
                return true;
            }
        }
    }
    
    return false;
}

// Processar requisição atual
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if (!processRoute($requestUri, $requestMethod)) {
    // Rota não encontrada - 404
    http_response_code(404);
    echo json_encode([
        'error' => 'Rota não encontrada',
        'uri' => $requestUri,
        'method' => $requestMethod
    ]);
}
