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
            $results = is_numeric($_GET['query'])
                     ? $this->search_tickets ((int)$_GET['query'])
                     : $this->search_services(     $_GET['query'], $groups);
        }

        return isset($_GET['partial'])
               ? new \Web\Views\SearchResultsView($results)
               : new \Web\Views\ChooseGroupView($groups, $results);
    }

    private function search_services(string $query, array $groups): array
    {
        $q       = strtolower($query);
        $results = [];

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
        return $results;
    }

    private function search_tickets(int $ticket_id): array
    {
        global $UREPORT;

        $results = [];
        $url     = "$UREPORT[url]/requests/$ticket_id.json";
        $json    = json_decode(file_get_contents($url), true);
        if ($json && $json[0]['service_request_id']) {
            $results[] = [
                'service_request_id' => $json[0]['service_request_id'],
                'service_code'       => $json[0]['service_code'],
                'service_name'       => $json[0]['service_name'],
                'description'        => $json[0]['description' ],
                'status'             => $json[0]['status'],
                'agency_responsible' => $json[0]['agency_responsible'],
                'requested_datetime' => $json[0]['requested_datetime']
            ];
        }

        return $results;
    }
}
