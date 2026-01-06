<?php

use ChurchCRM\dto\SystemURLs;
use ChurchCRM\Slim\SlimUtils;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\PhpRenderer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// Carregar models do módulo ministério
require_once __DIR__ . '/../../modules/ministerio/models/Reuniao.php';

$app->group('/rsvp', function (RouteCollectorProxy $group): void {
    $group->get('/{token}', 'serveRSVPPage');
    $group->post('/{token}', 'processarRSVP');
});

function serveRSVPPage(Request $request, Response $response, array $args): Response
{
    $token = SlimUtils::getRouteArgument($request, 'token');
    $participante = ReuniaoModel::buscarParticipantePorToken($token);
    
    if (!$participante) {
        $renderer = new PhpRenderer('templates/');
        return $renderer->render($response, '404.php')
            ->withStatus(404);
    }
    
    $renderer = new PhpRenderer('templates/rsvp/');
    return $renderer->render($response, 'rsvp.php', [
        'participante' => $participante,
        'sRootPath' => SystemURLs::getRootPath(),
        'token' => $token,
    ]);
}

function processarRSVP(Request $request, Response $response, array $args): Response
{
    $token = SlimUtils::getRouteArgument($request, 'token');
    $data = $request->getParsedBody();
    $status = $data['status'] ?? 'confirmado';
    
    $sucesso = ReuniaoModel::confirmarRSVP($token, $status);
    
    if ($sucesso) {
        $participante = ReuniaoModel::buscarParticipantePorToken($token);
        $renderer = new PhpRenderer('templates/rsvp/');
        return $renderer->render($response, 'rsvp-sucesso.php', [
            'participante' => $participante,
            'status' => $status,
            'sRootPath' => SystemURLs::getRootPath(),
        ]);
    }
    
    return SlimUtils::renderJSON($response, [
        'success' => false,
        'message' => 'Erro ao confirmar presença'
    ])->withStatus(400);
}

