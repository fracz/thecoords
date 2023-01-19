<?php

declare(strict_types=1);

use App\Infrastructure\LoggerFactory;
use DI\ContainerBuilder;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (LoggerFactory $loggerFactory) {
            return $loggerFactory->createLogger('app.log');
        },
    ]);
};
