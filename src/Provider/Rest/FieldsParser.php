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

use RuntimeException;

/**
 * FieldsParser
 *
 * @author Axel Etcheverry <axel@etcheverry.biz>
 */
class FieldsParser
{
    /**
     * Parse fields string
     *
     * @param  string $fields
     * @return array
     */
    public function parse($fields)
    {
        $data   = new FieldsBag;
        $key    = '';
        $value  = '';
        $chars  = str_split($fields);

        for ($i = 0; $i < count($chars); $i++) {
            $char = $chars[$i];

            if ($char == '{') {
                if (!empty($key)) {
                    throw new RuntimeException(sprintf(
                        'Syntax error for the right syntax to use near \'%s\' at col %d',
                        substr($fields, ($i - 2), 5),
                        $i+1
                    ));
                }

                $key = $value;
                $value = '';
                $data[$key] = new FieldsBag;
                continue;
            }

            if ($char == '}') {
                $data[$key][$value] = true;
                $value = '';
                $key = '';
                continue;
            }

            if ($char == ',') {
                if (empty($value)) {
                    continue;
                }

                if (!empty($key)) {
                    $data[$key][$value] = true;
                } else {
                    $data[$value] = true;
                }

                $value = '';
                continue;
            }

            if ($char == ' ') {
                continue;
            }

            $value .= $char;
        }

        if (!empty($value)) {
            $data[$value] = true;
        }

        return $data;
    }
}
