<?php
/**
 * Renders an HTML partial of just the search results
 *
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Views;

use Web\View;

class SearchResultsView extends View
{
    public function __construct(array $searchResults, string $query)
    {
        parent::__construct();

        $this->vars = [
            'searchResults' => $searchResults,
            'query'         => $query
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/partials/searchResults.twig', $this->vars);
    }
}
