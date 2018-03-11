<?php

/** @var \Zend\Expressive\Application $app */

/**
 * Check token
 */
$app->get(
    '/refresh[/]',
    Liberatus\Action\RefreshAction::class,
    'check'
);
