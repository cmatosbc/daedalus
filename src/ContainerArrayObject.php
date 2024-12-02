<?php

namespace Daedalus;

use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * A PSR-11 compliant container implementation using ArrayObject
 * with singleton management capabilities
 */
class ContainerArrayObject extends EnhancedArrayObject implements ContainerInterface
{
    /** @var array<string, bool> Tracks which entries are singletons */
    private array $singletons = [];

    /** @var array<string, object> Cached singleton instances */
    private array $instances = [];

    /**
     * Constructor
     *
     * @param array $definitions Initial container definitions
     */
    public function __construct(array $definitions = [])
    {
        parent::__construct($definitions);
    }

    /**
     * Finds an entry of the container by its identifier and returns it
     *
     * @param string $id Identifier of the entry to look for
     * @return mixed Entry
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier
     * @throws ContainerExceptionInterface Error while retrieving the entry
     */
    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw new class($id) extends \Exception implements NotFoundExceptionInterface {
                public function __construct(string $id) {
                    parent::__construct("No entry was found for identifier: $id");
                }
            };
        }

        try {
            $definition = $this->offsetGet($id);
            
            // For singletons, check if we have a cached instance
            if ($this->isSingleton($id)) {
                if (!isset($this->instances[$id])) {
                    $this->instances[$id] = $this->resolve($definition);
                }
                return $this->instances[$id];
            }
            
            // For non-singletons, always resolve fresh
            return $this->resolve($definition);
        } catch (\Throwable $e) {
            throw new class($e->getMessage(), 0, $e) extends \Exception implements ContainerExceptionInterface {};
        }
    }

    /**
     * Returns true if the container can return an entry for the given identifier
     *
     * @param string $id Identifier of the entry to look for
     * @return bool
     */
    public function has(string $id): bool
    {
        return $this->offsetExists($id);
    }

    /**
     * Register a class or value as a singleton
     *
     * @param string $id Identifier for the singleton
     * @param string|object|callable $concrete The class name, instance or factory function
     * @return self
     */
    public function singleton(string $id, string|object|callable $concrete): self
    {
        $this->singletons[$id] = true;
        $this->bind($id, $concrete);
        return $this;
    }

    /**
     * Bind a class or value to the container
     *
     * @param string $id Identifier for the binding
     * @param mixed $concrete The class name, instance or factory function
     * @return self
     */
    public function bind(string $id, mixed $concrete): self
    {
        $this->offsetSet($id, $concrete);
        if (isset($this->instances[$id])) {
            unset($this->instances[$id]);
        }
        return $this;
    }

    /**
     * Check if an identifier is registered as a singleton
     *
     * @param string $id Identifier to check
     * @return bool
     */
    public function isSingleton(string $id): bool
    {
        return isset($this->singletons[$id]);
    }

    /**
     * Clear all cached singleton instances
     *
     * @return void
     */
    public function clearInstances(): void
    {
        $this->instances = [];
        // Don't clear singleton registrations, just their instances
    }

    /**
     * Resolve the concrete implementation
     *
     * @param mixed $concrete The concrete implementation to resolve
     * @return mixed
     * @throws \Exception If the concrete implementation cannot be resolved
     */
    private function resolve(mixed $concrete): mixed
    {
        if (is_callable($concrete)) {
            return $concrete($this);
        }

        if (is_object($concrete)) {
            return clone $concrete;
        }

        if (is_string($concrete) && class_exists($concrete)) {
            return $this->buildClass($concrete);
        }

        return $concrete;
    }

    /**
     * Build a class instance using reflection
     *
     * @param string $class The class name to instantiate
     * @return object
     * @throws \ReflectionException
     */
    private function buildClass(string $class): object
    {
        $reflector = new \ReflectionClass($class);
        
        if (!$reflector->isInstantiable()) {
            throw new \Exception("Class $class is not instantiable");
        }

        $constructor = $reflector->getConstructor();
        
        if (is_null($constructor)) {
            return new $class();
        }

        $parameters = $constructor->getParameters();
        $dependencies = $this->resolveDependencies($parameters);

        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * Resolve constructor dependencies
     *
     * @param \ReflectionParameter[] $parameters
     * @return array
     */
    private function resolveDependencies(array $parameters): array
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();
            
            if (!$type instanceof \ReflectionNamedType || $type->isBuiltin()) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                    continue;
                }
                throw new \Exception("Cannot resolve parameter: " . $parameter->getName());
            }

            $dependencies[] = $this->get($type->getName());
        }

        return $dependencies;
    }
}
