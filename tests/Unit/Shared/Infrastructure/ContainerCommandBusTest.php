<?php

declare(strict_types=1);

use App\Shared\Infrastructure\ContainerCommandBus;
use Psr\Container\ContainerInterface;

describe('ContainerCommandBus', function () {
    it('dispatches a command to its resolved handler', function () {
        $handler = new class () {
            public bool $called = false;
            public ?object $received = null;

            public function __invoke(object $command): string
            {
                $this->called = true;
                $this->received = $command;
                return 'handled';
            }
        };

        $container = Mockery::mock(ContainerInterface::class);
        $container->allows('has')
            ->with('App\Budget\Application\Command\UpdateBudgetHandler')
            ->andReturn(true);
        $container->allows('get')
            ->with('App\Budget\Application\Command\UpdateBudgetHandler')
            ->andReturn($handler);

        $bus = new ContainerCommandBus($container);
        $command = new App\Budget\Application\Command\UpdateBudgetCommand(type: 'weekly', amount: 300, userId: 1);
        $result = $bus->dispatch($command);

        expect($result)->toBe('handled');
        expect($handler->called)->toBeTrue();
        expect($handler->received)->toBe($command);
    });

    it('throws when command class name does not end with Command', function () {
        $container = Mockery::mock(ContainerInterface::class);
        $bus = new ContainerCommandBus($container);

        $bus->dispatch(new stdClass());
    })->throws(InvalidArgumentException::class, "Command class name must end with 'Command'");

    it('throws when handler is not registered', function () {
        $container = Mockery::mock(ContainerInterface::class);
        $container->allows('has')
            ->with('App\Budget\Application\Command\UpdateBudgetHandler')
            ->andReturn(false);

        $bus = new ContainerCommandBus($container);

        $bus->dispatch(
            new App\Budget\Application\Command\UpdateBudgetCommand(type: 'weekly', amount: 100, userId: 1)
        );
    })->throws(RuntimeException::class, 'No handler registered for');
});
