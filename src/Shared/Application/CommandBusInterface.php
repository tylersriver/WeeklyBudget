<?php

declare(strict_types=1);

namespace App\Shared\Application;

/**
 * Port for dispatching commands to their handlers.
 *
 * Actions depend on this abstraction instead of concrete handlers,
 * keeping the infrastructure layer decoupled from application internals.
 */
interface CommandBusInterface
{
    /**
     * Dispatch a command to its resolved handler.
     *
     * @template T
     * @param object $command The command DTO to dispatch
     * @return mixed The handler's return value
     */
    public function dispatch(object $command): mixed;
}
