<?php

namespace App\Application\Cli;

use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\ConfigurationArray;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

class CliApp extends Application
{
    public function __construct(ContainerInterface $container, EntityManagerInterface $entityManager)
    {
        parent::__construct('thecoords', '1.0.0');
        $this->addCommands(array_merge($this->detectCommands($container, __DIR__)));
        $entityManagerProvider = new SingleManagerProvider($entityManager);
        \Doctrine\ORM\Tools\Console\ConsoleRunner::addCommands($this, $entityManagerProvider);
        $this->configureMigrations($entityManager);
    }

    private function detectCommands(ContainerInterface $container, string $directory): array
    {
        return array_filter(array_map(function (string $filename) use ($container) {
            if (preg_match('#^(.+)Command.php$#', $filename, $match)) {
                $fullClassName = 'App\\Application\\Cli\\' . $match[1] . 'Command';
                return $container->get($fullClassName);
            } else {
                return null;
            }
        }, scandir($directory)));
    }

    private function configureMigrations(EntityManagerInterface $entityManager)
    {
        $emLoader = new ExistingEntityManager($entityManager);
        $configuration = new ConfigurationArray([
            'table_storage' => [
                'table_name' => 'doctrine_migration_versions',
                'version_column_name' => 'version',
                'version_column_length' => 1024,
                'executed_at_column_name' => 'executed_at',
                'execution_time_column_name' => 'execution_time',
            ],
            'migrations_paths' => [
                'App\Infrastructure\Migrations' => __DIR__ . '/../../Infrastructure/Migrations',
            ],
            'all_or_nothing' => true,
            'transactional' => true,
            'check_database_platform' => false,
        ]);
        $di = DependencyFactory::fromEntityManager($configuration, $emLoader);
        \Doctrine\Migrations\Tools\Console\ConsoleRunner::addCommands($this, $di);
    }
}
