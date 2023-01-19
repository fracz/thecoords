<?php

declare(strict_types=1);

use App\Application\Settings\AppSettings;
use App\Infrastructure\AppContainer;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        AppSettings::class => function (\Psr\Container\ContainerInterface $container) {
            $config = $container->get('appConfig');
            $devMode = $config['devMode'] ?? false;
            return new AppSettings([
                'url' => $config['url'],
                'devMode' => $devMode,
                'displayErrorDetails' => $devMode,
                'logError' => $devMode,
                'logErrorDetails' => $devMode,
                'jwtSecret' => $config['jwtSecret'],
                'logLevels' => $config['logLevels'] ?? [],
                'doctrine' => [
                    'dev_mode' => $devMode,
                    'cache_dir' => AppContainer::VAR_DIR . '/cache/doctrine',
                    'metadata_dirs' => [realpath(__DIR__ . '/../src/Domain')],
                    'connection' => [
                        'driver' => 'pdo_mysql',
                        'charset' => 'utf8',
                        'host' => $config['db']['host'] ?? '127.0.0.1',
                        'port' => $config['db']['port'] ?? 3306,
                        'dbname' => $config['db']['dbname'] ?? 'thecoords',
                        'user' => $config['db']['user'],
                        'password' => $config['db']['password'],
                    ]
                ],
            ]);
        }
    ]);
};
