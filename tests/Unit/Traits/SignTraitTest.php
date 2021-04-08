<?php

namespace IFlytek\Xfyun\Core\Tests\Unit\Traits;

use IFlytek\Xfyun\Core\Traits\SignTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class SignTraitTest extends TestCase
{
    use SignTrait;

    public function testSuccessfullySignUri()
    {
        $result = 'wss://tts-api.xfyun.cn/v2/tts?host=tts-api.xfyun.cn&date=Mon%2C+05+Apr+2021+07%3A09%3A53+GMT&authorization=YXBpX2tleT0iOWEzMzk0ODc2M2FjZmNkZTUzMTEwZDUyODNlOTliMDIiLGFsZ29yaXRobT0iaG1hYy1zaGEyNTYiLGhlYWRlcnM9Imhvc3QgZGF0ZSByZXF1ZXN0LWxpbmUiLHNpZ25hdHVyZT0iWnZ2STI0ZStEallQbTZHajRhejE5QTJiTGU3Q0xUSngwdnFZQmwwam9lcz0i';
        $this->assertEquals(
            $result,
            $this->signUriV1(
                'wss://tts-api.xfyun.cn/v2/tts',
                array_merge(
                    Yaml::parseFile(__DIR__ . '/../credentials.yml'),
                    [
                        'appId' => getenv('PHPSDK_CORE_APPID'),
                        'apiKey' => getenv('PHPSDK_CORE_APIKEY'),
                        'apiSecret' => getenv('PHPSDK_CORE_APISECRET')
                    ]
                )
            )
        );
    }

    public function testSuccessfullySignV1()
    {
        $this->assertEquals(
            'IrrzsJeOFk1NGfJHW6SkHUoN9CU=',
            $this->signV1('595f23df', 'd9f4aa7ea6d94faca62cd88a28fd5234', '1512041814')
        );
    }
}
