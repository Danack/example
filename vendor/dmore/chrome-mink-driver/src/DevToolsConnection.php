<?php
namespace DMore\ChromeDriver;

use Behat\Mink\Exception\DriverException;
use WebSocket\Client;
use WebSocket\ConnectionException;

abstract class DevToolsConnection
{
    /** @var Client */
    private $client;
    /** @var int */
    private $command_id = 1;
    /** @var string */
    private $url;
    /** @var int|null */
    private $socket_timeout;

    public function __construct($url, $socket_timeout = null)
    {
        $this->url = $url;
        $this->socket_timeout = $socket_timeout;
    }

    public function canDevToolsConnectionBeEstablished()
    {
        $url = 'http://127.0.0.1:9222/json/version';
        $c = curl_init($url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
        $s = curl_exec($c);
        curl_close($c);

        return $s !== false && strpos($s, 'Chrome') !== false;
    }

    protected function getUrl()
    {
        return $this->url;
    }
    
    public function connect($url = null)
    {
        $url = $url == null ? $this->url : $url;
        $options = ['fragment_size' => 2000000]; # Chrome closes the connection if a message is sent in fragments
        if (is_numeric($this->socket_timeout) && $this->socket_timeout > 0) {
            $options['timeout'] = (int) $this->socket_timeout;
        }
        $this->client = new Client($url, $options);
    }

    public function close()
    {
        $this->client->close();
    }

    /**
     * @param string $command
     * @param array $parameters
     * @return null|string
     * @throws \Exception
     */
    public function send($command, array $parameters = [])
    {
        $payload['id'] = $this->command_id++;
        $payload['method'] = $command;
        if (!empty($parameters)) {
            $payload['params'] = $parameters;
        }

        $this->client->send(json_encode($payload));

        $data = $this->waitFor(function ($data) use ($payload) {
            return array_key_exists('id', $data) && $data['id'] == $payload['id'];
        });

        if (isset($data['result'])) {
            return $data['result'];
        }

        return ['result' => ['type' => 'undefined']];
    }

    protected function waitFor(callable $is_ready)
    {
        $data = [];
        while (true) {
            try {
                $response = $this->client->receive();
            } catch (ConnectionException $exception) {
                $message = $exception->getMessage();
                $state = json_decode(substr($message, strpos($message, '{')), true);
                throw new StreamReadException($state['eof'], $state['timed_out'], $state['blocked']);
            }
            if (is_null($response)) {
                return null;
            }
            $data = json_decode($response, true);

            if (array_key_exists('error', $data)) {
                $message = $data['error']['data'] ? $data['error']['message'] . '. ' . $data['error']['data'] : $data['error']['message'];
                throw new DriverException($message , $data['error']['code']);
            }

            if ($this->processResponse($data)) {
                break;
            }

            if ($is_ready($data)) {
                break;
            }
        }

        return $data;
    }

    /**
     * @param array $data
     * @return bool
     */
    abstract protected function processResponse(array $data);
}
