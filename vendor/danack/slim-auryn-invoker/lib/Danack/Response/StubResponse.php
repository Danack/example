<?php

namespace Danack\Response;

interface StubResponse
{
    public function getStatus();
    public function getBody();
    public function getHeaders();
}
