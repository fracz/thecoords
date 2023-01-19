<?php

namespace App\Domain\User;

interface BelongsToUser
{
    public function getUser(): User;
}