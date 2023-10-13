<?php
/**
 * @copyright 2020-2022 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);

$ROUTES = new Aura\Router\RouterContainer(BASE_URI);
$map    = $ROUTES->getMap();

$map->attach('home.', '', function ($r) {
    $r->get('service', '/{group_code}/{service_code}', Web\Controllers\ServiceRequestController::class)->allows(['POST']);
    $r->get('group',   '/{group_code}',                Web\Controllers\ChooseServiceController::class);
    $r->get('index',   '/',                            Web\Controllers\ChooseGroupController::class);
    $r->tokens([
        'group_code'   => '[a-z\-]+',
        'service_code' => '\d+'
    ]);
});
