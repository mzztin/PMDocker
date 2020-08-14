<?php

declare(strict_types=1);

namespace Fadhel\PMDocker\mapping;

class Port
{
    /** @var int */
    private $portOnHost;
    /** @var int */
    private $portOnDocker;

    public function __construct(int $portOnHost, int $portOnDocker)
    {
        $this->portOnHost = $portOnHost;
        $this->portOnDocker = $portOnDocker;
    }

    public function getPortOnHost(): int
    {
        return $this->portOnHost;
    }

    public function getPortOnDocker(): int
    {
        return $this->portOnDocker;
    }

    public function __toString(): string
    {
        return "-p {$this->portOnHost}:{$this->portOnDocker}";
    }
}