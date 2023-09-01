<?php

declare(strict_types=1);

namespace Freento\FastSearchAutocomplete\Model\Attribute\Backend;

use Freento\FastSearchAutocomplete\Setup\Patch\Data\AddOriginalNameAttribute;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;

class OriginalName extends AbstractBackend
{
    /**
     * Fills original name attribute value with product name
     *
     * @param ProductInterface $object
     * @return $this
     */
    public function beforeSave($object): OriginalName
    {
        $object->setData(AddOriginalNameAttribute::ORIGINAL_NAME_ATTRIBUTE_CODE, $object->getName());

        return $this;
    }
}
