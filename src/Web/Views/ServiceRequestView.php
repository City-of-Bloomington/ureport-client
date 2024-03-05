<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Views;

use Web\View;

class ServiceRequestView extends View
{
    public function __construct(array $service, array $definition, string $group_code)
    {
        parent::__construct();
        $this->vars = [
            'service'             => $service,
            'attributes'          => $definition['attributes'] ?? null,
            'group_code'          => $group_code,
            'google_maps_api_key' => GOOGLE_MAPS_API_KEY,
            'default_latitude'    => ini_get('date.default_latitude'),
            'default_longitude'   => ini_get('date.default_longitude'),
            'firstname'           => $_SESSION['firstname'] ?? '',
            'lastname'            => $_SESSION['lastname' ] ?? '',
            'email'               => $_SESSION['email'    ] ?? '',
            'phone'               => $_SESSION['phone'    ] ?? ''
        ];
        if (isset($_SESSION['errorMessages'])) {
            foreach ($_SESSION['errorMessages'] as $e) {
                $this->vars['errorMessages'][] = $e->getMessage();
            }
        }
        unset($_SESSION['errorMessages']);
    }

    public function render(): string
    {
        return $this->twig->render("{$this->outputFormat}/requestForm.twig", $this->vars);
    }
}
