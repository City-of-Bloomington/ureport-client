<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
/**
 * Grab a timestamp for calculating process time
 */
declare (strict_types=1);
use Web\Authentication\Auth;

$startTime = microtime(true);

include '../src/Web/bootstrap.php';
ini_set('session.save_path', SITE_HOME.'/sessions');
ini_set('session.cookie_path', BASE_URI);
session_start();

$matcher = $ROUTES->getMatcher();
$route   = $matcher->match(GuzzleHttp\Psr7\ServerRequest::fromGlobals());
$view    = null;

if ($route) {
    $controller = $route->handler;
    $c = new $controller($DI);
    if (is_callable($c)) {
        $view = $c($route->attributes);
    }
}

if (!$view) {
    $f = $matcher->getFailedRoute();
    $view = new \Web\Views\NotFoundView();
}


echo $view->render();

// Append some useful stats to the output of HTML pages
if ($view->outputFormat === 'html') {
    # Calculate the process time
    $endTime = microtime(true);
    $processTime = $endTime - $startTime;
    echo "<!-- Process Time: $processTime -->\n";

    $size   = ['B','kB','MB','GB','TB','PB','EB','ZB','YB'];
    $bytes  = memory_get_peak_usage();
    $factor = floor( (strlen("$bytes") - 1) / 3);
    $memory = sprintf("%.2f", $bytes / pow(1024, $factor)) . @$size[$factor];
    echo "<!-- Memory: $memory -->";
}
