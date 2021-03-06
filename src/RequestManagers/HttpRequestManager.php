<?php

/**
 * This file is part of web3.php package.
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Web3\RequestManagers;

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use Web3\RequestManagers\RequestManager;
use Web3\RequestManagers\IRequestManager;

class HttpRequestManager extends RequestManager implements IRequestManager
{
    /**
     * client
     * 
     * @var \GuzzleHttp
     */
    protected $client;

    /**
     * construct
     * 
     * @param string $host
     * @return void
     */
    public function __construct($host)
    {
        parent::__construct($host);
        $this->client = new Client;
    }

    /**
     * sendPayload
     * 
     * @param string $payload
     * @param callable $callback
     * @return void
     */
    public function sendPayload($payload, $callback)
    {
        if (!is_string($payload)) {
            throw new \InvalidArgumentException('Payload must be string.');
        }
        // $promise = $this->client->postAsync($this->host, [
        //     'headers' => [
        //         'content-type' => 'application/json'
        //     ],
        //     'body' => $payload
        // ]);
        // $promise->then(
        //     function (ResponseInterface $res) use ($callback) {
        //         var_dump($res->body());
        //         call_user_func($callback, null, $res);
        //     },
        //     function (RequestException $err) use ($callback) {
        //         var_dump($err->getMessage());
        //         call_user_func($callback, $err, null);
        //     }
        // );
        try {
            $res = $this->client->post($this->host, [
                'headers' => [
                    'content-type' => 'application/json'
                ],
                'body' => $payload
            ]);
            $json = json_decode($res->getBody());

            if (JSON_ERROR_NONE !== json_last_error()) {
                return call_user_func($callback, new \InvalidArgumentException('json_decode error: ' . json_last_error_msg()), null);
            }

            call_user_func($callback, null, $json);
        } catch (RequestException $err) {
            call_user_func($callback, $err, null);
        }
    }
}