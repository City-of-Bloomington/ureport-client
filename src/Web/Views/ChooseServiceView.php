<?php
/**
 * @copyright 2023 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Views;

use Web\View;

class ChooseServiceView extends View
{
    public function __construct(array $services, string $group_code, string $group_name)
    {
        parent::__construct();

        $this->vars = [
            'group_code' => $group_code,
            'group_name' => $group_name,
            'services'   => $services
        ];
    }
    public function render(): string
    {
        return $this->twig->render("{$this->outputFormat}/chooseService.twig", $this->vars);
    }
}
