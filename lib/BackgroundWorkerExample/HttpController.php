<?php

declare(strict_types = 1);

namespace BackgroundWorkerExample;

use BackgroundWorkerExample\ImageJobRepo;
use Example\Response\ImageResponse;
use PHPUnit\Util\Json;
use SlimAuryn\Response\FileResponse;
use SlimAuryn\Response\JsonResponse;
use SlimAuryn\Response\TwigResponse;
use VarMap\VarMap;

class HttpController
{
    /**
     * Get the HTML page
     */
    public function getFormPage()
    {
        return new TwigResponse('image_queue.html');
    }

    /**
     * Serve up an image if it's ready
     */
    public function getImage(string $imageFilename)
    {
        $filename = substr($imageFilename, 0, strlen($imageFilename) - 4);
        $filename = preg_replace("#[^a-z0-9A-Z]#iu", '', $filename);
        $path = __DIR__ . "/../../var/image_cache/" . $filename . ".gif";

        if (file_exists($path) !== true) {
            return new JsonResponse(
                ['error'=> 'not found'],
                [],
                404
            );
        }
        header('Content-type: image/gif');
        readfile($path);
        exit(0);
//        return new ImageResponse(
//            $path,
//            $imageFilename
//        );
    }

    /**
     * Get the status of an Image Job
     */
    public function getImageStatus(VarMap $varMap, ImageJobRepo $imageJobRepo)
    {
        if ($varMap->has('job_id') !== true) {
            return new JsonResponse([
               'error' => "job_id not set"
            ]);
        }


        $job_id = $varMap->get('job_id');
        $status = $imageJobRepo->getJobStatus($job_id);

        return new JsonResponse([
            'job_status' => $status,
        ]);
    }


    /**
     * Start the process of generating an image, or return immediately if
     * it already exists.
     */
    public function postImageRequest(VarMap $varMap, ImageJobRepo $imageJobRepo)
    {
        $text = $varMap->get('text');
        $job = ImageJob::createFromText($text);
        $filename = $job->getResultFilename();

        $path = __DIR__ . "/../../var/image_cache/" . $filename;

        if (file_exists($path) === true) {
            return new JsonResponse([
                'job_status' => 'done',
                'filename' => $filename
            ]);
        }

        $imageJobRepo->queueImageJob($job);

        return new JsonResponse([
            'job_status' => 'queued',
            'id' => $job->getId(),
            'filename' => $filename
        ]);
    }
}
