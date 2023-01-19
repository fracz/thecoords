<?php

declare(strict_types=1);

namespace App\Application\Settings;

use Monolog\Logger;

class AppSettings
{
    private array $settings;

    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return mixed
     */
    public function get(string $key = '')
    {
        return (empty($key)) ? $this->settings : $this->settings[$key];
    }

    public function isDevMode(): bool
    {
        return !!$this->settings['devMode'];
    }

    public function getUrl(): string
    {
        return $this->settings['url'];
    }

    public function getLogLevel(string $logFilename): int
    {
        $levels = $this->settings['logLevels'] ?? [];
        $level = $levels[$logFilename] ?? $levels['default'] ?? 'DEBUG';
        return Logger::getLevels()[$level] ?? Logger::DEBUG;
    }
}
