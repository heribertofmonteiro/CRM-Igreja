<?php
/**
 * Rotas do módulo Ministério - Dashboard
 */
use ChurchCRM\dto\SystemURLs;
use ChurchCRM\Slim\Middleware\Request\Auth\AdminRoleAuthMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\PhpRenderer;

$app->group('/ministerio', function (RouteCollectorProxy $group): void {
    $group->get('', 'ministerioDashboard');
    $group->get('/', 'ministerioDashboard');
    // Removido: $group->get('/{id}/detalhes', 'ministerioDetalhes');
    // Esta rota está duplicada no módulo modules/ministerio/v2/routes/ministerio.php
})->add(AdminRoleAuthMiddleware::class);

function ministerioDashboard($request, $response, $args): Response
{
    $renderer = new PhpRenderer('templates/ministerio/');

    $pageArgs = [
        'sRootPath'  => SystemURLs::getRootPath(),
        'sPageTitle' => gettext('Ministério & Comunicação'),
    ];

    return $renderer->render($response, 'dashboard.php', $pageArgs);
}

// Função ministerioDetalhes removida - está implementada no módulo
// Acesse via: /v2/ministerio/{id}/detalhes (API endpoint)


