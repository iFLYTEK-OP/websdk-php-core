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
     * @var string 请求uri
     */
    private $uri;

    /**
     * @var string 发送的字符串
     */
    private $input;

    /**
     * @var int 超时时间
     */
    private $timeout;

    public function __construct($uri, $input, $timeout = 3)
    {
        $this->uri = $uri;
        $this->input = $input;
        $this->timeout = $timeout;
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
            $client = new Client($this->uri);
            $client->setTimeout($this->timeout);
            $client->send($this->input);
            while (true) {
                $message = $this->jsonDecode($client->receive());
                if ($message->code !== 0) {
                    throw new \Exception('error receive');
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
}
