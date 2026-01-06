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
    $group->get('/{id}/detalhes', 'ministerioDetalhes');
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

function ministerioDetalhes($request, $response, array $args): Response
{
    $renderer = new PhpRenderer('templates/ministerio/');

    $pageArgs = [
        'sRootPath'  => SystemURLs::getRootPath(),
        'sPageTitle' => gettext('Detalhes do Ministério'),
        'ministerioId' => (int)$args['id'],
    ];

    return $renderer->render($response, 'detalhes.php', $pageArgs);
}


