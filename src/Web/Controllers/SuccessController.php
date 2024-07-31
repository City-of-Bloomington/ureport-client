<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Controllers;

class SuccessController extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_GET['service_request_id'])) {
            return new \Web\Views\ThankYouView((int)$_GET['service_request_id']);
        }
        return new \Web\Views\NotFoundView();
    }
}
