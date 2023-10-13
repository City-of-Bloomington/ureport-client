<?php
/**
 * @copyright 2023 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Controllers;

use Web\Controller;
use Web\View;

class ChooseServiceController extends Controller
{
    public function __invoke(array $params): View
    {
        $open311 = $this->di->get('Web\Open311Gateway');
        $groups  = $open311->getServiceGroups();
        $code    = $params['group_code'];

        if (array_key_exists($code, $groups)) {
            $services = $open311->getGroupServices($groups[$code]);
            return new \Web\Views\ChooseServiceView($services, $code, $groups[$code]);
        }
        return new \Web\Views\NotFoundView();
    }
}
