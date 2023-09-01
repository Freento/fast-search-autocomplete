<?php

declare(strict_types=1);

namespace Freento\FastSearchAutocomplete\Api\Data;

use Freento\FastSearchAutocomplete\Setup\Patch\Data\AddOriginalNameAttribute;

interface ElasticProductInterface
{
    /**
     * String constants for property names
     */
    public const ID = 'id';
    public const ORIGINAL_NAME = AddOriginalNameAttribute::ORIGINAL_NAME_ATTRIBUTE_CODE;

    /**
     * Getter for Id.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Setter for Id.
     *
     * @param string $id
     * @return void
     */
    public function setId(string $id): void;

    /**
     * Getter for original name
     *
     * @return string
     */
    public function getOriginalName(): string;

    /**
     * Setter for original name
     *
     * @param string $originalName
     * @return void
     */
    public function setOriginalName(string $originalName): void;
}
