<?php

declare(strict_types=1);

use App\Application\Settings\AppSettings;
use Slim\App;
use Tuupola\Middleware\JwtAuthentication;

return function (App $app) {
    /** @var AppSettings $settings */
    $settings = $app->getContainer()->get(AppSettings::class);
    $jwtSecret = $settings->get('jwtSecret');
    $app->add(new JwtAuthentication([
        'path' => '/api',
        'secret' => $jwtSecret,
        'secure' => !$settings->isDevMode(),
        'ignore' => [
            '/api/config',
            '/api/tokens',
        ],
    ]));
};
