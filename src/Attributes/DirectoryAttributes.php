<?php

declare(strict_types=1);

namespace Azura\Files\Attributes;

use League\Flysystem\StorageAttributes;

class DirectoryAttributes extends AbstractAttributes
{
    /**
     * @param string $path
     * @param string|callable|null $visibility
     * @param int|callable|null $lastModified
     */
    public function __construct(string $path, $visibility = null, $lastModified = null)
    {
        $this->type = StorageAttributes::TYPE_DIRECTORY;

        $this->path = $path;
        $this->visibility = $visibility;
        $this->lastModified = $lastModified;
    }

    public static function fromArray(array $attributes): self
    {
        return new self(
            $attributes[StorageAttributes::ATTRIBUTE_PATH],
            $attributes[StorageAttributes::ATTRIBUTE_VISIBILITY] ?? null,
            $attributes[StorageAttributes::ATTRIBUTE_LAST_MODIFIED] ?? null
        );
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            StorageAttributes::ATTRIBUTE_TYPE => $this->type,
            StorageAttributes::ATTRIBUTE_PATH => $this->path,
            StorageAttributes::ATTRIBUTE_VISIBILITY => $this->visibility,
            StorageAttributes::ATTRIBUTE_LAST_MODIFIED => $this->lastModified,
        ];
    }
}
