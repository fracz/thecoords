<?php

declare(strict_types=1);

use App\Application\Settings\AppSettings;
use App\Infrastructure\Doctrine\TinyintType;
use App\Infrastructure\Doctrine\UTCDateTimeType;
use DI\ContainerBuilder;
use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Setup;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Doctrine\UuidType;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        EntityManagerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(AppSettings::class)->get('doctrine');

            $cache = $settings['dev_mode'] ?
                DoctrineProvider::wrap(new ArrayAdapter()) :
                DoctrineProvider::wrap(new FilesystemAdapter('', 0, $settings['cache_dir']));

            $config = Setup::createAnnotationMetadataConfiguration(
                $settings['metadata_dirs'],
                $settings['dev_mode'],
                null,
                $cache,
                false
            );

            $config->addCustomNumericFunction('rand', \DoctrineExtensions\Query\Mysql\Rand::class);
            $config->addCustomDatetimeFunction('dateadd', \DoctrineExtensions\Query\Mysql\DateAdd::class);
            $config->addCustomDatetimeFunction('datesub', \DoctrineExtensions\Query\Mysql\DateSub::class);

            if (!Type::hasType(UuidType::NAME)) {
                Type::addType(UuidType::NAME, UuidType::class);
                Type::addType('utcdatetime', UTCDateTimeType::class);
                Type::addType('tinyint', TinyintType::class);
            }

            return EntityManager::create($settings['connection'], $config);
        },
    ]);
};
