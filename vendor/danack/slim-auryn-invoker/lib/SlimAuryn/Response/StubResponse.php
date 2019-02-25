<?php

namespace SlimAuryn\Response;

interface StubResponse
{
    public function getStatus() : int;
    public function getBody() : string;
    public function getHeaders(): array;
}
