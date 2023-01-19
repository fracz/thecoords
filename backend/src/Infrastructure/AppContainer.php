<?php

namespace App\Infrastructure;

use DI\Container;
use DI\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;

final class AppContainer
{
    public const VAR_DIR = __DIR__ . '/../../../var';

    public static function getContainer($configFile = 'config.yml'): Container
    {
        $config = Yaml::parseFile(self::VAR_DIR . '/config/' . $configFile)['app'];
        $containerBuilder = new ContainerBuilder();
        if (!($config['devMode'] ?? false)) {
            $containerBuilder->enableCompilation(self::VAR_DIR . '/cache/di');
        }
        $containerBuilder->addDefinitions(['appConfig' => $config]);
        $containerBuilder->useAnnotations(true);
        $settings = require __DIR__ . '/../../app/settings.php';
        $settings($containerBuilder);
        $dependencies = require __DIR__ . '/../../app/dependencies.php';
        $dependencies($containerBuilder);
        $orm = require __DIR__ . '/../../app/orm.php';
        $orm($containerBuilder);
        $repositories = require __DIR__ . '/../../app/repositories.php';
        $repositories($containerBuilder);
        return $containerBuilder->build();
    }
}
