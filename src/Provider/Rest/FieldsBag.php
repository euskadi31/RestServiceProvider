<?php
/*
 * This file is part of the RestServiceProvider.
 *
 * (c) Axel Etcheverry <axel@etcheverry.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Euskadi31\Silex\Provider\Rest;

use ArrayObject;

/**
 * FieldsBag
 *
 * @author Axel Etcheverry <axel@etcheverry.biz>
 */
class FieldsBag extends ArrayObject
{
    /**
     * Default parameters.
     *
     * @var array
     */
    protected $defaults;

    /**
     * @var array
     */
    protected $keys = [];

    /**
     *
     * @param array $parameters
     */
    public function __construct(array $parameters = [])
    {
        $this->defaults = [];

        parent::__construct($parameters);
    }

    /**
     * Process fields keys
     *
     * @param  array       $data
     * @param  string|null $parent
     * @return void
     */
    protected function process(array $data, $parent = null)
    {
        foreach ($data as $key => $value) {
            if ($value instanceof FieldsBag) {
                $this->process($value->getArrayCopy(), $key . '.');
            }

            $this->keys[$parent . $key] = true;
        }
    }

    /**
     * Set default fields
     *
     * @param array $parameters
     * @return FieldsBag
     */
    public function setDefaults(array $parameters)
    {
        $this->defaults = array_flip($parameters);

        return $this;
    }

    /**
     * Add parameter
     *
     * @param string $parameter
     * @return FieldsBag
     */
    public function addParameter($parameter)
    {
        $this[$parameter] = true;

        return $this;
    }

    /**
     * Check field
     *
     * @param string $parameter
     * @return boolean
     */
    public function has($parameter)
    {
        if ($this->count() == 0) {
            return isset($this->defaults[$parameter]);
        }

        if (empty($this->keys)) {
            $this->process($this->getArrayCopy());
        }

        return isset($this->keys[$parameter]);
    }
}
