<?php

declare(strict_types=1);

namespace  Freento\FastSearchAutocomplete\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Freento\FastSearchAutocomplete\Helper\StoreHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Search\Helper\Data as SearchHelper;

class SearchForm extends Template implements ArgumentInterface
{
    private const AUTOCOMPLETE_URL = 'searchautocomplete/ajax/suggest';
    private const DIRECT_AUTOCOMPLETE_URL = 'search_result.php?';
    private const PRODUCT_URL = 'searchautocomplete/product/get';

    public const SEARCH_MODE_NATIVE = 'native';
    public const SEARCH_MODE_DIRECT = 'direct';

    /**
     * @var StoreHelper
     */
    private StoreHelper $storeHelper;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var SearchHelper
     */
    private SearchHelper $searchHelper;

    /**
     * @param StoreHelper $storeHelper
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param SearchHelper $searchHelper
     * @param array $data
     */
    public function __construct(
        StoreHelper $storeHelper,
        Context $context,
        StoreManagerInterface $storeManager,
        SearchHelper $searchHelper,
        array $data = []
    ) {
        $this->storeHelper = $storeHelper;
        $this->storeManager = $storeManager;
        $this->searchHelper = $searchHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get URL for autocomplete.
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getNativeAutocompleteUrl(): string
    {
        return $this->storeManager->getStore()->getUrl(self::AUTOCOMPLETE_URL);
    }

    /**
     * Get URL for direct autocomplete.
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDirectAutocompleteUrl(): string
    {
        return $this->storeManager->getStore()->getBaseUrl() .
            self::DIRECT_AUTOCOMPLETE_URL .
            http_build_query(['storeId' => $this->storeHelper->getStoreId()]) .
            '&';
    }

    /**
     * Get URL for product.
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAutocompleteProductUrl(): string
    {
        return $this->storeManager->getStore()->getUrl(self::PRODUCT_URL);
    }

    /**
     * Is native search mode.
     *
     * @return bool
     */
    public function isNativeSearchMode(): bool
    {
        return $this->storeHelper->getAutocompleteMode() === self::SEARCH_MODE_NATIVE;
    }

    /**
     * Get autocomplete URL by config setting.
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAutocompleteUrlByConfig(): string
    {
        return $this->isNativeSearchMode() ? $this->getNativeAutocompleteUrl() : $this->getDirectAutocompleteUrl();
    }

    /**
     * Get search helper
     *
     * @return SearchHelper
     */
    public function getSearchHelper(): SearchHelper
    {
        return $this->searchHelper;
    }
}
