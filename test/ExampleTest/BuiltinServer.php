<?php

declare(strict_types=1);

namespace ExampleTest;

class BuiltinServer
{
    /** @var int  */
    private $port;

    /** @var string  */
    private $directory;

    private $lockFile;
    
    private $childPID;

    /**
     * @param string $address The port that will be used to access the site.
     * @param string $directory The directory that will be used to serve the files.
     */
    public function __construct(int $port, string $directory)
    {
        $this->port = $port;
        $this->directory = $directory;
        $this->lockFile = $this->getLockFile();
    }

    public function __destruct()
    {
        $this->removeLockFile();
        $this->waitForChildToClose();
    }

    public static function createAndStart(int $port, string $directory )
    {
        $server = new BuiltinServer($port, $directory);
        $server->startServer();

        return $server;
    }





    private function getLockFile()
    {
        // TODO - use dynamic file name if you want to have multiple servers running.
        return sys_get_temp_dir().'/lockFile_builtinserver.pid';
    }

    public function exec_timeout($cmd, $timeout, &$output = '')
    {
        $fdSpec = [
            0 => ['file', '/dev/null', 'r'], //nothing to send to child process
            1 => ['pipe', 'w'], //child process's stdout
            2 => ['pipe', 'w', 'a'], //don't care about child process stderr
        ];

        $pipes = [];
        $proc = proc_open($cmd, $fdSpec, $pipes);
        
        stream_set_blocking($pipes[1], false);
        
        $stop = time() + $timeout;
        while (true === true) {
            $in = [$pipes[1], $pipes[2]];
            $out = [];
            $err = [];
            //stream_select($in, $out, $err, min(1, $stop - time()));
            stream_select($in, $out, $err, 0, 200);
            

            if (count($in) !== 0) {
                foreach ($in as $socketToRead) {
                    while (feof($socketToRead) !== false) {
                        $output .= stream_get_contents($socketToRead);
                        continue;
                    }
                }
            }

            if ($stop <= time()) {
                break;
            }
            else if ($this->isLockFileStillValid() === false) {
                break;
            }
        }
        

        fclose($pipes[1]); //close process's stdout, since we're done with it
        fclose($pipes[2]); //close process's stderr, since we're done with it
        
        $status = proc_get_status($proc);
        if (intval($status['running']) !== 0) {
            proc_terminate($proc); //terminate, since close will block until the process exits itself
            //This is the child process - so just exit
            exit(0);
        }
        else {
            proc_close($proc);
            //This is the child process - so just exit
            exit(0);
            return $status['exitcode'];
        }
    }

    private function isLockFileStillValid()
    {
        if (file_exists($this->lockFile) === false) {
            return false;
        }

        return true;
    }
    
    public function startServer()
    {
        $pid = pcntl_fork();
        $this->childPID = $pid;

        if ($pid < 0) {
            echo 'Unable to start the server process.';
            return 1;
        }

        $command = sprintf(
            "php -S 0.0.0.0:%s -t %s",
            $this->port,
            $this->directory
        );



        if ($pid > 0) {
            // PHP server takes a moment to get into a state to be able to accept
            // requests
            // TODO - make test requests rather than just sleeping.
            sleep(1);
            return 0;
        }

        if (posix_setsid() < 0) {
            echo 'Unable to set the child process as session leader';
            return -1;
        }

        touch($this->lockFile);
        $this->exec_timeout($command, 15, $output);

        return 1;
    }

    public function removeLockFile()
    {
        @unlink($this->lockFile);
    }
    
    public function waitForChildToClose()
    {
        $status = null;
        if (defined('WNOHANG') === false) {
            define('WNOHANG', 1);
        }
        
        $options = WNOHANG;
        
        for ($i=0; $i<10; $i++) {
            $info = pcntl_waitpid($this->childPID, $status, $options);
            //var_dump($info);
            if ($info === 0) {
                //echo "Child has exited\n";
                return;
            }
            if ($info === -1) {
                //echo "Child has already exited?\n";
                return;
            }
        }

        echo "Child maybe failed to exit. You might have a zombie server.";
    }



    public function getURL($url)
    {
        $ch = curl_init();

        $fullUrl = "http://127.0.0.1:" . $this->port . $url;
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
