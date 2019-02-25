<?php

declare(strict_types=1);

use SlimAurynTest\BaseTestCase;
use SlimAuryn\ResponseMapper\TwigResponseMapper;
use SlimAuryn\Response\TwigResponse;

class TwigResponseMapperTest extends BaseTestCase
{
    /**
     * @covers \SlimAuryn\ResponseMapper\TwigResponseMapper
     */
    public function testMapStubResponseToPsr7()
    {
        // The templates are included in order of priority.
        $templatePaths = [
            __DIR__ . '/../../templates'
        ];

        $loader = new Twig_Loader_Filesystem($templatePaths);
        $twig = new Twig_Environment($loader, array(
            'cache' => false,
            'strict_variables' => true,
            'debug' => true
        ));

        $twigResponseMapper = new TwigResponseMapper($twig);

        $twigResponse = new TwigResponse(
            'test.html',
            $params = ['foo' => 'bar'],
            $status = 201,
            ['x-foo' => 'bar']
        );

        $originalResponse = new \Slim\Http\Response();
        $response = $twigResponseMapper($twigResponse, $originalResponse);
        $this->assertSame($status, $response->getStatusCode());

        $response->getBody()->rewind();
        $bodyString = $response->getBody()->getContents();

        $this->assertContains('foo was set to bar', $bodyString);

        $this->assertTrue($response->hasHeader('x-foo'));
        $this->assertSame('bar', $response->getHeaderLine('x-foo'));
    }
}