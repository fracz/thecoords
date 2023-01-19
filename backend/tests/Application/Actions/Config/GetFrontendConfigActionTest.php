<?php

declare(strict_types=1);

namespace Tests\Application\Actions\Config;

use Tests\TestCase;

/** @small */
class GetFrontendConfigActionTest extends TestCase
{
    public function testAction()
    {
        $app = $this->getAppInstance();
        $request = $this->createRequest(null, 'GET', '/api/config');
        $response = $app->handle($request);
        $payload = json_decode((string)$response->getBody(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('url', $payload);
    }
}
