<?php
namespace DMore\ChromeDriver;

class StreamReadException extends \Exception
{
    /** @var bool */
    private $eof;
    /** @var bool */
    private $timed_out;
    /** @var bool */
    private $blocked;

    public function __construct($eof, $timed_out, $blocked)
    {
        $this->eof = $eof;
        $this->timed_out = $timed_out;
        $this->blocked = $blocked;
    }

    /**
     * @return boolean
     */
    public function isEof()
    {
        return $this->eof;
    }

    /**
     * @return boolean
     */
    public function isTimedOut()
    {
        return $this->timed_out;
    }

    /**
     * @return boolean
     */
    public function isBlocked()
    {
        return $this->blocked;
    }
}
