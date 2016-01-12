<?php

namespace Aixia\PostitBoardFront;

use GuzzleHttp\Client;


class RestClient
{
    const METHOD_GET    = 'GET';
    const METHOD_POST   = 'POST';
    const METHOD_PATCH  = 'PATCH';
    const METHOD_DELETE = 'DELETE';

    private $client;

    private $config;

    /**
     * RestClient constructor.
     */
    public function __construct()
    {
        $this->client = new Client();
        $this->config = require_once(APP_PATH . '/config/config.php');
    }

    private function getAuthConfig()
    {
        return [
            'auth' => [
                $this->config['login'],
                $this->config['password']
            ]
        ];
    }

    public function get($service, $id = null)
    {
        $res = [];

        $uri = $this->config['base_url'] . $service;
        if ($id != null) {
            $uri .= '/' . $id;
        }

        try {
            $res = $this->client->request(self::METHOD_GET, $uri, $this->getAuthConfig());
            $res = json_decode($res->getBody()->getContents(), JSON_OBJECT_AS_ARRAY);

        } catch (\Exception $ex) {
            var_export($ex->getMessage());
            throw $ex;
        }

        return $res;
    }

    public function post($service, $data = [])
    {
        $request = new \GuzzleHttp\Psr7\Request(self::METHOD_POST, $this->config['base_url'] . $service,
            [
                'Content-Type' => 'application/json;charset=UTF-8'
            ],
            json_encode($data)
        );
        $this->client->send($request, $this->getAuthConfig());
    }

    public function delete($service, $id)
    {
        if (empty($id)) {
            return false;
        }

        try {
            return $this->client->request(self::METHOD_DELETE, $this->config['base_url'] . $service . '/' . $id, $this->getAuthConfig());
        } catch (\Exception $ex) {
            var_export($ex->getMessage());
            throw $ex;
        }
    }

    public function patch($service, $id = null, $data = [])
    {
        try {
            $request = new \GuzzleHttp\Psr7\Request(self::METHOD_PATCH, $this->config['base_url'] . $service . '/' . $id,
                [
                    'Content-Type' => 'application/json;charset=UTF-8'
                ],
                json_encode($data)
            );

            $this->client->send($request, $this->getAuthConfig());
        } catch (\Exception $ex) {
            var_export($ex->getMessage());
            throw $ex;
        }
    }
}



