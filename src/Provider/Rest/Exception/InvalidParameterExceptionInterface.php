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

/**
 * InvalidParameterExceptionInterface
 *
 * @author Axel Etcheverry <axel@etcheverry.biz>
 */
interface InvalidParameterExceptionInterface
{
    /**
     * Get parameter name
     *
     * @return string
     */
    public function getParameter();
}
