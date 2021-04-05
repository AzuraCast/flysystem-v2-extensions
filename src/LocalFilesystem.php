<?php

namespace Azura\Files;

use Azura\Files\Adapter\LocalAdapterInterface;
use League\Flysystem\PathNormalizer;

class LocalFilesystem extends AbstractFilesystem
{
    protected LocalAdapterInterface $localAdapter;

    public function __construct(
        LocalAdapterInterface $adapter,
        array $config = [],
        PathNormalizer $pathNormalizer = null
    ) {
        $this->localAdapter = $adapter;

        parent::__construct($adapter, $config, $pathNormalizer);
    }

    /** @inheritDoc */
    public function isLocal(): bool
    {
        return true;
    }

    /** @inheritDoc */
    public function getLocalPath(string $path): string
    {
        return $this->localAdapter->getLocalPath($path);
    }

    /** @inheritDoc */
    public function upload(string $localPath, string $to): void
    {
        $destPath = $this->getLocalPath($to);
        copy($localPath, $destPath);
    }

    /** @inheritDoc */
    public function download(string $from, string $localPath): void
    {
        $sourcePath = $this->getLocalPath($from);
        copy($sourcePath, $localPath);
    }

    /** @inheritDoc */
    public function withLocalFile(string $path, callable $function)
    {
        $localPath = $this->getLocalPath($path);
        return $function($localPath);
    }
}
