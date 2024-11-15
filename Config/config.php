<?php

declare(strict_types=1);

return [
    'name' => 'Mautic Sentry Error handler',
    'description' => 'Logs errors to Sentry.',
    'version' => '1.0',
    'author' => 'SWIS',

    'services' => [
        'other' => [
            'mautic_sentry.event_listener' => [
                'class' => MauticPlugin\MauticSentryBundle\EventListener\SentryExceptionListener::class,
                'arguments' => [],
                'tags' => [
                    'kernel.event_listener',
                    'kernel.event_listener',
                ],
                'tagArguments' => [
                    [
                        'event' => 'kernel.exception',
                        'method' => 'handleExceptionEvent',
                        /*
                         * 255 is the Mautic error-page priority, we want to log error's before it stops propagation.
                         * @see app/bundles/CoreBundle/Config/config.php
                         */
                        'priority' => 256,
                    ],
                    [
                        'event' => 'kernel.terminate',
                        'method' => 'handleKernelTerminateEvent',
                    ],
                ],
            ],
        ],
    ],
];
