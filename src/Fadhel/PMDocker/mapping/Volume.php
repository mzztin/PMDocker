<?php

declare(strict_types=1);

namespace Fadhel\PMDocker\mapping;

class Volume
{
    /** @var string */
    private $pathOnHost;
    /** @var string */
    private $pathOnDocker;

    public function __construct(string $pathOnHost, string $pathOnDocker)
    {
        $this->pathOnHost = $pathOnHost;
        $this->pathOnDocker = $pathOnDocker;
    }

    public function getPathOnHost(): string
    {
        return $this->pathOnHost;
    }

    public function getPathOnDocker(): string
    {
        return $this->pathOnDocker;
    }

    public function __toString(): string
    {
        return "-v {$this->pathOnHost}:{$this->pathOnDocker}";
    }
}