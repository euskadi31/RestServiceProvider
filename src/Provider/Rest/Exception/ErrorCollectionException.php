<?php
/*
 * This file is part of the RestServiceProvider.
 *
 * (c) Axel Etcheverry <axel@etcheverry.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Euskadi31\Silex\Provider\Rest\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Iterator;
use ArrayAccess;
use Countable;

/**
 * ErrorCollectionException
 *
 * @author Axel Etcheverry <axel@etcheverry.biz>
 */
class ErrorCollectionException extends HttpException implements Iterator, ArrayAccess, Countable
{
    /**
     * @var array
     */
    private $container;

    /**
     *
     * @param array   $errors
     * @param integer $statusCode
     * @param array   $headers
     */
    public function __construct(array $errors, $statusCode = 400, array $headers = [])
    {
        parent::__construct($statusCode, null, null, $headers);

        $this->container = $errors;
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->container);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->container[] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * {@inheritDoc}
     */
    public function rewind()
    {
        return reset($this->container);
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return current($this->container);
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return key($this->container);
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        return next($this->container);
    }

    /**
     * {@inheritDoc}
     */
    public function valid()
    {
        return key($this->container) !== null;
    }
}
