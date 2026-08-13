<?php
/**
 * JUANDirectory Framework - Dependency Injection Container
 *
 * @package JUANDirectory\Core
 * @author John Virdi V. Alfonso
 * @license MIT
 * @version 2.0.0
 */

namespace JUANDirectory\Core;

use Closure;
use ArrayAccess;
use Exception;

/**
 * Service Container Class
 *
 * Implements a service locator/dependency injection container.
 *
 * @category Framework
 * @package JUANDirectory\Core
 * @implements ArrayAccess
 */
class Container implements ArrayAccess
{
    /**
     * Registered services and factories
     *
     * @var array<string, Closure|object>
     */
    protected array $bindings = [];

    /**
     * Resolved instances cache
     *
     * @var array<string, object>
     */
    protected array $instances = [];

    /**
     * Service aliases
     *
     * @var array<string, string>
     */
    protected array $aliases = [];

    /**
     * Register a binding in the container
     *
     * @param string $abstract Service name
     * @param Closure|string|object|null $concrete Concrete implementation
     * @param bool $singleton Whether to treat as singleton
     * @return self For method chaining
     */
    public function bind(string $abstract, Closure|string|object|null $concrete = null, bool $singleton = false): self
    {
        if ($concrete instanceof Closure) {
            $this->bindings[$abstract] = ['concrete' => $concrete, 'singleton' => $singleton];
        } elseif (is_object($concrete)) {
            $this->instances[$abstract] = $concrete;
        } elseif (is_string($concrete)) {
            $this->bindings[$abstract] = ['concrete' => $concrete, 'singleton' => $singleton];
        } elseif ($concrete === null) {
            // Assume abstract class name as concrete
            $this->bindings[$abstract] = ['concrete' => $abstract, 'singleton' => $singleton];
        }

        return $this;
    }

    /**
     * Register a singleton in the container
     *
     * @param string $abstract Service name
     * @param Closure|string|object|null $concrete Concrete implementation
     * @return self For method chaining
     */
    public function singleton(string $abstract, Closure|string|object|null $concrete = null): self
    {
        return $this->bind($abstract, $concrete, true);
    }

    /**
     * Register an instance in the container
     *
     * @param string $abstract Service name
     * @param object $instance Instance
     * @return self For method chaining
     */
    public function instance(string $abstract, object $instance): self
    {
        $this->instances[$abstract] = $instance;
        return $this;
    }

    /**
     * Resolve a service from the container
     *
     * @param string $abstract Service name
     * @return mixed Resolved service
     * @throws Exception If service not found
     */
    public function make(string $abstract): mixed
    {
        // Check if already resolved as instance
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        // Check if has binding
        if (!isset($this->bindings[$abstract])) {
            throw new Exception("Service '{$abstract}' not found in container");
        }

        $binding = $this->bindings[$abstract];
        $concrete = $binding['concrete'];
        $singleton = $binding['singleton'] ?? false;

        // Resolve concrete
        if ($concrete instanceof Closure) {
            $instance = $concrete($this);
        } elseif (is_string($concrete)) {
            $instance = class_exists($concrete) ? new $concrete() : $concrete;
        } else {
            $instance = $concrete;
        }

        // Cache if singleton
        if ($singleton) {
            $this->instances[$abstract] = $instance;
        }

        return $instance;
    }

    /**
     * Register an alias for a service
     *
     * @param string $abstract Service name
     * @param string $alias Alias name
     * @return self For method chaining
     */
    public function alias(string $abstract, string $alias): self
    {
        $this->aliases[$alias] = $abstract;
        return $this;
    }

    /**
     * Check if service is bound
     *
     * @param string $abstract Service name
     * @return bool True if bound
     */
    public function has(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
    }

    /**
     * ArrayAccess: Check if offset exists
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }

    /**
     * ArrayAccess: Get offset
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->make($offset);
    }

    /**
     * ArrayAccess: Set offset
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->bind($offset, $value);
    }

    /**
     * ArrayAccess: Unset offset
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->bindings[$offset], $this->instances[$offset]);
    }
}
