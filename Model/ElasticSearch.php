<?php

declare(strict_types=1);

namespace Freento\FastSearchAutocomplete\Model;

use Freento\FastSearchAutocomplete\Helper\StoreHelper;
use Magento\AdvancedSearch\Model\Client\ClientInterface;
use Magento\Framework\Search\Request\Builder as RequestBuilder;
use Magento\Elasticsearch\SearchAdapter\ConnectionManager;
use Magento\Elasticsearch7\SearchAdapter\Mapper;
use Magento\Framework\Search\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Freento\FastSearchAutocomplete\Api\Data\ElasticProductInterface;
use Freento\FastSearchAutocomplete\Api\Data\ElasticProductInterfaceFactory;
use Freento\FastSearchAutocomplete\Exception\ElasticConnectionException;

class ElasticSearch
{
    private const SEARCH_QUERY_NAME = 'freento_quick_search_product_ids_names';
    private const SEARCH_QUERY_SEARCH_TERM = 'search_term';
    private const SEARCH_QUERY_FROM = 0;
    public const SEARCH_QUERY_SIZE = 10;

    /**
     * @var RequestBuilder
     */
    private RequestBuilder $requestBuilder;

    /**
     * @var Mapper
     */
    private Mapper $mapper;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var ConnectionManager
     */
    private ConnectionManager $connectionManager;

    /**
     * @var ElasticProductInterfaceFactory
     */
    private ElasticProductInterfaceFactory $elasticProductFactory;

    /**
     * @var StoreHelper
     */
    private StoreHelper $storeHelper;

    /**
     * @param RequestBuilder $requestBuilder
     * @param Mapper $mapper
     * @param StoreManagerInterface $storeManager
     * @param ConnectionManager $connectionManager
     * @param ElasticProductInterfaceFactory $elasticProductFactory
     * @param StoreHelper $storeHelper
     */
    public function __construct(
        RequestBuilder $requestBuilder,
        Mapper $mapper,
        StoreManagerInterface $storeManager,
        ConnectionManager $connectionManager,
        ElasticProductInterfaceFactory $elasticProductFactory,
        StoreHelper $storeHelper
    ) {
        $this->requestBuilder = $requestBuilder;
        $this->mapper = $mapper;
        $this->storeManager = $storeManager;
        $this->connectionManager = $connectionManager;
        $this->elasticProductFactory = $elasticProductFactory;
        $this->storeHelper = $storeHelper;
    }

    /**
     * Get search result by query.
     *
     * @param string $query
     * @return ElasticProductInterface[]
     * @throws ElasticConnectionException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function search(string $query): array
    {
        $request = $this->getRequest($query);
        $requestResult = $this->getResponse($request);
        return $this->getItemsFromResponse($requestResult);
    }

    /**
     * Get connection.
     *
     * @return ClientInterface
     * @throws ElasticConnectionException
     */
    private function getConnection(): ClientInterface
    {
        $connection = $this->connectionManager->getConnection();
        if (!$connection->testConnection()) {
            throw new ElasticConnectionException(__('Couldn\'t connect to elastic'));
        }

        return $connection;
    }

    /**
     * Get request with prepared parameters.
     *
     * @param string $query
     * @return RequestInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getRequest(string $query): RequestInterface
    {
        $this->requestBuilder->setRequestName(self::SEARCH_QUERY_NAME)
            ->bindDimension('scope', (string)$this->storeManager->getStore()->getId())
            ->bind(self::SEARCH_QUERY_SEARCH_TERM, $query)
            ->setFrom(self::SEARCH_QUERY_FROM)
            ->setSize($this->storeHelper->getMaxResultsCount())
            ->setSort([]);
        return $this->requestBuilder->create();
    }

    /**
     * Get response by request.
     *
     * @param RequestInterface $request
     * @return array
     * @throws ElasticConnectionException
     */
    private function getResponse(RequestInterface $request): array
    {
        /**
         * @var $client \Magento\Elasticsearch7\Model\Client\Elasticsearch
         */
        $client = $this->getConnection();
        $query = $this->getQuery($request);
        return $client->query($query);
    }

    /**
     * Get query.
     *
     * @param RequestInterface $request
     * @return array
     */
    private function getQuery(RequestInterface $request): array
    {
        $query = $this->mapper->buildQuery($request);
        $body = $query['body'] ?? [];
        $body['_source'] = [ElasticProductInterface::ORIGINAL_NAME];
        if (isset($body['stored_fields'])) {
            unset($body['stored_fields']);
        }

        $query['body'] = $body;
        return $query;
    }

    /**
     * Get items from response.
     *
     * @param array $rawResponse
     * @return ElasticProductInterface[]
     */
    private function getItemsFromResponse(array $rawResponse): array
    {
        $items = $rawResponse['hits']['hits'] ?? [];
        $productData = [];
        foreach ($items as $item) {
            if (isset($item['_id'], $item['_source'][ElasticProductInterface::ORIGINAL_NAME])) {
                $originalName = $item['_source'][ElasticProductInterface::ORIGINAL_NAME];
                if (is_array($originalName)) {
                    $originalName = current($originalName);
                }

                $productItem = $this->elasticProductFactory->create();
                $productItem->setId($item['_id']);
                $productItem->setOriginalName($originalName);
                $productData[] = $productItem;
            }
        }

        return $productData;
    }
}
