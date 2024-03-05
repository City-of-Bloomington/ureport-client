<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Controllers;

use Web\Controller;
use Web\View;

class ContactInfoController extends Controller
{
    public function __invoke(array $params): View
    {
        $open311 = $this->di->get('Web\Open311Gateway');
        $service = $open311->getService($params['service_code']);
        if ($service) {
            if (isset($_POST['firstname'])) {
                foreach (['firstname', 'lastname', 'email', 'phone'] as $f) {
                    $_SESSION[$f] = $_POST[$f];
                }
                $requestForm  = View::generateUrl('home.request', [
                    'group_code'   => $params['group_code'  ],
                    'service_code' => $params['service_code']
                ]);
                header("Location: $requestForm");
                exit();
            }
            return new \Web\Views\ContactInfoView($service, $params['group_code']);
        }
        return new \Web\Views\NotFoundView();
    }
}
