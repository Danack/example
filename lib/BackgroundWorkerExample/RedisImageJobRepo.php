<?php

declare(strict_types = 1);

namespace BackgroundWorkerExample;

use Redis;

class RedisImageJobRepo implements ImageJobRepo
{
    /** @var Redis */
    private $redis;

    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    public function queueImageJob(ImageJob $imageJob)
    {
        $this->redis->rpush(
            ImageJobKey::getAbsoluteKeyName(),
            $imageJob->toString()
        );
        $this->setJobStatus($imageJob, 'queued');

        return true;
    }

    public function waitForImageJob(int $timeout = 5): ?ImageJob
    {
        $key = ImageJobKey::getAbsoluteKeyName();
        // A nil multi-bulk when no element could be popped and the timeout expired.
        // A two-element multi-bulk with the first element being the name of the key
        // where an element was popped and the second element being the value of
        // the popped element.
        $listElement = $this->redis->blpop([$key], $timeout);

        if (count($listElement) === 0) {
            return null;
        }

        $keyReturned = $listElement[0];
        $data = $listElement[1];

        $imageJob = ImageJob::fromString($data);

        return $imageJob;
    }


    public function setJobStatus(ImageJob $imageJob, string $status)
    {

        $key = ImageJobKey::getKeyNameForStatus($imageJob->getId());

//        \error_log("Key is " . $key);

        $this->redis->set($key, $status);
    }

    public function getJobStatus(string $jobId)
    {
        $key = ImageJobKey::getKeyNameForStatus($jobId);
        $result = $this->redis->get($key);
        if ($result === false) {
            return "Unknown";
        }

        return $result;
    }
}
