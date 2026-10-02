<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Controllers;

use ReCaptcha\ReCaptcha;

class ServiceRequestController extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $open311 = $this->di->get('Web\Open311Gateway');
        $service = $open311->getService($params['service_code']);
        $def     = $open311->getServiceDefinition($params['service_code']);

        if ($service) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (empty($_POST) && empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0) {
                    $_SESSION['errorMessages'][] = 'file/uploadTooLarge';
                }
                elseif (   !empty($_POST['g-recaptcha-response'])
                        && !empty($_POST['service_code'        ])) {

                    $rc  = new ReCaptcha(RECAPTCHA_SECRET_KEY);
                    $r   = $rc->setExpectedHostname(BASE_HOST)->verify($_POST['g-recaptcha-response']);
                    if ($r->isSuccess()) {
                        $res  = [];
                        if ( isset( $_FILES['media'] )) {
                            switch ($_FILES['media']['error']) {
                                case UPLOAD_ERR_INI_SIZE:
                                case UPLOAD_ERR_FORM_SIZE:
                                    $_SESSION['errorMessages'][] = 'file/uploadTooLarge';
                                    break;
                                case UPLOAD_ERR_NO_FILE:
                                    $json = $open311->postServiceRequest($_POST);
                                    $res  = $json[0];
                                    break;
                                case UPLOAD_ERR_OK:
                                    $json = $open311->postServiceRequest($_POST, $_FILES['media']);
                                    $res  = $json[0];
                                    break;
                                default:
                                    $_SESSION['errorMessages'][] = 'file/uploadFailed';
                            }
                        }

                        if (!empty($res['service_request_id'])) {
                            try {
                                $req = $open311->getServiceRequest((int)$res['service_request_id']);
                                header('Location: '.\Web\View::generateUrl('home.success')."?service_request_id=$res[service_request_id]");
                                exit();
                            }
                            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
                        }

                        if (!empty($res['code']) && $res['code'] == 400 && !empty($res['description'])) {
                            $_SESSION['errorMessages'][] = $res['description'];
                        }
                    }
                }
            }
            return new \Web\Views\ServiceRequestView($service, $def, $params['group_code']);
        }
        return new \Web\Views\NotFoundView();
    }
}
