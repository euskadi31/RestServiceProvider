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

use Euskadi31\Silex\Provider\RestServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use Exception;

class RestProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
        $app = new Application(['debug' => true]);

        $app->register(new RestServiceProvider);

        $app->get('/me', function() {
            return 'Hi!';
        });

        $app->get('/error', function() {
            throw new Exception('Bad Exception');
        });

        $this->assertInstanceOf('Euskadi31\Silex\Provider\Rest\RestListener', $app['rest.listener']);

        $response = $app->handle(Request::create('/me'));

        $this->assertEquals(200, $response->getStatusCode());

        $response = $app->handle(Request::create('/me1'));

        $this->assertEquals(404, $response->getStatusCode());

        $this->assertEquals(json_encode([
            'error' => [
                'message'   => 'No route found for "GET /me1"',
                'type'      => 'NotFoundHttpException',
                'code'      => 404,
                'file'      => realpath(__DIR__ . '/../../') . '/vendor/symfony/http-kernel/EventListener/RouterListener.php',
                'line'      => 159
            ]
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), $response->getContent());


        $response = $app->handle(Request::create('/error'));

        $this->assertEquals(500, $response->getStatusCode());

        $this->assertEquals(json_encode([
            'error' => [
                'message'   => 'Bad Exception',
                'type'      => 'Exception',
                'code'      => 500,
                'file'      => __FILE__,
                'line'      => 31
            ]
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), $response->getContent());
    }

    public function testRequest()
    {
        $app = new Application(['debug' => true]);

        $app->register(new RestServiceProvider);

        $app->post('/users', function(Request $request){
            if ($request->request->has('foo')) {
                return $request->request->get('foo');
            }

            return 'error';
        });

        $request = Request::create('/users', 'POST', [], [], [], [], json_encode(['foo' => 'bar']));
        $request->setRequestFormat('json');
        $request->headers->set('Content-Type', 'application/json');

        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('bar', $response->getContent());
    }

    public function testWithoutMasterRequest()
    {
        $app = new Application(['debug' => true]);

        $app->register(new RestServiceProvider);

        $app->post('/users', function(Request $request){
            if ($request->request->has('foo')) {
                return $request->request->get('foo');
            }

            return 'error';
        });

        $request = Request::create('/users', 'POST', [], [], [], [], json_encode(['foo' => 'bar']));
        $request->setRequestFormat('json');
        $request->headers->set('Content-Type', 'application/json');

        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('bar', $response->getContent());
    }

    public function testErrorResponseWithoutDebug()
    {
        $app = new Application(['debug' => false]);

        $app->register(new RestServiceProvider);

        $response = $app->handle(Request::create('/me1'));

        $this->assertEquals(404, $response->getStatusCode());

        $this->assertEquals(json_encode([
            'error' => [
                'message'   => 'No route found for "GET /me1"',
                'type'      => 'NotFoundHttpException',
                'code'      => 404
            ]
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), $response->getContent());
    }
}
