<?php

declare (strict_types = 1);

namespace Example;

use Params\Input;
use Psr\Http\Message\ServerRequestInterface;

class Psr7Input implements Input
{
    /** @var ServerRequestInterface */
    private $request;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    public function get()
    {
        $json = $this->request->getBody()->getContents();
        if (!empty($json)) {
            return json_decode_safe($json);
        }
        else {
            return $json;
        }
    }
}
