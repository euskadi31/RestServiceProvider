<?php
/*
 * This file is part of the RestServiceProvider.
 *
 * (c) Axel Etcheverry <axel@etcheverry.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Euskadi31\Silex\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\EventListenerProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Rest integration for Silex.
 *
 * @author Axel Etcheverry <axel@etcheverry.biz>
 */
class RestServiceProvider implements ServiceProviderInterface, EventListenerProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Container $app)
    {
        $app['rest.listener'] = function($app) {
            return new Rest\RestListener($app);
        };

        $app['rest.fields'] = function($app) {
            $request = $app['request_stack']->getCurrentRequest();

            $fields = $request->query->get('fields', '');
            if (!empty($fields)) {
                $parser = $app['rest.fields.parser'];

                return $parser->parse($fields);
            }

            return new Rest\FieldsBag();
        };

        $app['rest.fields.parser'] = function($app) {
            return new Rest\FieldsParser;
        };

        $app['rest.filter'] = function($app) {
            return new Rest\FieldsFilter($app['rest.fields']);
        };
    }

    /**
     * {@inheritDoc}
     */
    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber($app['rest.listener']);
    }
}
