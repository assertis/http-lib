<?php
declare(strict_types=1);

namespace Assertis\Http\Response;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use RuntimeException;
use SplObjectStorage;

/**
 * Represents the result of a batch operation. This result container is
 * iterable, countable, and you can can get a result by value using the
 * getResult function.
 *
 * Successful results are anything other than exceptions. Failure results are
 * exceptions.
 *
 * Class stolen from GuzzleHttp v5.2 (it's no more in 6+)
 */
class BatchResults implements Countable, IteratorAggregate, ArrayAccess
{
    private $hash;

    /**
     * BatchResults constructor.
     * @param array $objects
     */
    public function __construct(array $objects)
    {
        $hash = new SplObjectStorage();
        foreach ($objects as $obj){
            $hash->attach($obj);
        }
        $this->hash = $hash;
    }

    /**
     * Get the keys that are available on the batch result.
     *
     * @return array
     */
    public function getKeys()
    {
        return iterator_to_array($this->hash);
    }

    /**
     * Gets a result from the container for the given object. When getting
     * results for a batch of requests, provide the request object.
     *
     * @param object $forObject Object to retrieve the result for.
     *
     * @return mixed|null
     */
    public function getResult($forObject)
    {
        return isset($this->hash[$forObject]) ? $this->hash[$forObject] : null;
    }

    /**
     * Get an array of successful results.
     *
     * @return array
     */
    public function getSuccessful()
    {
        $results = [];
        foreach ($this->hash as $key) {
            if (!($this->hash[$key] instanceof \Exception)) {
                $results[] = $this->hash[$key];
            }
        }

        return $results;
    }

    /**
     * Get an array of failed results.
     *
     * @return array
     */
    public function getFailures()
    {
        $results = [];
        foreach ($this->hash as $key) {
            if ($this->hash[$key] instanceof \Exception) {
                $results[] = $this->hash[$key];
            }
        }

        return $results;
    }

    /**
     * Allows iteration over all batch result values.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        $results = [];
        foreach ($this->hash as $key=>$obj) {
            $results[] = $obj;
        }

        return new ArrayIterator($results);
    }

    /**
     * Counts the number of elements in the batch result.
     *
     * @return int
     */
    public function count()
    {
        return count($this->hash);
    }

    /**
     * Checks if the batch contains a specific numerical array index.
     *
     * @param int $key Index to access
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        return $key < count($this->hash);
    }

    /**
     * Allows access of the batch using a numerical array index.
     *
     * @param int $key Index to access.
     *
     * @return mixed|null
     */
    public function offsetGet($key)
    {
        $i = -1;
        foreach ($this->hash as $obj) {
            if ($key === ++$i) {
                return $this->hash[$obj];
            }
        }

        return null;
    }

    public function offsetUnset($key)
    {
        throw new RuntimeException('Not implemented');
    }

    public function offsetSet($key, $value)
    {
        throw new RuntimeException('Not implemented');
    }
}
