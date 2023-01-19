<?php

declare(strict_types=1);

namespace Tests;

use App\Application\Cli\CliApp;
use App\Domain\Caller\PhoneNumber;
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


final class EntityUtils
{
    private function __construct()
    {
    }

    public static function setField($entity, string $field, $value)
    {
        (new self())->doSetField($entity, $field, $value);
    }

    private function doSetField($entity, string $field, $value)
    {
        $prop = $this->getProperty($entity, $field);
        $prop->setAccessible(true);
        $prop->setValue($entity, $value);
    }

    private function getProperty($entity, string $field): \ReflectionProperty
    {
        $rc = new \ReflectionClass($entity);
        do {
            if ($rc->hasProperty($field)) {
                return $rc->getProperty($field);
            }
        } while ($rc = $rc->getParentClass());
        throw new \InvalidArgumentException("There is no $field field in the " . get_class($entity));
    }

    public static function getField($entity, string $field)
    {
        return (new self())->doGetField($entity, $field);
    }

    private function doGetField($entity, string $field)
    {
        $getter = function (string $field) {
            return $this->{$field};
        };
        return $getter->call($entity, $field);
    }

    public static function mapToIds($entities): array
    {
        if (!is_array($entities)) {
            $entities = iterator_to_array($entities);
        }
        return array_values(array_map(function ($entity) {
            return $entity->getId();
        }, $entities));
    }
}
