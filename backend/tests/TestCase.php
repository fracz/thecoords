<?php

declare(strict_types=1);

namespace Tests;

use App\Application\Actions\Tokens\JwtToken;
use App\Application\Cli\CliApp;
use App\Application\Settings\AppSettings;
use App\Domain\User\User;
use App\Infrastructure\AppContainer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase as PHPUnit_TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request as SlimRequest;
use Slim\Psr7\Uri;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

class TestCase extends PHPUnit_TestCase
{
    private static $dataForTests = [];
    use ProphecyTrait;
    use EntityHelpers;

    protected App $app;
    protected CliApp $cli;

    /** @before */
    public function initializeTest()
    {
        $this->app = $this->getAppInstance();
        $this->cli = $this->app->getContainer()->get(CliApp::class);
        $this->cli->setAutoExit(false);
        $initializedAtLeastOnce = isset(self::$dataForTests[static::class]);
        if (!$initializedAtLeastOnce || !$this->isSmall() || !defined('INTEGRATION_TESTS_BOOTSTRAPPED')) {
            defined('INTEGRATION_TESTS_BOOTSTRAPPED') || define('INTEGRATION_TESTS_BOOTSTRAPPED', true);
            $this->executeCommand('orm:schema-tool:drop --force');
            $this->executeCommand('orm:schema-tool:create');
            $this->executeCommand('orm:generate-proxies');
            $this->initializeDatabaseForTests();
            self::$dataForTests[static::class] = true;
        }
    }

    protected function initializeDatabaseForTests()
    {
    }

    /** @param array|string $command */
    protected function executeCommand($command, $ignoreFailure = false): string
    {
        if (is_array($command)) {
            $input = new ArrayInput($command);
        } else {
            $input = new StringInput($command);
        }
        $output = new BufferedOutput();
        $input->setInteractive(false);
        $this->cli->setCatchExceptions(false);
        $error = $this->cli->run($input, $output);
        $result = $output->fetch();
        if ($error && !$ignoreFailure) {
            $this->fail("Command error: $command\nReturn code: $error\nOutput:\n$result");
        }
        return $result;
    }

    protected function getAppInstance(): App
    {
        $container = AppContainer::getContainer('config.test.yml');
        AppFactory::setContainer($container);
        $app = AppFactory::create();
        $middleware = require __DIR__ . '/../app/middleware.php';
        $middleware($app);
        $routes = require __DIR__ . '/../app/routes.php';
        $routes($app);
        foreach ($this->getDiMocks() as $serviceName => $serviceImpl) {
            $container->set($serviceName, $serviceImpl);
        }
        return $app;
    }

    protected function getDiMocks(): array
    {
        return [];
    }

    protected function get(string $class)
    {
        return $this->app->getContainer()->get($class);
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->get(EntityManagerInterface::class);
    }

    protected function getRepository(string $entityClass): EntityRepository
    {
        return $this->getEntityManager()->getRepository($entityClass);
    }

    protected function createRequest(?User $user, string $method, string $path, ?array $body = []): Request
    {
        $headers = ['Accept' => 'application/json', 'Content-Type' => 'application/json'];
        if ($user) {
            $settings = $this->getAppInstance()->getContainer()->get(AppSettings::class);
            $token = JwtToken::create()->user($user)->issue($settings);
            $headers['Authorization'] = "Bearer $token";
        }
        $uri = new Uri('https', '', 80, $path);
        $handle = fopen('php://temp', 'w+');
        $stream = (new StreamFactory())->createStreamFromResource($handle);

        $h = new Headers();
        foreach ($headers as $name => $value) {
            $h->addHeader($name, $value);
        }

        $request = new SlimRequest($method, $uri, $h, [], [], $stream);
        if ($body) {
            $request = $request->withParsedBody($body);
        }
        return $request;
    }
}
