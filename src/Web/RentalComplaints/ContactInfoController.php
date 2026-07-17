<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\RentalComplaints;

class ContactInfoController extends \Web\Controller
{
    public const RENTAL_COMPLAINTS = '1';

    public function __invoke(array $params): \Web\View
    {
        $open311 = $this->di->get('Web\Open311Gateway');
        $service = $open311->getService(self::RENTAL_COMPLAINTS);
        if ($service) {
            if (isset($_POST['first_name'])) {
                foreach (['first_name', 'last_name', 'email', 'phone'] as $f) {
                    $_SESSION[$f] = $_POST[$f];
                }
                $requestForm  = \Web\View::generateUrl('rental_complaints.request', [
                    'group_code'   => $params['group_code'  ],
                    'service_code' => self::RENTAL_COMPLAINTS
                ]);
                header("Location: $requestForm");
                exit();
            }
            return new ContactInfoView($service, $params['group_code']);
        }
        return new \Web\Views\NotFoundView();
    }
}
