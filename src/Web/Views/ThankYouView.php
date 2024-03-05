<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Views;

use Web\View;

class ThankYouView extends View
{
    public function __construct(int $service_request_id)
    {
        parent::__construct();

        $this->vars = [
            'service_request_id' => $service_request_id
        ];
    }

    public function render(): string
    {
        return $this->twig->render("{$this->outputFormat}/thankYou.twig", $this->vars);
    }
}
