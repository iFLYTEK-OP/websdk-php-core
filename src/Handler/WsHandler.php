<?php

/**
 * Copyright 1999-2021 iFLYTEK Corporation

 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace IFlytek\Xfyun\Core\Handler;

use IFlytek\Xfyun\Core\Traits\JsonTrait;
use WebSocket\Client;
use WebSocket\Exception;
use GuzzleHttp\Psr7\Response;

/**
 * WebSocket处理类
 *
 * @author guizheng@iflytek.com
 */
class WsHandler
{
    use JsonTrait;

    /**
     * @var WebSocket\Client ws client
     */
    private $client;

    /**
     * @var string 发送的字符串
     */
    private $input;

    public function __construct($uri, $input, $timeout = 3)
    {
        $this->client = new Client($uri);
        $this->client->setTimeout($timeout);
        $this->input = $input;
    }

    /**
     * 发送并等待获取返回
     *
     * 这是一个同步阻塞的过程，调用将持续到结果返回或者出现超时
     */
    public function sendAndReceive()
    {
        $result = '';
        try {
            $this->client->send($this->input);
            while (true) {
                $message = $this->jsonDecode($this->client->receive());
                if ($message->code !== 0) {
                    throw new \Exception(json_encode($message));
                }
                switch ($message->data->status) {
                    case 1:
                        $result .= base64_decode($message->data->audio);
                        break;
                    case 2:
                        $result .= base64_decode($message->data->audio);
                        break 2;
                }
            }
            return new Response(200, [], $result);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function send($message = null)
    {
        try {
            if (empty($message)) {
                if (!empty($this->input)) {
                    $message = $this->input;
                } else {
                    throw new Exception();
                }
            }
            return $this->client->send($message);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function receive()
    {
        try {
            return $this->client->receive();
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
