<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Controllers;

use Web\Controller;
use Web\View;

class ServiceRequestController extends Controller
{
    public function __invoke(array $params): View
    {
        $open311 = $this->di->get('Web\Open311Gateway');
        $service = $open311->getService($params['service_code']);
        $def     = $open311->getServiceDefinition($params['service_code']);
        if ($service) {
            if (!empty($_POST['service_code'])) {
                $file = $_FILES['media'] ?? null;
                $json = $open311->postServiceRequest($_POST, $file);
                $res  = $json[0];
                if (!empty($res['service_request_id'])) {
                    try {
                        $req = $open311->getServiceRequest($res['service_request_id']);
                        return new \Web\Views\ThankYouView((int)$res['service_request_id']);
                    }
                    catch (\Exception $e) {
                        $_SESSION['errorMessages'][] = $e;
                    }
                }

                if ($res['code'] == 400 && !empty($res['description'])) {
                    $_SESSION['errorMessages'][] = new \Exception($res['description']);
                }
            }
            return new \Web\Views\ServiceRequestView($service, $def, $params['group_code']);
        }
        return new \Web\Views\NotFoundView();
    }
}
