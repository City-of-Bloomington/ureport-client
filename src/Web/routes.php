<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);

$ROUTES = new Aura\Router\RouterContainer(BASE_URI);
$map    = $ROUTES->getMap();

$map->attach('home.', '', function ($r) {
    $r->get('help',   '/help',                                Web\Controllers\HelpController::class);
    $r->get('request', '/{group_code}/{service_code}/fields', Web\Controllers\ServiceRequestController::class)->allows(['POST']);
    $r->get('contact', '/{group_code}/{service_code}',        Web\Controllers\ContactInfoController::class)->allows(['POST']);
    $r->get('service', '/{group_code}',                       Web\Controllers\ChooseServiceController::class);
    $r->get('index',   '/',                                   Web\Controllers\ChooseGroupController::class);
    $r->tokens([
        'group_code'   => '[a-z\-]+',
        'service_code' => '\d+'
    ]);
});
