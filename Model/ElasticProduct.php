<?php

declare(strict_types=1);

namespace Freento\FastSearchAutocomplete\Model;

use Freento\FastSearchAutocomplete\Api\Data\ElasticProductInterface;
use Magento\Framework\DataObject;

class ElasticProduct extends DataObject implements ElasticProductInterface
{
    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return (string)$this->getData(self::ID);
    }

    /**
     * @inheritDoc
     */
    public function setId(string $id): void
    {
        $this->setData(self::ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getOriginalName(): string
    {
        return (string)$this->getData(self::ORIGINAL_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setOriginalName(string $originalName): void
    {
        $this->setData(self::ORIGINAL_NAME, $originalName);
    }
}
