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
            throw new Exception('Bad Exception', 10400);
        });

        $this->assertInstanceOf('Euskadi31\Silex\Provider\Rest\RestListener', $app['rest.listener']);

        $response = $app->handle(Request::create('/me'));

        $this->assertEquals(200, $response->getStatusCode());

        $response = $app->handle(Request::create('/me1'));

        $this->assertEquals(404, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);

        $this->assertEquals('No route found for "GET /me1"', $json['errors'][0]['message']);
        $this->assertEquals('NotFoundHttpException', $json['errors'][0]['type']);
        $this->assertEquals(0, $json['errors'][0]['code']);
        $this->assertTrue(isset($json['errors'][0]['exception']));

        $response = $app->handle(Request::create('/error'));

        $this->assertEquals(500, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);

        $this->assertEquals('Bad Exception', $json['errors'][0]['message']);
        $this->assertEquals('Exception', $json['errors'][0]['type']);
        $this->assertEquals(10400, $json['errors'][0]['code']);
        $this->assertTrue(isset($json['errors'][0]['exception']));
    }

    public function testFieldsParameter()
    {
        $that = $this;

        $app = new Application(['debug' => true]);

        $app->register(new RestServiceProvider);

        $app->get('/fields', function() use ($app, $that) {
            $fields = $app['rest.fields'];

            $that->assertInstanceOf('Euskadi31\Silex\Provider\Rest\FieldsBag', $fields);

            $that->assertTrue($fields->has('name'));
            $that->assertTrue($fields->has('email'));
            $that->assertFalse($fields->has('phone'));
        });

        $app->handle(Request::create('/fields?fields=name,email'));
    }

    public function testNotFieldsParameter()
    {
        $app = new Application(['debug' => true]);

        $app->register(new RestServiceProvider);

        $app->get('/fields-empty', function() use ($app) {
            $fields = $app['rest.fields'];

            $response = '';
            $response .= var_export($fields instanceof \Euskadi31\Silex\Provider\Rest\FieldsBag, true);
            $response .= PHP_EOL;
            $response .= var_export($fields->has('name'), true);
            $response .= PHP_EOL;
            $response .= var_export($fields->has('email'), true);
            $response .= PHP_EOL;
            $response .= var_export($fields->has('phone'), true);

            return $response;
        });

        $response = $app->handle(Request::create('/fields-empty'));

        $this->assertEquals(
            'true' . PHP_EOL . 'false' . PHP_EOL . 'false' . PHP_EOL . 'false',
            $response->getContent()
        );
    }

    public function testEmptyFieldsParameter()
    {
        $app = new Application(['debug' => true]);

        $app->register(new RestServiceProvider);

        $app->get('/fields-empty', function() use ($app) {
            $fields = $app['rest.fields'];

            $response = '';
            $response .= var_export($fields instanceof \Euskadi31\Silex\Provider\Rest\FieldsBag, true);
            $response .= PHP_EOL;
            $response .= var_export($fields->has('name'), true);
            $response .= PHP_EOL;
            $response .= var_export($fields->has('email'), true);
            $response .= PHP_EOL;
            $response .= var_export($fields->has('phone'), true);

            return $response;
        });

        $response = $app->handle(Request::create('/fields-empty?fields='));

        $this->assertEquals(
            'true' . PHP_EOL . 'false' . PHP_EOL . 'false' . PHP_EOL . 'false',
            $response->getContent()
        );
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
            'errors' => [
                [
                    'message'   => 'No route found for "GET /me1"',
                    'type'      => 'NotFoundHttpException',
                    'code'      => 0
                ]
            ]
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), $response->getContent());
    }
}
