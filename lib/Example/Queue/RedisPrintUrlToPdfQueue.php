<?php

declare(strict_types=1);

namespace Example\Queue;

use Example\Queue\PrintUrlToPdfJobJobKey;

class RedisPrintUrlToPdfQueue implements PrintUrlToPdfQueue
{
    /** @var \Redis */
    private $redis;

    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    public function pushPrintUrlToPdfJob(PrintUrlToPdfJob $invoicePdfJob)
    {
        $json = json_encode_safe($invoicePdfJob->toArray());
        $keyname = PrintUrlToPdfJobJobKey::getKeyPrefix();
        $this->redis->rPush($keyname, $json);
    }

    public function getPrintUrlToPdfJob(int $timeout): ?PrintUrlToPdfJob
    {
        $keyname = PrintUrlToPdfJobJobKey::getKeyPrefix();

        // A nil multi-bulk when no element could be popped and the timeout expired.
        // A two-element multi-bulk with the first element being the name of the key
        // where an element was popped and the second element being the value of
        // the popped element.
        $redisData = $this->redis->blpop([$keyname], $timeout);

        if (count($redisData) === 0) {
            return null;
        }

        [$keyname, $data] = $redisData;

        return PrintUrlToPdfJob::fromData($data);
    }

    public function clearQueue()
    {
        $keyname = PrintUrlToPdfJobJobKey::getKeyPrefix();
        $this->redis->del($keyname);
    }
}
