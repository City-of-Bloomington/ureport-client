<?php
/**
 * @copyright 2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
use Aura\Di\ContainerBuilder;

$builder = new ContainerBuilder();
$DI = $builder->newInstance();
$DI->set('Web\Open311Gateway', new Web\Open311Gateway($UREPORT));

//---------------------------------------------------------
// Declare database repositories
//---------------------------------------------------------

//---------------------------------------------------------
// Metadata providers
//---------------------------------------------------------

//---------------------------------------------------------
// Services
//---------------------------------------------------------

//---------------------------------------------------------
// Use Cases
//---------------------------------------------------------
