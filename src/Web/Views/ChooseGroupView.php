<?php
/**
 * @copyright 2023 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Views;

use Web\View;

class ChooseGroupView extends View
{
    public function __construct(array $groups)
    {
        parent::__construct();
        $this->vars = ['groups' => $groups];
    }

    public function render(): string
    {
        return $this->twig->render("{$this->outputFormat}/chooseGroup.twig", $this->vars);
    }
}
