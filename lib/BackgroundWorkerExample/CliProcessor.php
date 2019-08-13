<?php

declare(strict_types = 1);

namespace BackgroundWorkerExample;

class CliProcessor
{
    /** @var \BackgroundWorkerExample\ImageJobRepo */
    private $imageJobRepo;

    /**
     *
     * @param ImageJobRepo $imageJobRepo
     */
    public function __construct(ImageJobRepo $imageJobRepo)
    {
        $this->imageJobRepo = $imageJobRepo;
    }

    public function processImageQueue()
    {
        continuallyExecuteCallable(
            [$this, 'runInternally'],
            0,
            0,
            600
        );
    }

    public function runInternally()
    {
        $imageJob = $this->imageJobRepo->waitForImageJob(5);

        if ($imageJob === null) {
            return;
        }

        $this->psychedelicFontGif($imageJob);
    }


    function psychedelicFontGif(ImageJob $imageJob)
    {
        $name = $imageJob->getText();

        set_time_limit(3000);

        $aniGif = new \Imagick();
        $aniGif->setFormat("gif");



        $maxFrames = 20;
        $scale = 2/3;
        $fontScale = 0.5;
        echo "Starting: " . $imageJob->getResultFilename() . "\n";

        $this->imageJobRepo->setJobStatus($imageJob, 'Processing 0/' . $maxFrames);


        $name = str_replace('_', "\n", $name);

        for ($frame = 0; $frame < $maxFrames; $frame++) {
            echo "Frame $frame \n";
            $draw = new \ImagickDraw();
            $draw->setStrokeOpacity(1);
            $draw->translate(0, -40);
            $draw->setFont(__DIR__ . "/CANDY.TTF");
            $draw->setfontsize(150 * $scale * $fontScale);

            for ($strokeWidth = 25; $strokeWidth > 0; $strokeWidth--) {
                $hue = intval(fmod(($frame * 360 / $maxFrames) + 170 + $strokeWidth * 360 / 25, 360));
                $color = "hsl($hue, 255, 128)";
                $draw->setStrokeColor($color);
                $draw->setFillColor($color);
                $draw->setStrokeWidth($strokeWidth * 3 * $scale);
                $draw->annotation(60 * $scale, 165 * $scale, $name);
            }

            $draw->setStrokeColor('none');
            $draw->setFillColor('black');
            $draw->setStrokeWidth(0);
            $draw->annotation(60 * $scale, 165 * $scale, $name);

            //Create an image object which the draw commands can be rendered into
            $imagick = new \Imagick();
            $imagick->newImage(
                intval(650 * $scale),
                intval(230 * $scale),
                new \ImagickPixel('transparent')
            );
            $imagick->setImageFormat("png");

            //Render the draw commands in the ImagickDraw object
            //into the image.
            $imagick->drawImage($draw);

            $imagick->setImageDelay(5);
            $aniGif->addImage($imagick);

            $imagick->destroy();

            $this->imageJobRepo->setJobStatus($imageJob, "Processing $frame/$maxFrames");
        }

        // loop forever
        $aniGif->setImageIterations(0);
        // Optimise gif
        $aniGif->deconstructImages();

        echo "Writing file: " . $imageJob->getResultFilename();
        $this->imageJobRepo->setJobStatus($imageJob, 'Writing file');

        $path = __DIR__ . "/../../var/image_cache/" .$imageJob->getResultFilename();

        @mkdir(
            dirname($path),
            0755,
            true
        );

        $aniGif->writeImages(
            $path,
            true
        );

        $this->imageJobRepo->setJobStatus($imageJob, 'complete');
    }
}
