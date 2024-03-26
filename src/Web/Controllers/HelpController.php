<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Controllers;

use Web\Controller;
use Web\View;


class HelpController extends Controller
{
    public function __invoke(array $params): View
    {
        return new \Web\Views\HelpView();
    }
}