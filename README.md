# Manage docker containers with PHP

This package provides a nice way to start docker containers and execute commands on them.

````php
$containerInstance = \Fadhel\PMDocker\Container::create($imageName)->start();

$process = $containerInstance->execute('whoami');

$process->getOutput(); // returns the name of the user inside the docker container
````

## Usage

You can get an instance of a docker container using

```php
$containerInstance = \Fadhel\PMDocker\Container::create($imageName)->start();
```

By default the container will be daemonized and it will be cleaned up after it exists.

### Customizing the docker container

#### Prevent daemonization

If you don't want your docker being daemonized, call `doNotDaemonize`.

```php
$containerInstance = \Fadhel\PMDocker\Container::create($imageName)
    ->doNotDaemonize()
    ->start();
```

#### Prevent automatic clean up

If you don't want your docker being cleaned up after it exists, call `doNotCleanUpAfterExit`.

```php
$containerInstance = \Fadhel\PMDocker\Container::create($imageName)
    ->doNotCleanUpAfterExit()
    ->start();
```

#### Naming the container

You can name the container by passing the name as the second argument to the constructor.

```php
new \Fadhel\PMDocker\Container($imageName, $nameOfContainer));
```

Alternatively, use the `name` method.

```php
$containerInstance = \Fadhel\PMDocker\Container::create($imageName)
    ->name($yourName)
    ->start();
```

#### Mapping ports

You can map ports between the host machine and the docker container using the `mapPort` method. To map multiple ports, just call `mapPort` multiple times.

```php
$containerInstance = \Fadhel\PMDocker\Container::create($imageName)
    ->mapPort($portOnHost, $portOnContainer)
    ->mapPort($anotherPortOnHost, $anotherPortOnContainer)
    ->start();
```

#### Environment variables

You can set environment variables using the `setEnvironmentVariable` method. To add multiple arguments, just call `setEnvironmentVariable` multiple times.

```php
$containerInstance = \Fadhel\PMDocker\Container::create($imageName)
    ->setEnvironmentVariable($variableKey, $variableValue)
    ->setEnvironmentVariable($anotherVariableKey, $anotherVariableValue)
    ->start();
```

#### Setting Volumes

You can set volumes using the `setVolume` method. To add multiple arguments, just call `setVolume` multiple times.

```php
$containerInstance = \Fadhel\PMDocker\Container::create($imageName)
    ->setVolume($pathOnHost, $pathOnDocker)
    ->setVolume($anotherPathOnHost, $anotherPathOnDocker)
    ->start();
```

#### Setting Labels

You can set labels using the `setLabel` method. To add multiple arguments, just call `setLabel` multiple times.

```php
$containerInstance = \Fadhel\PMDocker\Container::create($imageName)
    ->setLabel($labelName, $labelValue)
    ->setLabel($anotherLabelName, $anotherLabelValue)
    ->start();
```

#### Automatically stopping the container after PHP exists

When using this package in a testing environment, it can be handy that the docker container is stopped after `__destruct` is called on it (mostly this will happen when the PHP script ends). You can enable this behaviour with the `stopOnDestruct` method.

```php
$containerInstance = \Fadhel\PMDocker\Container::create($imageName)
    ->stopOnDestruct()
    ->start();
```

#### Getting the start command string

You can get the string that will be executed when a container is started with the `getStartCommand` function

```php
// returns "docker run -d --rm spatie/docker"
\Fadhel\PMDocker\Container::create($imageName)->getStartCommand();
```

### Available methods on the docker container instance

#### Executing a command

To execute a command on the container, use the `execute` method.

```php
$process = $instance->execute($command);
```

You can execute multiple command in one go by passing an array.

```php
$process = $instance->execute([$command, $anotherCommand]);
```

The execute method returns an instance of [`Symfony/Process`](https://symfony.com/doc/current/components/process.html).

You can check if your command ran successfully using the `isSuccessful` $method

```php
$process->isSuccessful(); // returns a boolean
```

You can get to the output using `getOutput()`. If the command did not run successfully, you can use `getErrorOutput()`. For more information on how to work with a `Process` head over to [the Symfony docs](https://symfony.com/doc/current/components/process.html).

#### Installing a public key

If you cant to connect to your container instance via SSH, you probably want to add a public key to it.

This can be done using the `addPublicKey` method.

```php
$instance->addPublicKey($pathToPublicKey);
```

It is assumed that the `authorized_keys` file is located in at `/root/.ssh/authorized_keys`. If this is not the case, you can specify the path of that file as a second parameter.

```php
$instance->addPublicKey($pathToPublicKey, $pathToAuthorizedKeys);
```

Note that in order to be able to connect via SSH, you should set up a SSH server in your dockerfile. Take a look at the dockerfile in the tests of this package for an example.

#### Adding files to your instance

Files can be added to an instance with `addFiles`.

```php
$instance->addFiles($fileOrDirectoryOnHost, $pathInContainer);
```

#### Adding other functions on the docker instance

The `Fadhel\PMDocker\ContainerInstance` class is [macroable](https://github.com/DimBis/PMDocker/blob/master/src/Fadhel/PMDocker/uitls/Macroable.php). This means you can add extra functions to it.

````php
\Fadhel\PMDocker\ContainerInstance::macro('whoAmI', function () {
    $process = $containerInstance->run('whoami');


    return $process->getOutput();
});

$containerInstance = \Fadhel\PMDocker\Container::create($imageName)->start();

$containerInstace->whoAmI(); // returns of name of user in the docker container
````

## Credits

- [Ruben Van Assche](https://github.com/rubenvanassche)
- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](https://github.com/spatie/docker/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
