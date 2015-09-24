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

use Euskadi31\Silex\Provider\Rest\RestListener;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;

class RestListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testSubscribedEvents()
    {
        $this->assertEquals([
            KernelEvents::REQUEST   => [['onKernelRequest', Application::EARLY_EVENT]],
            KernelEvents::EXCEPTION => [['onKernelException', -8]]
        ], RestListener::getSubscribedEvents());
    }

    public function testKernelResponseWithoutMasterRequest()
    {
        $appMock = $this->getMock('Silex\Application');
        $getResponseEventMock = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
                     ->disableOriginalConstructor()
                     ->getMock();
        $getResponseEventMock->expects($this->once())
            ->method('isMasterRequest')
            ->will($this->returnValue(false));

        $listener = new RestListener($appMock);

        $listener->onKernelRequest($getResponseEventMock);
    }

    public function testKernelResponseWithMasterRequest()
    {
        $appMock = $this->getMock('Silex\Application');

        $request = Request::create('/users', 'GET', [], [], [], [], json_encode(['foo' => 'bar']));
        $request->setRequestFormat('json');
        $request->headers->set('Content-Type', 'application/json');
        $request->headers->set('Accept-Language', 'fr');

        $getResponseEventMock = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
                     ->disableOriginalConstructor()
                     ->getMock();
        $getResponseEventMock->expects($this->once())
            ->method('isMasterRequest')
            ->will($this->returnValue(true));
        $getResponseEventMock->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $listener = new RestListener($appMock);

        $listener->onKernelRequest($getResponseEventMock);

        $this->assertEquals('fr', $request->attributes->get('_locale'));
    }

    public function testKernelExceptionWithHeader()
    {
        $that = $this;

        $appMock = new Application;

        $exceptionMock = new \Symfony\Component\HttpKernel\Exception\HttpException(401, 'foo', null, [
            'WWW-Authenticate' => 'Bearer foo'
        ]);

        $getResponseForExceptionEventMock = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent')
                     ->disableOriginalConstructor()
                     ->getMock();
        $getResponseForExceptionEventMock->expects($this->once())
            ->method('getException')
            ->will($this->returnValue($exceptionMock));
        $getResponseForExceptionEventMock->expects($this->once())
            ->method('setResponse')
            ->will($this->returnCallback(function($response) use ($that) {
                $that->assertEquals('Bearer foo', $response->headers->get('WWW-Authenticate'));
            }));

        $listener = new RestListener($appMock);

        $listener->onKernelException($getResponseForExceptionEventMock);
    }
}
