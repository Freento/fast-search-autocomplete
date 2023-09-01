<?php

declare(strict_types=1);

namespace Freento\FastSearchAutocomplete\Controller\Ajax;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Freento\FastSearchAutocomplete\Helper\StoreHelper;
use Freento\FastSearchAutocomplete\Model\ElasticSearch;
use Psr\Log\LoggerInterface;

class Suggest extends Action implements HttpGetActionInterface
{
    /**
     * @var ElasticSearch
     */
    private ElasticSearch $elasticSearch;

    /**
     * @var StoreHelper
     */
    private StoreHelper $helper;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param Context $context
     * @param ElasticSearch $elasticSearch
     * @param StoreHelper $helper
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        ElasticSearch $elasticSearch,
        StoreHelper $helper,
        LoggerInterface $logger
    ) {
        $this->elasticSearch = $elasticSearch;
        $this->helper = $helper;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Get products by search query in JSON format.
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $searchString = $this->getRequest()->getParam('q', false);
        if (!$searchString) {
            return $resultJson->setHttpResponseCode(503);
        }

        $data = [];
        try {
            $data = $this->elasticSearch->search($searchString);
        } catch (\Exception $e) {
            $resultJson->setHttpResponseCode(503);
            $this->logger->warning($e->getMessage(), $e->getTrace());
        }

        $maxSymbols = $this->helper->getMaxSearchResultSymbols();
        $responseData = [];
        foreach ($data as $item) {
            $title = $item->getOriginalName();
            if ($maxSymbols > 0 && strlen($title) > $maxSymbols) {
                $title = mb_substr($title, 0, $maxSymbols) . '...';
            }

            $responseData[] = [
                'title' =>  $title,
                'id' => $item->getId()
            ];
        }

        return $resultJson->setData($responseData);
    }
}
