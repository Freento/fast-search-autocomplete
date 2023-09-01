<?php

declare(strict_types=1);

namespace Freento\FastSearchAutocomplete\Helper;

use Freento\FastSearchAutocomplete\Model\ElasticSearch;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;

class StoreHelper extends AbstractHelper
{
    private const XML_PATH_AUTOCOMPLETE_MODE = 'search/autocomplete/mode';
    private const XML_PATH_ELASTICSEARCH_HOSTNAME = 'catalog/search/elasticsearch5_server_hostname';
    private const XML_PATH_ELASTICSEARCH_PORT = 'catalog/search/elasticsearch5_server_port';
    private const XML_PATH_ELASTICSEARCH_INDEX_PREFIX = 'catalog/search/elasticsearch5_index_prefix';
    private const XML_PATH_ELASTICSEARCH_ENABLEAUTH = 'catalog/search/elasticsearch5_enable_auth';
    private const XML_PATH_ELASTICSEARCH_USERNAME = 'catalog/search/elasticsearch5_username';
    private const XML_PATH_ELASTICSEARCH_PASSWORD = 'catalog/search/elasticsearch5_password';
    private const XML_PATH_ELASTICSEARCH_TIMEOUT = 'catalog/search/elasticsearch5_server_timeout';
    private const XML_PATH_SEARCH_ENGINE = 'catalog/search/engine';
    private const XML_PATH_MAX_SEARCH_RESULT_SYMBOLS = 'search/autocomplete/max_search_result_symbols';
    private const XML_PATH_MAX_RESULTS_COUNT = 'search/autocomplete/max_results_count';

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context);
    }

    /**
     * Return the value of a specific setting depending on the Store ID.
     *
     * @param string $configPath
     * @param int|null $storeId
     * @return string|null
     */
    private function getConfigValue($configPath, $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            $configPath,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get search autocomplete mode.
     *
     * @return string
     */
    public function getAutocompleteMode(): string
    {
        return (string)$this->getConfigValue(self::XML_PATH_AUTOCOMPLETE_MODE);
    }

    /**
     * Get store id.
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId(): string
    {
        return (string)$this->storeManager->getStore()->getStoreId();
    }

    /**
     * Get elasticsearch hostname.
     *
     * @return string
     */
    public function getElasticsearchHostname(): string
    {
        return (string)$this->getConfigValue(self::XML_PATH_ELASTICSEARCH_HOSTNAME);
    }

    /**
     * Get elasticsearch port.
     *
     * @return string
     */
    public function getElasticsearchPort(): string
    {
        return (string)$this->getConfigValue(self::XML_PATH_ELASTICSEARCH_PORT);
    }

    /**
     * Get elasticsearch index prefix.
     *
     * @return string
     */
    public function getElasticsearchIndexPrefix(): string
    {
        return (string)$this->getConfigValue(self::XML_PATH_ELASTICSEARCH_INDEX_PREFIX);
    }

    /**
     * Get elasticsearch enable auth.
     *
     * @return string
     */
    public function getElasticsearchEnableAuth(): string
    {
        return (string)$this->getConfigValue(self::XML_PATH_ELASTICSEARCH_ENABLEAUTH);
    }

    /**
     * Get elasticsearch username.
     *
     * @return string
     */
    public function getElasticsearchUsername(): string
    {
        return (string)$this->getConfigValue(self::XML_PATH_ELASTICSEARCH_USERNAME);
    }

    /**
     * Get elasticsearch password.
     *
     * @return string
     */
    public function getElasticsearchPassword(): string
    {
        return (string)$this->getConfigValue(self::XML_PATH_ELASTICSEARCH_PASSWORD);
    }

    /**
     * Get elasticsearch timeout.
     *
     * @return string
     */
    public function getElasticsearchTimeout(): string
    {
        return (string)$this->getConfigValue(self::XML_PATH_ELASTICSEARCH_TIMEOUT);
    }

    /**
     * @return string
     */
    public function getSearchEngine(): string
    {
        return (string)$this->getConfigValue(self::XML_PATH_SEARCH_ENGINE);
    }

    /**
     * @return int
     */
    public function getMaxSearchResultSymbols(): int
    {
        return (int)$this->getConfigValue(self::XML_PATH_MAX_SEARCH_RESULT_SYMBOLS);
    }

    /**
     * @return string
     */
    public function getMaxResultsCount(): string
    {
        return (string)$this->getConfigValue(self::XML_PATH_MAX_RESULTS_COUNT)
            ?: (string)ElasticSearch::SEARCH_QUERY_SIZE;
    }
}
