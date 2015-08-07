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
     * Process data
     *
     * @param  array|ArrayObject $data
     * @param  FieldsBag         $fields
     * @return array
     */
    private function process($data, FieldsBag $fields = null)
    {
        foreach ($data as $key => $value) {
            if ($value instanceof ArrayObject) {
                $value = $value->getArrayCopy();
            }

            if (is_numeric($key)) {
                $data[$key] = $this->process($value, $fields);
            } else {
                if (!empty($fields) && !$fields->has($key) && $key != 'id') {
                    unset($data[$key]);
                } else if (is_array($value) && !empty($fields) && $fields->has($key)) {
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
