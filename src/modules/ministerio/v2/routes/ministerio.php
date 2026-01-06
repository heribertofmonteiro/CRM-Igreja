<?php
/**
 * Rotas do módulo Ministério para v2 - API REST
 * 
 * Nota: $app é injetado globalmente pelo index.php do v2
 */
use ChurchCRM\dto\SystemURLs;
use ChurchCRM\Slim\Middleware\Request\Auth\AdminRoleAuthMiddleware;
use ChurchCRM\Slim\Middleware\Request\Auth\EditRecordsRoleAuthMiddleware;
use ChurchCRM\Authentication\AuthenticationManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

// Carregar models
require_once __DIR__ . '/../../models/Ministerio.php';
require_once __DIR__ . '/../../models/Reuniao.php';
require_once __DIR__ . '/../../models/Mensagem.php';

// Helper para responder JSON compatível com Slim 4
function respondJson(Response $response, $payload, int $status = null): Response {
    $body = $response->getBody();
    $body->rewind();
    $body->write(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    if ($status !== null) {
        $response = $response->withStatus($status);
    }
    return $response->withHeader('Content-Type', 'application/json');
}

// Grupo de rotas API do módulo ministério
// $app é definido no index.php do v2
global $app;
if (!isset($app) && isset($moduleApp)) {
    $app = $moduleApp;
}

if (isset($app)) {
    $app->group('/ministerio', function (RouteCollectorProxy $group): void {
    // Listar ministérios (API endpoint - usa /api para diferenciar do dashboard)
    $group->get('/api', function (Request $request, Response $response): Response {
        $ministerios = MinisterioModel::listar();
        return respondJson($response, ['data' => $ministerios]);
    });
    
    // Criar ministério
    $group->post('/criar', function (Request $request, Response $response): Response {
        $data = $request->getParsedBody();
        $user = AuthenticationManager::getCurrentUser();
        $data['lider_id'] = $user->getPerson()->getId();
        $id = MinisterioModel::criar($data);
        return respondJson($response, ['data' => ['id' => $id], 'message' => 'Ministério criado com sucesso']);
    })->add(EditRecordsRoleAuthMiddleware::class);
    
    // Detalhes do ministério
    $group->get('/{id}/detalhes', function (Request $request, Response $response, array $args): Response {
        $ministerio = MinisterioModel::buscarPorId($args['id']);
        if (!$ministerio) {
            return respondJson($response, ['error' => 'Ministério não encontrado'], 404);
        }
        $ministerio['membros'] = MinisterioModel::listarMembros($args['id']);
        return respondJson($response, ['data' => $ministerio]);
    });
    
    // Atualizar ministério
    $group->post('/{id}/atualizar', function (Request $request, Response $response, array $args): Response {
        $data = $request->getParsedBody();
        $id = (int)$args['id'];
        MinisterioModel::atualizar($id, $data);
        return respondJson($response, ['message' => 'Ministério atualizado com sucesso']);
    })->add(EditRecordsRoleAuthMiddleware::class);
    
    // Excluir ministério
    $group->post('/{id}/excluir', function (Request $request, Response $response, array $args): Response {
        $id = (int)$args['id'];
        MinisterioModel::excluir($id);
        return respondJson($response, ['message' => 'Ministério excluído com sucesso']);
    })->add(EditRecordsRoleAuthMiddleware::class);
    
    // Adicionar membro
    $group->post('/{id}/membros/adicionar', function (Request $request, Response $response, array $args): Response {
        $data = $request->getParsedBody();
        MinisterioModel::adicionarMembro($args['id'], $data['membro_id'], $data['funcao'] ?? null);
        return respondJson($response, ['message' => 'Membro adicionado com sucesso']);
    })->add(EditRecordsRoleAuthMiddleware::class);
    
    // Remover membro
    $group->post('/{id}/membros/{membro_id}/remover', function (Request $request, Response $response, array $args): Response {
        MinisterioModel::removerMembro($args['id'], $args['membro_id']);
        return respondJson($response, ['message' => 'Membro removido com sucesso']);
    })->add(EditRecordsRoleAuthMiddleware::class);
    
    // Listar reuniões
    $group->get('/reuniao', function (Request $request, Response $response): Response {
        $params = $request->getQueryParams();
        $ministerioId = $params['ministerio_id'] ?? null;
        $apenasFuturas = isset($params['futuras']) && $params['futuras'] == '1';
        
        $reunioes = ReuniaoModel::listar($ministerioId, $apenasFuturas);
        return respondJson($response, ['data' => $reunioes]);
    });
    
    // Criar reunião
    $group->post('/reuniao/criar', function (Request $request, Response $response): Response {
        $data = $request->getParsedBody();
        $user = AuthenticationManager::getCurrentUser();
        $data['criado_por'] = $user->getPerson()->getId();
        $id = ReuniaoModel::criar($data);
        return respondJson($response, ['data' => ['id' => $id], 'message' => 'Reunião criada com sucesso']);
    })->add(EditRecordsRoleAuthMiddleware::class);
    
    // Atualizar reunião
    $group->post('/reuniao/{id}/atualizar', function (Request $request, Response $response, array $args): Response {
        $data = $request->getParsedBody();
        $id = (int)$args['id'];
        ReuniaoModel::atualizar($id, $data);
        return respondJson($response, ['message' => 'Reunião atualizada com sucesso']);
    })->add(EditRecordsRoleAuthMiddleware::class);
    
    // Excluir reunião
    $group->post('/reuniao/{id}/excluir', function (Request $request, Response $response, array $args): Response {
        $id = (int)$args['id'];
        ReuniaoModel::excluir($id);
        return respondJson($response, ['message' => 'Reunião excluída com sucesso']);
    })->add(EditRecordsRoleAuthMiddleware::class);
    
    // Participantes da reunião
    $group->get('/reuniao/{id}/participantes', function (Request $request, Response $response, array $args): Response {
        $participantes = ReuniaoModel::listarParticipantes($args['id']);
        return respondJson($response, ['data' => $participantes]);
    });
    
    // Listar mensagens
    $group->get('/mensagem', function (Request $request, Response $response): Response {
        $params = $request->getQueryParams();
        $filtros = [];
        if (isset($params['ministerio_id'])) $filtros['ministerio_id'] = $params['ministerio_id'];
        if (isset($params['reuniao_id'])) $filtros['reuniao_id'] = $params['reuniao_id'];
        if (isset($params['status'])) $filtros['status'] = $params['status'];
        if (isset($params['tipo'])) $filtros['tipo'] = $params['tipo'];
        
        $mensagens = MensagemModel::listar($filtros);
        return respondJson($response, ['data' => $mensagens]);
    });
    
    // Enviar mensagem
    $group->post('/mensagem/enviar', function (Request $request, Response $response): Response {
        $data = $request->getParsedBody();
        $user = AuthenticationManager::getCurrentUser();
        $data['criado_por'] = $user->getPerson()->getId();
        $id = MensagemModel::criar($data);
        return respondJson($response, ['data' => ['id' => $id], 'message' => 'Mensagem criada com sucesso']);
    })->add(EditRecordsRoleAuthMiddleware::class);
    
    // Histórico de mensagens
    $group->get('/mensagens/historico', function (Request $request, Response $response): Response {
        $params = $request->getQueryParams();
        $ministerioId = $params['ministerio_id'] ?? null;
        $reuniaoId = $params['reuniao_id'] ?? null;
        
        if (!$ministerioId && !$reuniaoId) {
            return respondJson($response, ['error' => 'ministerio_id ou reuniao_id é obrigatório'], 400);
        }
        
        $historico = MensagemModel::obterHistorico($ministerioId, $reuniaoId);
        return respondJson($response, ['data' => $historico]);
    });

    // Mensagem por ID
    $group->get('/mensagens/{id}', function (Request $request, Response $response, array $args): Response {
        $mensagem = MensagemModel::buscarPorId((int)$args['id']);
        if (!$mensagem) {
            return respondJson($response, ['error' => 'Mensagem não encontrada'], 404);
        }
        return respondJson($response, ['data' => $mensagem]);
    });
    
    // RSVP via token
    $group->get('/reuniao/rsvp/{token}', function (Request $request, Response $response, array $args): Response {
        $participante = ReuniaoModel::buscarParticipantePorToken($args['token']);
        if (!$participante) {
            return respondJson($response, ['error' => 'Token inválido'], 404);
        }
        return respondJson($response, ['data' => $participante]);
    });
    
    $group->post('/reuniao/rsvp/{token}', function (Request $request, Response $response, array $args): Response {
        $data = $request->getParsedBody();
        $status = $data['status'] ?? 'confirmado';
        $sucesso = ReuniaoModel::confirmarRSVP($args['token'], $status);
        return respondJson($response, ['success' => $sucesso, 'message' => 'Presença confirmada']);
    });
    })->add(AdminRoleAuthMiddleware::class);
}

