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
        $that = $this;

        $app = new Application(['debug' => true]);

        $app->register(new RestServiceProvider);

        $app->get('/me', function() {
            return 'Hi!';
        });

        $app->get('/fields', function(Request $request) use ($app, $that) {
            $fields = $app['rest.fields'];

            $that->assertInstanceOf('Euskadi31\Silex\Provider\Rest\FieldsBag', $fields);

            $that->assertTrue($fields->has('name'));
            $that->assertTrue($fields->has('email'));
            $that->assertFalse($fields->has('phone'));
        });

        $app->get('/fields-empty', function(Request $request) use ($app, $that) {
            $fields = $app['rest.fields'];

            $that->assertInstanceOf('Euskadi31\Silex\Provider\Rest\FieldsBag', $fields);

            $that->assertFalse($fields->has('name'));
            $that->assertFalse($fields->has('email'));
            $that->assertFalse($fields->has('phone'));
        });

        $app->get('/error', function() {
            throw new Exception('Bad Exception');
        });

        $this->assertInstanceOf('Euskadi31\Silex\Provider\Rest\RestListener', $app['rest.listener']);

        $response = $app->handle(Request::create('/me'));

        $this->assertEquals(200, $response->getStatusCode());

        $response = $app->handle(Request::create('/me1'));

        $this->assertEquals(404, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);

        $this->assertEquals('No route found for "GET /me1"', $json['error']['message']);
        $this->assertEquals('NotFoundHttpException', $json['error']['type']);
        $this->assertEquals(404, $json['error']['code']);
        $this->assertTrue(isset($json['error']['exception']));

        $response = $app->handle(Request::create('/error'));

        $this->assertEquals(500, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);

        $this->assertEquals('Bad Exception', $json['error']['message']);
        $this->assertEquals('Exception', $json['error']['type']);
        $this->assertEquals(500, $json['error']['code']);
        $this->assertTrue(isset($json['error']['exception']));

        $response = $app->handle(Request::create('/fields?fields=name,email'));

        $response = $app->handle(Request::create('/fields-empty'));
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
