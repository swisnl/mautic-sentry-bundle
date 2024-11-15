<?php

declare(strict_types=1);

namespace MauticPlugin\MauticSentryBundle\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

final class SentryExceptionListener
{
    public function __construct()
    {
        if (!getenv('SENTRY_DSN')) {
            return;
        }

        \Sentry\init([
            'dsn' => getenv('SENTRY_DSN'),
            'environment' => getenv('SENTRY_ENVIRONMENT') ?: getenv('APP_ENV') ?: 'unknown',
        ]);
    }

    public function handleExceptionEvent(ExceptionEvent $event): void
    {
        \Sentry\captureException($event->getThrowable());
        $event->getRequest()->attributes->set(SentryExceptionListener::class, true);
    }

    public function handleKernelTerminateEvent(TerminateEvent $event): void
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        // If already logged the exception, we don't log the Termination event.
        if ($request->attributes->get(SentryExceptionListener::class, false)) {
            return;
        }

        if ($response->getStatusCode() < 200 || $response->getStatusCode() > 299) {
            \Sentry\captureException(new \RuntimeException(sprintf('Terminated route @ "%s"', $request->getRequestUri())));
        }
    }
}
