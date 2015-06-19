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

use Euskadi31\Silex\Application\RestTrait;
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use ArrayObject;

class RestTraitTest extends \PHPUnit_Framework_TestCase
{
    protected function getApplication()
    {
        $app = new ApplicationTest();

        $users = [
            [
                'id'            => 1234,
                'first_name'    => 'Axel',
                'last_name'     => 'Etcheverry',
                'email'         => 'axel.etcheverry@kokoroe.fr'
            ],
            [
                'id'            => 1235,
                'first_name'    => 'Rui',
                'last_name'     => 'Avelino',
                'email'         => 'rui.avelino@kokoroe.fr'
            ],
            new ArrayObject([
                'id'            => 1236,
                'first_name'    => 'William',
                'last_name'     => 'Rudent',
                'email'         => 'william.rudent@kokoroe.fr'
            ])
        ];

        $app->get('/me', function() use ($app, $users) {
            return $app->json($users[0]);
        });

        $app->get('/users', function() use ($app, $users) {
            return $app->json($users);
        });

        return $app;
    }


    public function testFieldsParameter()
    {
        $app = $this->getApplication();

        $response = $app->handle(Request::create('/me?fields=email'));

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals(json_encode([
            'id'    => 1234,
            'email' => 'axel.etcheverry@kokoroe.fr'
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PRETTY_PRINT), $response->getContent());

        $response = $app->handle(Request::create('/users?fields=email'));

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals(json_encode([
            [
                'id'    => 1234,
                'email' => 'axel.etcheverry@kokoroe.fr'
            ],
            [
                'id'    => 1235,
                'email' => 'rui.avelino@kokoroe.fr'
            ],
            [
                'id'    => 1236,
                'email' => 'william.rudent@kokoroe.fr'
            ]
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PRETTY_PRINT), $response->getContent());
    }

    public function testPrettyPrintParameter()
    {
        $app = $this->getApplication();

        $response = $app->handle(Request::create('/me?fields=email&pretty=0'));

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals(json_encode([
            'id'    => 1234,
            'email' => 'axel.etcheverry@kokoroe.fr'
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), $response->getContent());
    }

    public function testCallbackParameter()
    {
        $app = $this->getApplication();

        $response = $app->handle(Request::create('/me?fields=email&callback=Acme.process'));

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals(sprintf('/**/Acme.process(%s);', json_encode([
            'id'    => 1234,
            'email' => 'axel.etcheverry@kokoroe.fr'
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PRETTY_PRINT)), $response->getContent());
    }
}

class ApplicationTest extends Application
{
    use RestTrait;
}
