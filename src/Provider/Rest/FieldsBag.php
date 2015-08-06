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

/**
 * FieldsBag
 *
 * @author Axel Etcheverry <axel@etcheverry.biz>
 */
class FieldsBag
{
    /**
     * Parameter storage.
     *
     * @var array
     */
    protected $parameters;

    /**
     * @var boolean
     */
    protected $parametersIsEmpty;

    /**
     * Default parameters.
     *
     * @var array
     */
    protected $defaults;

    /**
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->defaults             = [];
        $this->parameters           = array_flip($parameters);
        $this->parametersIsEmpty    = (count($this->parameters) == 0);
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
        $this->parameters[$parameter] = true;

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
        if ($this->parametersIsEmpty) {
            return isset($this->defaults[$parameter]);
        }

        return isset($this->parameters[$parameter]);
    }
}
