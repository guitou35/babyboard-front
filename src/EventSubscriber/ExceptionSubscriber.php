<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class ExceptionSubscriber implements EventSubscriberInterface
{

    public function __construct(private UrlGeneratorInterface $router)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => [
                ['handleUnauthorizedAuthentication', 10]
            ]
        ];
    }

    /**
     * @param ExceptionEvent $event
     */
    function logoutUser(ExceptionEvent $event)
    {
        $redirectToLogoutResponse = new RedirectResponse($this->router->generate('app_logout'));
        $event->setResponse($redirectToLogoutResponse);
    }

    /**
     * If the event is a HttpException with a 401 statusCode or a runtimeError exception with a previous event with a 401 status code,
     * we logout the user.
     * @param ExceptionEvent $event
     */
    public function handleUnauthorizedAuthentication(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HttpException || $exception instanceof CustomUserMessageAuthenticationException) {
            switch ($exception->getCode()) {
                case 401:
                    $redirectRoute = $this->router->generate('app_logout');
                    break;
                case 403:
                case 404:
                    $redirectRoute = $this->router->generate('app_home');
                break;
                default:
                    break;
            }

            if (isset($redirectRoute)) {
                $event->setResponse(new RedirectResponse($redirectRoute));
            }
        }

    }
}