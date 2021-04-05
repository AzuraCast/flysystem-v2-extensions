<?php

namespace Azura\Files\Adapter;

interface LocalAdapterInterface extends ExtendedAdapterInterface
{
    public function getLocalPath(string $path): string;
}
