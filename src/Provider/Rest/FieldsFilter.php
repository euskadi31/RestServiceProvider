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
use IteratorAggregate;
use ArrayIterator;

/**
 * FieldsFilter
 *
 * @author Axel Etcheverry <axel@etcheverry.biz>
 */
class FieldsFilter
{
    /**
     * @var FieldsBag
     */
    protected $fields;

    /**
     * @param FieldsBag $fields
     */
    public function __construct(FieldsBag $fields)
    {
        $this->fields = $fields;
    }

    /**
     * Process data object
     *
     * @param  ArrayObject    $data
     * @param  FieldsBag|null $fields
     * @return ArrayObject
     */
    private function processObject(ArrayObject $data, FieldsBag $fields = null)
    {
        $it = $data->getIterator();

        foreach ($it as $key => $value) {
            if (is_numeric($key)) {
                $it->offsetSet($key, $this->process($value, $fields));
            } else {
                if (!empty($fields) && !$fields->has($key) && $key != 'id') {
                    $it->offsetUnset($key);
                } else if ((is_array($value) || $value instanceof ArrayObject) && !empty($fields) && $fields->has($key)) {
                    $it->offsetSet($key, $this->process(
                        $value,
                        is_bool($fields[$key]) ? null : $fields[$key]
                    ));
                }
            }
        }

        return $data;
    }

    /**
     * Process data array
     *
     * @param  array          $data
     * @param  FieldsBag|null $fields
     * @return array
     */
    private function processArray(array $data, FieldsBag $fields = null)
    {
        foreach ($data as $key => $value) {
            if (is_numeric($key)) {
                $data[$key] = $this->process($value, $fields);
            } else {
                if (!empty($fields) && !$fields->has($key) && $key != 'id') {
                    unset($data[$key]);
                } else if ((is_array($value) || $value instanceof ArrayObject) && !empty($fields) && $fields->has($key)) {
                    $data[$key] = $this->process(
                        $value,
                        is_bool($fields[$key]) ? null : $fields[$key]
                    );
                }
            }
        }

        return $data;
    }

    /**
     * Process data
     *
     * @param  array|ArrayObject $data
     * @param  FieldsBag         $fields
     * @return array
     */
    private function process($data, FieldsBag $fields = null)
    {
        if ($data instanceof ArrayObject) {
            return $this->processObject($data, $fields);
        } else if (is_array($data)) {
            return $this->processArray($data, $fields);
        }

        return $data;
    }

    /**
     * Filter data
     *
     * @param  array|ArrayObject $data
     * @return array
     */
    public function filter($data)
    {
        return $this->process($data, $this->fields);
    }
}
