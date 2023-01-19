<?php

declare(strict_types=1);

use App\Domain\User\UserRepository;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    // Here we map our UserRepository interface to its in memory implementation
    $containerBuilder->addDefinitions([
        UserRepository::class => function (\Doctrine\ORM\EntityManagerInterface $em) {
            return $em->getRepository(\App\Domain\User\User::class);
        }
    ]);
};
