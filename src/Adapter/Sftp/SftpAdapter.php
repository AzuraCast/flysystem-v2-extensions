<?php

namespace Azura\Files\Adapter\Sftp;

use Azura\Files\Adapter\ExtendedAdapterInterface;
use Azura\Files\Attributes\DirectoryAttributes;
use Azura\Files\Attributes\FileAttributes;
use League\Flysystem\PathPrefixer;
use League\Flysystem\PhpseclibV3\ConnectionProvider;
use League\Flysystem\StorageAttributes;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnixVisibility\VisibilityConverter;
use League\MimeTypeDetection\MimeTypeDetector;

class SftpAdapter extends \League\Flysystem\PhpseclibV3\SftpAdapter implements ExtendedAdapterInterface
{
    protected const NET_SFTP_TYPE_DIRECTORY = 2;

    protected ConnectionProvider $connectionProvider;

    protected VisibilityConverter $visibilityConverter;

    protected PathPrefixer $prefixer;

    public function __construct(
        ConnectionProvider $connectionProvider,
        string $root,
        ?VisibilityConverter $visibilityConverter = null,
        ?MimeTypeDetector $mimeTypeDetector = null
    ) {
        $this->connectionProvider = $connectionProvider;
        $this->visibilityConverter = $visibilityConverter;
        $this->prefixer = new PathPrefixer($root);

        parent::__construct($connectionProvider, $root, $visibilityConverter, $mimeTypeDetector);
    }

    /** @inheritDoc */
    public function getMetadata(string $path): StorageAttributes
    {
        $location = $this->prefixer->prefixPath($path);
        $connection = $this->connectionProvider->provideConnection();
        $stat = $connection->stat($location);

        if (!is_array($stat)) {
            throw UnableToRetrieveMetadata::create($path, 'metadata');
        }

        $attributes = $this->convertListingToAttributes($path, $stat);

        if (!$attributes instanceof FileAttributes) {
            throw UnableToRetrieveMetadata::create($path, 'metadata', 'path is not a file');
        }

        return $attributes;
    }

    protected function convertListingToAttributes(string $path, array $attributes): StorageAttributes
    {
        $permissions = $attributes['mode'] & 0777;
        $lastModified = $attributes['mtime'] ?? null;

        if ($attributes['type'] === self::NET_SFTP_TYPE_DIRECTORY) {
            return new DirectoryAttributes(
                ltrim($path, '/'),
                $this->visibilityConverter->inverseForDirectory($permissions),
                $lastModified
            );
        }

        return new FileAttributes(
            $path,
            $attributes['size'],
            $this->visibilityConverter->inverseForFile($permissions),
            $lastModified
        );
    }
}
