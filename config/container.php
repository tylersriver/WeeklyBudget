<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Slim\Views\Twig;
use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\Config\MySQLDriverConfig;
use Cycle\Database\Config\MySQL\TcpConnectionConfig;
use Cycle\Database\DatabaseManager;

$builder = new ContainerBuilder();

$builder->addDefinitions([

    'settings' => static function (): array {
        return require __DIR__ . '/settings.php';
    },

    Twig::class => static function (ContainerInterface $c): Twig {
        $settings = $c->get('settings')['twig'];
        return Twig::create($settings['path'], [
            'cache' => $settings['cache'],
        ]);
    },

    DatabaseManager::class => static function (ContainerInterface $c): DatabaseManager {
        $db = $c->get('settings')['db'];
        return new DatabaseManager(new DatabaseConfig([
            'default'     => 'default',
            'databases'   => [
                'default' => ['connection' => 'mysql'],
            ],
            'connections' => [
                'mysql' => new MySQLDriverConfig(
                    connection: new TcpConnectionConfig(
                        database: $db['database'],
                        host:     $db['host'],
                        port:     $db['port'],
                        user:     $db['user'],
                        password: $db['password'],
                    ),
                    reconnect: true,
                ),
            ],
        ]));
    },

    // Domain → Infrastructure bindings (Ports → Adapters)
    App\Budget\Domain\BudgetRepositoryInterface::class           => DI\autowire(App\Budget\Infrastructure\CycleBudgetRepository::class),
    App\Transaction\Domain\TransactionRepositoryInterface::class => DI\autowire(App\Transaction\Infrastructure\CycleTransactionRepository::class),
]);

return $builder->build();
