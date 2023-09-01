<?php

declare(strict_types=1);

namespace Freento\FastSearchAutocomplete\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Filesystem\Directory\WriteFactory;
use Magento\Framework\Filesystem\DirectoryList;

class GenerateConfigHelper extends AbstractHelper
{
    private const CONFIG_FILE_NAME = 'freento-elastic-config.json';

    /**
     * @var WriteFactory
     */
    private WriteFactory $writeFactory;

    /**
     * @var StoreHelper
     */
    private StoreHelper $storeHelper;

    /**
     * @var DirectoryList
     */
    private DirectoryList $directoryList;

    /**
     * @param Context $context
     * @param WriteFactory $writeFactory
     * @param StoreHelper $storeHelper
     * @param DirectoryList $directoryList
     */
    public function __construct(
        Context $context,
        WriteFactory $writeFactory,
        StoreHelper $storeHelper,
        DirectoryList $directoryList
    ) {
        $this->writeFactory = $writeFactory;
        $this->storeHelper = $storeHelper;
        $this->directoryList = $directoryList;
        parent::__construct($context);
    }

    /**
     * Generate config file.
     *
     * @return void
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    public function generateConfigFile()
    {
        $config = [
            'hostname' => $this->storeHelper->getElasticsearchHostname(),
            'port' => $this->storeHelper->getElasticsearchPort(),
            'index' => $this->storeHelper->getElasticsearchIndexPrefix(),
            'enableAuth' => $this->storeHelper->getElasticsearchEnableAuth(),
            'username' => $this->storeHelper->getElasticsearchUsername(),
            'password' => $this->storeHelper->getElasticsearchPassword(),
            'timeout' => $this->storeHelper->getElasticsearchTimeout(),
            'engine' => $this->storeHelper->getSearchEngine(),
            'max_results_count' => $this->storeHelper->getMaxResultsCount(),
            'max_search_result_symbols' => $this->storeHelper->getMaxSearchResultSymbols()
        ];

        $write = $this->writeFactory->create($this->directoryList->getPath('var'));
        $write->writeFile(self::CONFIG_FILE_NAME, json_encode($config));
    }
}
