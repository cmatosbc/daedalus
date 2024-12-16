<?php

namespace Daedalus\Tests;

use PHPUnit\Framework\TestCase;
use Daedalus\ContainerArrayObject;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class ContainerArrayObjectTest extends TestCase
{
    private ContainerArrayObject $container;

    protected function setUp(): void
    {
        $this->container = new ContainerArrayObject();
    }

    /**
     * @testdox Implements PSR-11 Container Interface
     */
    public function testPsrCompliance(): void
    {
        $this->assertInstanceOf(
            ContainerInterface::class,
            $this->container,
            'Should implement PSR-11 ContainerInterface'
        );
    }

    /**
     * @testdox Can bind and retrieve basic values
     */
    public function testBasicBinding(): void
    {
        $this->container->bind('key', 'value');
        
        $this->assertTrue(
            $this->container->has('key'),
            'Should detect bound key'
        );
        $this->assertEquals(
            'value',
            $this->container->get('key'),
            'Should retrieve bound value'
        );
    }

    /**
     * @testdox Throws exception for missing entries
     */
    public function testMissingEntry(): void
    {
        $this->assertFalse(
            $this->container->has('nonexistent'),
            'Should return false for non-existent key'
        );
        
        $this->expectException(NotFoundExceptionInterface::class);
        $this->container->get('nonexistent');
    }

    /**
     * @testdox Maintains singleton instances
     */
    public function testSingletonRegistration(): void
    {
        $obj = new \stdClass();
        $obj->value = 'test';
        
        $this->container->singleton('instance', $obj);
        
        $instance1 = $this->container->get('instance');
        $instance2 = $this->container->get('instance');
        
        $this->assertSame(
            $instance1,
            $instance2,
            'Should return same instance for singleton'
        );
    }

    /**
     * @testdox Supports factory functions for instance creation
     */
    public function testFactoryFunction(): void
    {
        $count = 0;
        $this->container->bind('factory', function() use (&$count) {
            $obj = new \stdClass();
            $obj->count = ++$count;
            return $obj;
        });

        $instance1 = $this->container->get('factory');
        $instance2 = $this->container->get('factory');
        
        $this->assertNotSame(
            $instance1,
            $instance2,
            'Should return new instance each time for factory'
        );
        $this->assertEquals(1, $instance1->count);
        $this->assertEquals(2, $instance2->count);
    }

    /**
     * @testdox Supports singleton factory with dependencies
     */
    public function testSingletonFactory(): void
    {
        $count = 0;
        $this->container->singleton('singleton.factory', function() use (&$count) {
            $obj = new \stdClass();
            $obj->count = ++$count;
            return $obj;
        });

        $instance1 = $this->container->get('singleton.factory');
        $instance2 = $this->container->get('singleton.factory');
        
        $this->assertSame(
            $instance1,
            $instance2,
            'Should return same instance for singleton factory'
        );
        $this->assertEquals(1, $instance1->count);
        $this->assertEquals(1, $instance2->count);
    }

    /**
     * @testdox Automatically injects container into factory functions
     */
    public function testAutomaticDependencyInjection(): void
    {
        // Define test classes in correct order (dependencies first)
        $this->container->bind(Config::class, Config::class);
        $this->container->bind(Database::class, Database::class);
        $this->container->bind(Logger::class, Logger::class);
        $this->container->bind(UserRepository::class, UserRepository::class);

        $repo = $this->container->get(UserRepository::class);
        
        $this->assertInstanceOf(
            UserRepository::class,
            $repo,
            'Should resolve all dependencies in chain'
        );
        $this->assertInstanceOf(
            Database::class,
            $repo->getDatabase(),
            'Should resolve all dependencies in chain'
        );
        $this->assertInstanceOf(
            Logger::class,
            $repo->getLogger(),
            'Should resolve all dependencies in chain'
        );
    }

    /**
     * @testdox Can bind interfaces to implementations
     */
    public function testInterfaceBinding(): void
    {
        $this->container->bind(PaymentGatewayInterface::class, StripeGateway::class);
        
        $gateway = $this->container->get(PaymentGatewayInterface::class);
        
        $this->assertInstanceOf(
            PaymentGatewayInterface::class,
            $gateway,
            'Should resolve interface to correct implementation'
        );
        $this->assertInstanceOf(
            StripeGateway::class,
            $gateway,
            'Should resolve interface to correct implementation'
        );
    }

    /**
     * @testdox Can clear cached instances
     */
    public function testClearInstances(): void
    {
        $obj = new \stdClass();
        $obj->value = 'test';
        $this->container->singleton('singleton', $obj);
        
        $instance1 = $this->container->get('singleton');
        $this->container->clearInstances();
        
        // Get a new instance - it should be different since we cleared instances
        $instance2 = $this->container->get('singleton');
        
        // Verify that the singleton binding still exists but returns a new instance
        $this->assertTrue(
            $this->container->isSingleton('singleton'),
            'Should still be a singleton after clearing instances'
        );
        $this->assertNotSame(
            $instance1,
            $instance2,
            'Should return new instance after clearing cache'
        );
        $this->assertEquals(
            $instance1->value,
            $instance2->value,
            'Should return new instance after clearing cache'
        );
        
        // Verify that subsequent gets return the same instance
        $instance3 = $this->container->get('singleton');
        $this->assertSame(
            $instance2,
            $instance3,
            'Should return same instance after clearing cache'
        );
    }

    /**
     * @testdox Resolves nested dependencies correctly
     */
    public function testNestedDependencies(): void
    {
        $this->container->bind(Config::class, Config::class);
        $this->container->bind(Database::class, Database::class);
        $this->container->bind(Cache::class, Cache::class);
        $this->container->bind(UserService::class, UserService::class);

        $service = $this->container->get(UserService::class);
        
        $this->assertInstanceOf(
            UserService::class,
            $service,
            'Should resolve all dependencies in chain'
        );
        $this->assertInstanceOf(
            Database::class,
            $service->getDatabase(),
            'Should resolve all dependencies in chain'
        );
        $this->assertInstanceOf(
            Cache::class,
            $service->getCache(),
            'Should resolve all dependencies in chain'
        );
        $this->assertInstanceOf(
            Config::class,
            $service->getDatabase()->getConfig(),
            'Should resolve all dependencies in chain'
        );
    }
}

// Test classes for dependency injection
class Database {
    private Config $config;
    
    public function __construct(Config $config) {
        $this->config = $config;
    }
    
    public function getConfig(): Config {
        return $this->config;
    }
}

class Logger {
    public function __construct() {}
}

class UserRepository {
    private Database $database;
    private Logger $logger;
    
    public function __construct(Database $database, Logger $logger) {
        $this->database = $database;
        $this->logger = $logger;
    }
    
    public function getDatabase(): Database {
        return $this->database;
    }
    
    public function getLogger(): Logger {
        return $this->logger;
    }
}

interface PaymentGatewayInterface {
    public function process(float $amount): bool;
}

class StripeGateway implements PaymentGatewayInterface {
    public function process(float $amount): bool {
        return true;
    }
}

class Config {
    public function __construct() {}
}

class Cache {
    public function __construct() {}
}

class UserService {
    private Database $database;
    private Cache $cache;
    
    public function __construct(Database $database, Cache $cache) {
        $this->database = $database;
        $this->cache = $cache;
    }
    
    public function getDatabase(): Database {
        return $this->database;
    }
    
    public function getCache(): Cache {
        return $this->cache;
    }
}
