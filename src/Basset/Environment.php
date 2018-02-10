<?php namespace Basset;

use Closure;
use ArrayAccess;
use InvalidArgumentException;
use Basset\Factory\FactoryManager;

class Environment implements ArrayAccess {

    /**
     * Asset collections.
     *
     * @var array
     */
    protected $collections = array();

    /**
     * Basset factory manager instance.
     *
     * @var \Basset\Factory\FactoryManager
     */
    protected $factory;

    /**
     * Asset finder instance.
     *
     * @var \Basset\AssetFinder
     */
    protected $finder;

    /**
     * Create a new environment instance.
     */
    public function __construct(FactoryManager $factory, AssetFinder $finder) {
        $this->factory = $factory;
        $this->finder = $finder;
    }

    /**
     * Alias of \Basset\Environment::collection()
     */
    public function make(string $name, ?Closure $callback = null) : Collection {
        return $this->collection($name, $callback);
    }

    /**
     * Create or return an existing collection.
     */
    public function collection(string $identifier, ?Closure $callback = null) : Collection {
        if ( ! isset($this->collections[$identifier])) {
            $directory = $this->prepareDefaultDirectory();

            $this->collections[$identifier] = new Collection($directory, $identifier);
        }

        // If the collection has been given a callable closure then we'll execute the closure with
        // the collection instance being the only parameter given. This allows users to begin
        // using the collection instance to add assets.
        if (is_callable($callback)) {
            call_user_func($callback, $this->collections[$identifier]);
        }

        return $this->collections[$identifier];
    }

    /**
     * Prepare the default directory for a new collection.
     */
    protected function prepareDefaultDirectory() : Directory {
        $path = $this->finder->setWorkingDirectory('/');

        return new Directory($this->factory, $this->finder, $path);
    }

    /**
     * Get all collections.
     */
    public function all() : array {
        return $this->collections;
    }

    /**
     * Determine if a collection exists.
     */
    public function has(string $name) : bool {
        return isset($this->collections[$name]);
    }

    /**
     * Register a package with the environment.
     */
    public function package(string $package, ?string $namespace = null) : void {
        if (is_null($namespace)) {
            list($vendor, $namespace) = explode('/', $package);
        }

        $this->finder->addNamespace($namespace, $package);
    }

    /**
     * Register an array of collections.
     */
    public function collections(array $collections) : void {
        foreach ($collections as $name => $callback) {
            $this->make($name, $callback);
        }
    }

    /**
     * Set a collection offset.
     *
     * @param  string  $offset
     * @param  mixed  $value
     */
    public function offsetSet($offset, $value) : void {
        if (is_null($offset)) {
            throw new InvalidArgumentException('Collection identifier not given.');
        }

        $this->collection($offset, $value);
    }

    /**
     * Get a collection offset.
     *
     * @param  string  $offset
     */
    public function offsetGet($offset) : ?Collection {
        return $this->has($offset) ? $this->collection($offset) : null;
    }

    /**
     * Unset a collection offset.
     *
     * @param  string  $offset
     */
    public function offsetUnset($offset) : void {
        unset($this->collections[$offset]);
    }

    /**
     * Determine if a collection offset exists.
     *
     * @param  string  $offset
     */
    public function offsetExists($offset) : bool {
        return $this->has($offset);
    }

}