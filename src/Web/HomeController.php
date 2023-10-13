<?php
/**
 * @copyright 2023 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web;

class HomeController extends Controller
{
    public function __invoke(array $params): View
    {
        global $UREPORT;

        $open311 = new Open311Gateway($UREPORT);
        $service = !empty($_REQUEST['service_code'])
                 ? $open311->getService($_REQUEST['service_code'])
                 : null;

        if ($service) {
            return new Views\RequestFormView();
        }

        if (!empty($_REQUEST['group'])) {
            $services = $open311->getGroupServices($_REQUEST['group']);
            return new Views\ChooseServiceView($services, $_REQUEST['group']);
        }

        return new Views\ChooseGroupView($open311->getServiceGroups());
    }
}
