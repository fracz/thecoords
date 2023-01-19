<?php

declare(strict_types=1);

namespace Tests;

use App\Domain\Caller\PhoneNumber;
use App\Domain\User\User;

trait EntityHelpers
{
    protected function persist($entity)
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    protected function createUser(): User
    {
        $user = new User(md5((string)microtime(true)));
        $user->setAccessToken('abc')
            ->setRefreshToken('cba')
            ->setTokenExpirationTime(new \DateTime('+300seconds'))
            ->setTimezone('Europe/Warsaw')
            ->setLocale('PL');
        $this->persist($user);
        return $user;
    }
}
