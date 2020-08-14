<?php

declare(strict_types=1);

namespace Fadhel\PMDocker\mapping;

class Environment
{
    /** @var string */
    private $name;
    /** @var string */
    private $value;

    public function __construct(string $name, string $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return "-e {$this->name}={$this->value}";
    }
}