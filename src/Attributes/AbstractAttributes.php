<?php

namespace Azura\Files\Attributes;

use League\Flysystem\ProxyArrayAccessToProperties;
use League\Flysystem\StorageAttributes;
use League\Flysystem\UnableToRetrieveMetadata;

abstract class AbstractAttributes implements StorageAttributes
{
    use ProxyArrayAccessToProperties;

    protected string $type;

    protected string $path;

    /**
     * @var string|callable|null
     */
    protected $visibility;

    /**
     * @var int|callable|null
     */
    protected $lastModified;

    public function path(): string
    {
        return $this->path;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function visibility(): ?string
    {
        $visibility = (is_callable($this->visibility))
            ? ($this->visibility)($this->path)
            : $this->visibility;

        return $visibility;
    }

    public function lastModified(): ?int
    {
        $lastModified = is_callable($this->lastModified)
            ? ($this->lastModified)($this->path)
            : $this->lastModified;

        if (null === $lastModified) {
            throw UnableToRetrieveMetadata::lastModified($this->path);
        }

        return $lastModified;
    }

    public function isFile(): bool
    {
        return (StorageAttributes::TYPE_FILE === $this->type);
    }

    public function isDir(): bool
    {
        return (StorageAttributes::TYPE_DIRECTORY === $this->type);
    }
}
