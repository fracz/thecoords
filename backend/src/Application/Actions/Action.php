<?php

declare(strict_types=1);

namespace App\Application\Actions;

use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\User\BelongsToUser;
use App\Domain\User\User;
use App\Domain\User\UserRepository;
use Assert\Assertion;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpNotFoundException;

abstract class Action
{
    protected LoggerInterface $logger;

    protected EntityManagerInterface $entityManager;

    protected Request $request;

    protected Response $response;

    protected array $args;

    /** @Inject */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /** @Inject */
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @throws HttpNotFoundException
     * @throws HttpBadRequestException
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;

        try {
            $this->beforeAction();
            return $this->action();
        } catch (DomainRecordNotFoundException $e) {
            throw new HttpNotFoundException($this->request, $e->getMessage());
        }
    }

    protected function beforeAction(): void
    {
    }

    /**
     * @throws DomainRecordNotFoundException
     * @throws HttpBadRequestException
     */
    abstract protected function action(): Response;

    /**
     * @return array|object
     */
    protected function getFormData()
    {
        return $this->request->getParsedBody();
    }

    /**
     * @return mixed
     * @throws HttpBadRequestException
     */
    protected function resolveArg(string $name)
    {
        if (!isset($this->args[$name])) {
            throw new HttpBadRequestException($this->request, "Could not resolve argument `{$name}`.");
        }
        return $this->args[$name];
    }

    /**
     * @param array|object|null $data
     */
    protected function respondWithData($data = null, int $statusCode = 200): Response
    {
        $payload = new ActionPayload($statusCode, $data);
        return $this->respond($payload);
    }

    protected function respond(ActionPayload $payload): Response
    {
        $data = $payload->getError() ? $payload : $payload->getData();
        $json = json_encode($data);
        $this->response->getBody()->write($json);
        return $this->response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($payload->getStatusCode());
    }

    protected function ensureExists($object)
    {
        if (!$object) {
            throw new HttpNotFoundException($this->request);
        }
        return $object;
    }

    protected function ensureBelongsToCurrentUser(?BelongsToUser $entity): BelongsToUser
    {
        $this->ensureExists($entity);
        if ($this->getCurrentUserId() !== $entity->getUser()->getId()->toString()) {
            throw new HttpForbiddenException($this->request);
        }
        return $entity;
    }

    protected function getCurrentUserId(): string
    {
        $userId = $this->request->getAttribute('userId');
        Assertion::uuid($userId);
        return $userId;
    }

    protected function getCurrentUser(): User
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);
        return $this->ensureExists($userRepository->findById($this->getCurrentUserId()));
    }
}
