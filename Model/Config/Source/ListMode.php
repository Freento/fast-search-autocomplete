<?php

declare(strict_types=1);

namespace Freento\FastSearchAutocomplete\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Freento\FastSearchAutocomplete\ViewModel\SearchForm;

class ListMode implements OptionSourceInterface
{
    /**
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => SearchForm::SEARCH_MODE_NATIVE, 'label' => __('Native Magento')],
            ['value' => SearchForm::SEARCH_MODE_DIRECT, 'label' => __('Direct Elastic')]
        ];
    }
}
