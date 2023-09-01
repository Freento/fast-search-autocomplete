<?php

declare(strict_types=1);

namespace Freento\FastSearchAutocomplete\Model;

use Freento\FastSearchAutocomplete\Setup\Patch\Data\AddOriginalNameAttribute;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogSearch\Model\Indexer\Fulltext\Processor;
use Magento\Eav\Model\AttributeRepository;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManager;

class FillFastSearchName
{
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $productCollectionFactory;

    /**
     * @var StoreManager
     */
    private StoreManager $storeManager;

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @var AttributeRepository
     */
    private AttributeRepository $attributeRepository;

    /**
     * @var Processor
     */
    private Processor $fulltextIndexerProcessor;

    /**
     * @param CollectionFactory $productCollectionFactory
     * @param StoreManager $storeManager
     * @param ResourceConnection $resourceConnection
     * @param AttributeRepository $attributeRepository
     * @param Processor $fulltextIndexerProcessor
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        StoreManager $storeManager,
        ResourceConnection $resourceConnection,
        AttributeRepository $attributeRepository,
        Processor $fulltextIndexerProcessor
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->storeManager = $storeManager;
        $this->resourceConnection = $resourceConnection;
        $this->attributeRepository = $attributeRepository;
        $this->fulltextIndexerProcessor = $fulltextIndexerProcessor;
    }

    /**
     * Sets original name attribute value based on the name attribute value on the particular store
     * Invalidates catalogsearch_fulltext indexer
     *
     * @return void
     * @throws NoSuchEntityException
     */
    public function execute(): void
    {
        $originalNameAttribute = $this->attributeRepository->get(
            ProductAttributeInterface::ENTITY_TYPE_CODE,
            AddOriginalNameAttribute::ORIGINAL_NAME_ATTRIBUTE_CODE
        );
        $connection = $this->resourceConnection->getConnection();
        $productsWasUpdated = false;

        foreach ($this->storeManager->getStores(true) as $store) {
            // Skip default store
            if ($store->getCode() === 'default') {
                continue;
            }

            $dataToInsert = [];

            $productCollection = $this->productCollectionFactory
                ->create()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect(AddOriginalNameAttribute::ORIGINAL_NAME_ATTRIBUTE_CODE)
                ->setStoreId($store->getId());

            foreach ($productCollection->getItems() as $product) {
                if ($product->getName() === $product->getOriginalName()) {
                    continue;
                }

                /** @var ProductInterface $product */
                $dataToInsert[] = [
                    'attribute_id' => $originalNameAttribute->getAttributeId(),
                    'store_id' => $store->getId(),
                    'entity_id' => $product->getId(),
                    'value' => $product->getName()
                ];
            }

            if (empty($dataToInsert)) {
                continue;
            }

            $connection->insertOnDuplicate(
                $this->resourceConnection->getTableName('catalog_product_entity_varchar'),
                $dataToInsert
            );

            $productsWasUpdated = true;
        }

        // If at least one product was updated - mark catalogsearch_fulltext indexer as invalid
        if ($productsWasUpdated) {
            $this->fulltextIndexerProcessor->markIndexerAsInvalid();
        }
    }
}
