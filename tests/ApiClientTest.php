<?php

use Gdronov\ExampleCom\ApiClient;
use PHPUnit\Framework\TestCase;

class ApiClientTest extends TestCase
{
    public function testApiKey()
    {
        $apiKey = md5(rand());
        $apiClient = new ApiClient($apiKey);
        $this->assertSame($apiKey, $apiClient->getApiKey());
    }
}
