<?php

declare(strict_types=1);

namespace Example\CliController;

use Example\Response\HtmlResponse;
use Twilio\Rest\Client as TwilioClient;
use Mandrill as MandrillClient;
use Example\MandrillConfig;
use Example\TwilioConfig;

class Debug
{
    public function hello()
    {
        return new HtmlResponse("Hello");
    }
}
