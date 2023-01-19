<?php

namespace App\Infrastructure;

use App\Application\Settings\AppSettings;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class LoggerFactory
{
    private AppSettings $settings;

    public function __construct(AppSettings $settings)
    {
        $this->settings = $settings;
    }

    public function createLogger(string $filename = null): LoggerInterface
    {
        $path = sprintf('%s/%s', AppContainer::VAR_DIR . '/logs', $filename);
        $handler = new RotatingFileHandler($path, 0, $this->settings->getLogLevel($filename), true, 0777);
        $handler->setFormatter(new LineFormatter(null, null, false, true));
        return new Logger($filename, [$handler]);
    }
}
