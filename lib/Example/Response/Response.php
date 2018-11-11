<?php


namespace Example\Response;

interface Response
{
    public function getStatus();
    public function getBody();
    public function getHeaders();
}
