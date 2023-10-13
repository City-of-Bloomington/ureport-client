<?php
/**
 * @copyright 2023 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Controllers;

use Web\Controller;
use Web\View;

class ChooseGroupController extends Controller
{
    public function __invoke(array $params): View
    {
        $open311 = $this->di->get('Web\Open311Gateway');
        $groups  = $open311->getServiceGroups();
        return new \Web\Views\ChooseGroupView($groups);
    }
}
