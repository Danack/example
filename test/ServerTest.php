<?php

namespace ExampleTest;

use ExampleTest\BaseTestCase;

/**
 * @group site_integration
 * @requires extension pcntl
 * @requires extension posix
 */
class ServerTest extends BaseTestCase
{
    /** @var  BuiltinServer */
    private static $server = null;

    private static $portToUse = 8080;
    
    public function setup()
    {
        parent::setup();
    }

    public static function setUpBeforeClass()
    {
        $path = realpath(__DIR__."/../../app/public");

        if ($path === false) {
            throw new \Exception("test site directory does not exist, can't serve from it.");
        }

        // PHPUnit calls this function even if the tests aren't
        // going to be run.
        if (extension_loaded('pcntl') === false) {
            throw new \Exception("pcntl extension is not loaded.");
        }
        
        if (extension_loaded('posix') === false) {
            throw new \Exception("posix extension is not loaded.");
        }

        self::$server = new BuiltinServer(self::$portToUse, $path);
        self::$server->startServer();
    }
    
    public static function tearDownAfterClass()
    {
        if (self::$server === null) {
            return;
        }

        self::$server->removeLockFile();
        self::$server->waitForChildToClose();
    }
    
    public function getURL($url)
    {
        $ch = curl_init();

        $fullUrl = "http://127.0.0.1:" . self::$portToUse . $url;
        curl_setopt($ch, CURLOPT_URL, $fullUrl);
        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $responseHeaders = [];

        $fnHeaderLine = function($curl, $header_line ) use (&$responseHeaders) {
            $responseHeaders[] = $header_line;
            return strlen($header_line);
        };

        curl_setopt($ch, CURLOPT_HEADERFUNCTION, $fnHeaderLine);

        $contents = curl_exec($ch);

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // close curl resource to free up system resources
        curl_close($ch);

        return [$statusCode, $contents, $responseHeaders];
    }
}
