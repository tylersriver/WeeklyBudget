<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Slim\Views\Twig;
use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\Config\MySQLDriverConfig;
use Cycle\Database\Config\MySQL\TcpConnectionConfig;
use Cycle\Database\DatabaseManager;
use Cycle\ORM\Factory;
use Cycle\ORM\ORM;
use Cycle\ORM\Schema as OrmSchema;
use Cycle\Schema\Compiler;
use Cycle\Schema\Registry;
use Cycle\Schema\Generator;
use Cycle\Annotated;
use Spiral\Tokenizer\ClassLocator;
use Symfony\Component\Finder\Finder;

$builder = new ContainerBuilder();

$builder->addDefinitions([

    'settings' => function (): array {
        return require __DIR__ . '/settings.php';
    },

    Twig::class => function (ContainerInterface $c): Twig {
        $settings = $c->get('settings')['twig'];
        return Twig::create($settings['path'], [
            'cache' => $settings['cache'],
        ]);
    },

    DatabaseManager::class => function (ContainerInterface $c): DatabaseManager {
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
                ),
            ],
        ]));
    },

    ORM::class => function (ContainerInterface $c): ORM {
        $dbal = $c->get(DatabaseManager::class);

        $finder = (new Finder())->files()->in([__DIR__ . '/../src/Entity'])->name('*.php');
        $classLocator = new ClassLocator($finder);

        $schema = (new Compiler())->compile(new Registry($dbal), [
            new Generator\ResetTables(),
            new Annotated\Embeddings($classLocator),
            new Annotated\Entities($classLocator),
            new Annotated\TableInheritance(),
            new Annotated\MergeColumns(),
            new Generator\GenerateRelations(),
            new Generator\GenerateModifiers(),
            new Generator\ValidateEntities(),
            new Generator\RenderTables(),
            new Generator\RenderRelations(),
            new Generator\RenderModifiers(),
            new Annotated\MergeIndexes(),
            new Generator\GenerateTypecast(),
        ]);

        return new ORM(
            factory: new Factory($dbal),
            schema: new OrmSchema($schema),
        );
    },

    App\Repository\TransactionRepository::class => DI\autowire(),
    App\Repository\BudgetRepository::class       => DI\autowire(),
]);

return $builder->build();
