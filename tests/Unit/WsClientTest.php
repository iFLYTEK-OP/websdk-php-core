<?php

namespace IFlytek\Xfyun\Core\Tests\Unit;

use IFlytek\Xfyun\Core\WsClient;
use IFlytek\Xfyun\Core\Handler\WsHandler;
use IFlytek\Xfyun\Core\Traits\SignTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class WsClientTest extends TestCase
{
    use SignTrait;

    public function testWsClientRequestSuccessfullySendAndReceive()
    {
        $credentials = array_merge(
            Yaml::parseFile(__DIR__ . '/./credentials.yml'),
            [
                'appId' => getenv('PHPSDK_CORE_APPID'),
                'apiKey' => getenv('PHPSDK_CORE_APIKEY'),
                'apiSecret' => getenv('PHPSDK_CORE_APISECRET')
            ]
        );

        $client = new WsClient(
            [
                'handler' => new WsHandler(
                    $this->signUriV1('wss://tts-api.xfyun.cn/v2/tts', $credentials),
                    $credentials['input']
                )
            ]
        );
        $result = $client->sendAndReceive();
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals('58173aacf1dabcb66fcd3fb7c54c0d68', md5($result->getBody()));
    }

    /**
     * @expectedException WebSocket\ConnectionException
     */
    public function testWsClientRequestException()
    {
        $client = new WsClient(
            [
                'handler' => new WsHandler(
                    'wss://tts-api.xfyun.cn/v2/tts',
                    'input'
                )
            ]
        );
        $result = $client->sendAndReceive();
    }
}
