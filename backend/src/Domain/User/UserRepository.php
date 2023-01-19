<?php

declare(strict_types=1);

namespace App\Domain\User;

interface UserRepository
{
    public function findById(string $id): ?User;

    public function findByShortId(string $shortId): ?User;

    public function persist(User $user): void;
}
