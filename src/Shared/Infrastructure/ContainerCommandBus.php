<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure;

use App\Shared\Application\CommandBusInterface;
use Psr\Container\ContainerInterface;

/**
 * Command Bus adapter that resolves handlers from the DI container.
 *
 * Uses a naming convention: {Name}Command → {Name}Handler in the same namespace.
 * Handlers must be invokable (__invoke).
 */
final class ContainerCommandBus implements CommandBusInterface
{
    public function __construct(
        private readonly ContainerInterface $container,
    ) {
    }

    public function dispatch(object $command): mixed
    {
        $commandClass = $command::class;
        $handlerClass = preg_replace('/Command$/', 'Handler', $commandClass);

        if ($handlerClass === null || $handlerClass === $commandClass) {
            throw new \InvalidArgumentException(
                "Command class name must end with 'Command': {$commandClass}",
            );
        }

        if (! $this->container->has($handlerClass)) {
            throw new \RuntimeException(
                "No handler registered for {$commandClass} (expected {$handlerClass})",
            );
        }

        /** @var callable(object): mixed $handler */
        $handler = $this->container->get($handlerClass);

        return $handler($command);
    }
}
