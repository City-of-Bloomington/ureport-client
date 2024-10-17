<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\RentalComplaints;

use ReCaptcha\ReCaptcha;

class ServiceRequestController extends \Web\Controller
{
    public const RENTAL_COMPLAINTS = '1';

    public function __invoke(array $params): \Web\View
    {
        $open311 = $this->di->get('Web\Open311Gateway');
        $service = $open311->getService(self::RENTAL_COMPLAINTS);
        $def     = $open311->getServiceDefinition(self::RENTAL_COMPLAINTS);

        if ($service) {
            if (   !empty($_POST['g-recaptcha-response'])
                && !empty($_POST['service_code'        ])) {

                $rc  = new ReCaptcha(RECAPTCHA_SECRET_KEY);
                $r   = $rc->setExpectedHostname(BASE_HOST)->verify($_POST['g-recaptcha-response']);
                if ($r->isSuccess()) {

                    $file = $_FILES['media'] ?? null;
                    $json = $open311->postServiceRequest($_POST, $file);
                    $res  = $json[0];
                    if (!empty($res['service_request_id'])) {
                        try {
                            $req = $open311->getServiceRequest((int)$res['service_request_id']);
                            header('Location: '.\Web\View::generateUrl('home.success')."?service_request_id=$res[service_request_id]");
                            exit();
                        }
                        catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
                    }

                    if (!empty($res['code']) && $res['code'] == 400 && !empty($res['description'])) {
                        $_SESSION['errorMessages'][] = new \Exception($res['description']);
                    }
                }
            }
            return new ServiceRequestView($service, $def, $params['group_code']);
        }
        return new \Web\Views\NotFoundView();
    }
}
