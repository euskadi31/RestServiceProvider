<?php
/*
 * This file is part of the RestServiceProvider.
 *
 * (c) Axel Etcheverry <axel@etcheverry.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Euskadi31\Silex\Application;

use Symfony\Component\HttpFoundation\JsonResponse;
use ArrayObject;

/**
 * Rest trait.
 *
 * @author Axel Etcheverry <axel@etcheverry.biz>
 */
trait RestTrait
{
    /**
     * Convert some data into a JSON response.
     *
     * @param mixed   $data    The response data
     * @param integer $status  The response status code
     * @param array   $headers An array of response headers
     * @return JsonResponse
     * @see JsonResponse
     */
    public function json($data = [], $status = 200, array $headers = [])
    {
        $request = $this['request_stack']->getMasterRequest();

        if ($request->query->has('fields')) {
            $fields = $this['rest.fields'];
            $fields->addParameter('id');

            foreach ($data as $key => $value) {
                if (is_numeric($key)) {
                    if ($value instanceof ArrayObject) {
                        $value = $value->getArrayCopy();
                        $data[$key] = $value;
                    }

                    foreach ($value as $key2 => $value2) {
                        if (!$fields->has($key2)) {
                            unset($data[$key][$key2]);
                        }
                    }
                } else {
                    if (!$fields->has($key)) {
                        unset($data[$key]);
                    }
                }
            }
        }

        $flags = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;

        if ($request->query->get('pretty', true)) {
            $flags |= JSON_PRETTY_PRINT;
        }

        $response = new JsonResponse;
        $response->headers->replace($headers);
        $response->setStatusCode($status);
        $response->setEncodingOptions($flags);
        $response->setData($data);

        if ($request->query->has('callback')) {
            $response->setCallback($request->query->get('callback'));
        }

        return $response;
    }
}
