<?php

declare(strict_types=1);

use App\Application\Actions\Config\GetFrontendConfigAction;
use App\Application\Actions\User\GetCurrentUserAction;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->group('/api', function (Group $group) {
        $group->get('/config', GetFrontendConfigAction::class);
        $group->group('/users', function (Group $group) {
            $group->get('/current', GetCurrentUserAction::class);
        });
    });
};
