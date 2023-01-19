<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

class GetCurrentUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $user = $this->userRepository->findById($this->getCurrentUserId());
        if (!$user) {
            throw new HttpNotFoundException($this->request);
        }
        return $this->respondWithData($user);
    }
}
