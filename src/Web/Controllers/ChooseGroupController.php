<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Controllers;

use Web\Controller;
use Web\View;

class ChooseGroupController extends Controller
{
    public const MIN_QUERY_LENGTH = 3;

    public function __invoke(array $params): View
    {
        $open311 = $this->di->get('Web\Open311Gateway');
        $groups  = [];
        foreach ($open311->getServiceGroups() as $code=>$name) {
            $groups[$code] = [
                'name'     => $name,
                'services' => $open311->getGroupServices($name)
            ];
        }

        $results = [];
        if (!empty($_GET['query']) && strlen($_GET['query']) >= self::MIN_QUERY_LENGTH) {
            $q = strtolower($_GET['query']);

            foreach ($groups as $g => $group) {
                foreach ($group['services'] as $s) {
                    $service_name = strtolower($s['service_name']);
                    $description  = strtolower($s['description' ] ?? '');

                    if (str_contains($service_name, $q) || str_contains($description, $q)) {
                        $results[] = [
                            'group_code'   => $g,
                            'service_code' => $s['service_code'],
                            'service_name' => $s['service_name'],
                            'description'  => $s['description' ]
                        ];
                    }

                }
            }
        }
        return isset($_GET['partial'])
               ? new \Web\Views\SearchResultsView($results)
               : new \Web\Views\ChooseGroupView($groups, $results);
    }
}
