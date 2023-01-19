<?php

namespace App\Infrastructure\Persistence;

use App\Domain\User\User;
use App\Domain\User\UserRepository;
use Doctrine\ORM\EntityRepository;

class DoctrineUserRepository extends EntityRepository implements UserRepository
{
    public function findById(string $id): ?User
    {
        return $this->find($id);
    }

    public function findByShortId(string $shortId): ?User
    {
        return $this->findOneBy(['shortUniqueId' => $shortId]);
    }

    public function persist(User $user): void
    {
        $this->getEntityManager()->persist($user);
    }
}
