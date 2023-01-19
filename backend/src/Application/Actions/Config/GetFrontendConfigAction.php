<?php

namespace App\Application\Actions\Config;

use App\Application\Actions\Action;
use App\Application\Settings\AppSettings;
use Psr\Http\Message\ResponseInterface as Response;

class GetFrontendConfigAction extends Action
{
    private AppSettings $settings;

    public function __construct(AppSettings $settings)
    {
        $this->settings = $settings;
    }

    protected function action(): Response
    {
        return $this->respondWithData([
            'url' => $this->settings->get('url'),
            'maxUploadSize' => [
                'file' => $this->getMaxUploadSizePerFile(),
                'total' => $this->getMaxUploadSize(),
            ],
        ]);
    }

    private function getMaxUploadSizePerFile(): int
    {
        $perFileLimit = 1024 * (int)ini_get('upload_max_filesize') * (substr(ini_get('upload_max_filesize'), -1) == 'M' ? 1024 : 1);
        return min($perFileLimit, $this->getMaxUploadSize());
    }

    private function getMaxUploadSize(): int
    {
        return 1024 * (int)ini_get('post_max_size') * (substr(ini_get('post_max_size'), -1) == 'M' ? 1024 : 1);
    }
}
