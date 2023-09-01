<?php

declare(strict_types=1);

namespace Freento\FastSearchAutocomplete\Controller\Ajax;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Psr\Log\LoggerInterface;
use Freento\FastSearchAutocomplete\Helper\GenerateConfigHelper;

class Generate extends Action implements HttpGetActionInterface
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var GenerateConfigHelper
     */
    private GenerateConfigHelper $generateHelper;

    /**
     * @param Context $context
     * @param LoggerInterface $logger
     * @param GenerateConfigHelper $generateHelper
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        GenerateConfigHelper $generateHelper
    ) {
        $this->logger = $logger;
        $this->generateHelper = $generateHelper;
        parent::__construct($context);
    }

    /**
     * Create config file.
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $message = __('Success');
        try {
            $this->generateHelper->generateConfigFile();
            $resultJson->setHttpResponseCode(200);
        } catch (\Exception $e) {
            $message = __('Error. View log for details.');
            $resultJson->setHttpResponseCode(503);
            $this->logger->warning($e->getMessage(), $e->getTrace());
        }

        return $resultJson->setData(['message' => $message]);
    }
}
