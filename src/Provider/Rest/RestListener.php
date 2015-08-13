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

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Debug\Exception\FlattenException;
use Silex\Application;

/**
 * Initializes the Rest.
 *
 * @author Axel Etcheverry <axel@etcheverry.biz>
 */
class RestListener implements EventSubscriberInterface
{
    protected $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     *
     * @param  GetResponseEvent $event
     * @return void
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : []);
        }

        $request->attributes->set('_locale', $request->getPreferredLanguage());
    }

    /**
     *
     * @param  GetResponseForExceptionEvent $event
     * @return void
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $headers = [];

        $exception = $event->getException();

        $e = FlattenException::create($exception);

        if ($exception instanceof HttpExceptionInterface) {
            $headers = $exception->getHeaders();
            $code = $exception->getStatusCode();
        } else {
            $code = $exception->getCode();
        }

        if ($code < 100 || $code >= 600) {
            $code = 500;
        }

        $error = [
            'error' => [
                'message'   => $exception->getMessage(),
                'type'      => join('', array_slice(explode('\\', get_class($exception)), -1)),
                'code'      => $code
            ]
        ];

        if ($this->app['debug']) {
            $error['error']['exception'] = $e->toArray();
        }

        $event->setResponse($this->app->json($error, $code, $headers));
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST   => [['onKernelRequest', Application::EARLY_EVENT]],
            KernelEvents::EXCEPTION => [['onKernelException', Application::EARLY_EVENT]]
        ];
    }
}
