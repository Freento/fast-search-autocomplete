<?php

declare(strict_types=1);

namespace Freento\FastSearchAutocomplete\Plugin\Search\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Search\Helper\Data as Origin;
use Freento\FastSearchAutocomplete\ViewModel\SearchForm;

class Data
{
    /**
     * @var SearchForm
     */
    private SearchForm $searchForm;

    /**
     * @param SearchForm $searchForm
     */
    public function __construct(
        SearchForm $searchForm
    ) {
        $this->searchForm = $searchForm;
    }

    /**
     * @param Origin $subject
     * @param callable $proceed
     * @return string
     * @throws NoSuchEntityException
     */
    public function aroundGetSuggestUrl(Origin $subject, callable $proceed): string
    {
        return $this->searchForm->getAutocompleteUrlByConfig();
    }

    /**
     * @param Origin $subject
     * @param callable $proceed
     * @return string
     * @throws NoSuchEntityException
     */
    public function aroundGetResultUrl(Origin $subject, callable $proceed): string
    {
        return $this->searchForm->getAutocompleteProductUrl();
    }
}
