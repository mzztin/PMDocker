<?php

declare(strict_types=1);

namespace Fadhel\PMDocker;

use Fadhel\PMDocker\exception\CouldNotStartContainer;
use Fadhel\PMDocker\mapping\Environment;
use Fadhel\PMDocker\mapping\Label;
use Fadhel\PMDocker\mapping\Port;
use Fadhel\PMDocker\mapping\Volume;
use Fadhel\PMDocker\utils\Macroable;
use Symfony\Component\Process\Process;

class Container
{
    use Macroable;

    /** @var string */
    public $image = '';
    /** @var string */
    public $name = '';
    /** @var bool */
    public $daemonize = true;
    /** @var Port[] */
    public $portMappings = [];
    /** @var Environment[] */
    public $environmentMappings = [];
    /** @var Volume[] */
    public $volumeMappings = [];
    /** @var Label[] */
    public $labelMappings = [];
    /** @var bool */
    public $cleanUpAfterExit = true;
    /** @var bool */
    public $stopOnDestruct = false;

    public function __construct(string $image, string $name = '')
    {
        $this->image = $image;
        $this->name = $name;
    }

    public static function create(...$args): self
    {
        return new static(...$args);
    }

    public function image(string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function daemonize(bool $daemonize = true): self
    {
        $this->daemonize = $daemonize;
        return $this;
    }

    public function doNotDaemonize(): self
    {
        $this->daemonize = false;
        return $this;
    }

    public function cleanUpAfterExit(bool $cleanUpAfterExit): self
    {
        $this->cleanUpAfterExit = $cleanUpAfterExit;
        return $this;
    }

    public function doNotCleanUpAfterExit(): self
    {
        $this->cleanUpAfterExit = false;
        return $this;
    }

    public function mapPort(int $portOnHost, $portOnDocker): self
    {
        $this->portMappings[] = new Port($portOnHost, $portOnDocker);
        return $this;
    }

    public function setEnvironmentVariable(string $envName, string $envValue): self
    {
        $this->environmentMappings[] = new Environment($envName, $envValue);
        return $this;
    }

    public function setVolume(string $pathOnHost, string $pathOnDocker): self
    {
        $this->volumeMappings[] = new Volume($pathOnHost, $pathOnDocker);
        return $this;
    }

    public function setLabel(string $labelName, string $labelValue): self
    {
        $this->labelMappings[] = new Label($labelName, $labelValue);
        return $this;
    }

    public function stopOnDestruct(bool $stopOnDestruct = true): self
    {
        $this->stopOnDestruct = $stopOnDestruct;
        return $this;
    }

    public function start(): ContainerInstance
    {
        $command = $this->getStartCommand();
        $process = Process::fromShellCommandline($command);
        $process->run();
        if (!$process->isSuccessful()) {
            throw CouldNotStartContainer::processFailed($this, $process);
        }
        $dockerIdentifier = $process->getOutput();
        return new ContainerInstance($this, $dockerIdentifier, $this->name);
    }

    public function getStartCommand(): string
    {
        return "docker run {$this->getExtraOptions()} {$this->image}";
    }

    protected function getExtraOptions(): string
    {
        $extraOptions = [];
        if (count($this->portMappings)) {
            $extraOptions[] = implode(' ', $this->portMappings);
        }
        if (count($this->environmentMappings)) {
            $extraOptions[] = implode(' ', $this->environmentMappings);
        }
        if (count($this->volumeMappings)) {
            $extraOptions[] = implode(' ', $this->volumeMappings);
        }
        if (count($this->labelMappings)) {
            $extraOptions[] = implode(' ', $this->labelMappings);
        }
        if ($this->name !== '') {
            $extraOptions[] = "--name {$this->name}";
        }
        if ($this->daemonize) {
            $extraOptions[] = '-d';
        }
        if ($this->cleanUpAfterExit) {
            $extraOptions[] = '--rm';
        }
        return implode(' ', $extraOptions);
    }
}