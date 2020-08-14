<?php

declare(strict_types=1);

namespace Fadhel\PMDocker\exception;

use Exception;
use Fadhel\PMDocker\Container;
use Symfony\Component\Process\Process;

class CouldNotStartContainer extends Exception
{
    public static function processFailed(Container $container, Process $process)
    {
        return new static("Could not start docker container for image {$container->image}`. Process output: `{$process->getErrorOutput()}`");
    }
}