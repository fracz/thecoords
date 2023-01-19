<?php

namespace App\Application\Actions\Tokens;

use App\Application\Settings\AppSettings;
use App\Domain\User\User;
use Assert\Assertion;
use Firebase\JWT\JWT;

class JwtToken
{
    private $tokenData = [];

    private const ALLOWED_STATES = ['deleteAccount'];

    public static function create(): self
    {
        return new self();
    }

    public function user(User $user, string $username = null): JwtToken
    {
        $this->tokenData['user'] = [
            'id' => $user->getId()->toString(),
            'username' => $username,
            'regulationsAccepted' => $user->isRegulationsAccepted(),
        ];
        return $this;
    }

    public function state(?string $state): self
    {
        if ($state) {
            Assertion::inArray($state, self::ALLOWED_STATES);
            $this->tokenData['state'] = $state;
        }
        return $this;
    }

    public function issue(AppSettings $settings): string
    {
        $now = time();
        $expirationTime = ($this->tokenData['state'] ?? null) === 'deleteAccount' ? 300 : 3600;
        $token = array_merge($this->tokenData, [
            'iss' => $settings->getUrl(),
            'iat' => $now, // issued at
            'nbf' => $now, // not before
            'exp' => $now + $expirationTime, // expires
        ]);
        return JWT::encode($token, $settings->get('jwtSecret'));
    }
}
