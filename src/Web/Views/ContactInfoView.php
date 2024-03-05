<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Views;

class ContactInfoView extends \Web\View
{
    public function __construct(array $service, string $group_code)
    {
        parent::__construct();
        $this->vars = [
            'service'    => $service,
            'group_code' => $group_code
        ];
    }

    public function render(): string
    {
        return $this->twig->render("{$this->outputFormat}/contactForm.twig", $this->vars);
    }
}
