<?php

declare(strict_types = 1);

namespace BackgroundWorkerExample;

interface ImageJobRepo
{
    public function queueImageJob(ImageJob $imageJob);

    /**
     * @param int $timeout How many seconds to wait for.
     * @return ImageJob|null
     */
    public function waitForImageJob(int $timeout = 5): ?ImageJob;

    public function setJobStatus(ImageJob $imageJob, string $status);

    public function getJobStatus(string $jobId);
}
