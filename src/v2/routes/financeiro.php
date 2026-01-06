<?php

use ChurchCRM\dto\SystemConfig;
use ChurchCRM\dto\SystemURLs;
use ChurchCRM\Slim\Middleware\Request\Auth\FinanceRoleAuthMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\PhpRenderer;

$app->group('/financeiro', function (RouteCollectorProxy $group): void {
    $group->get('', 'financeiroPage');
    $group->get('/', 'financeiroPage');
})->add(FinanceRoleAuthMiddleware::class);

function financeiroPage(Request $request, Response $response, array $args): Response
{
    $renderer = new PhpRenderer('templates/financeiro/');

    $pageArgs = [
        'sRootPath'  => SystemURLs::getRootPath(),
        'sPageTitle' => gettext('Financeiro'),
        'bEnabledFinance' => SystemConfig::getBooleanValue('bEnabledFinance'),
        'bEnabledFundraiser' => SystemConfig::getBooleanValue('bEnabledFundraiser'),
    ];

    return $renderer->render($response, 'dashboard.php', $pageArgs);
}











