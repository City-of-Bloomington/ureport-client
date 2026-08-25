<?php
/**
 * @copyright 2024-2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Controllers;

use Web\Controller;
use Web\View;
use Web\Open311Gateway;

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

        $q   = null;
        $res = [];
        if (      !empty($_GET['query'])
            && is_string($_GET['query'])
            &&    strlen($_GET['query']) >= self::MIN_QUERY_LENGTH) {

            $q   = preg_replace('/[^\w\x20]/', '', $_GET['query']);
            $res = is_numeric($q) ? $this->tickets ((int)$q, $open311)
                                  : $this->services(     $q, $groups);
        }

        return isset($_GET['partial'])
               ? new \Web\Views\SearchResultsView($res, $q)
               : new \Web\Views\ChooseGroupView($groups, $res, $q);
    }

    private function services(string $query, array $groups): array
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

    private function tickets(int $ticket_id, Open311Gateway $open311): array
    {
        $results = [];
        $json    = $open311->getServiceRequest($ticket_id);
        if ($json && !empty($json[0]['service_request_id'])) {
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
