<?php
require_once(dirname(__FILE__) . '/../../init.php');

use Capirussa\Pathe\Response;

/**
 * Tests Capirussa\Pathe\Response
 *
 */
class ResponseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testConstructWithoutParameters()
    {
        /** @noinspection PhpParamsInspection (this is intentional) */
        new Response();
    }

    public function testConstructWithEmptyResponse()
    {
        $response = new Response(null);

        $this->assertNotNull($response->getStatusCode());
        $this->assertEquals(0, $response->getStatusCode());

        $this->assertNotNull($response->getRawHeaders());
        $this->assertInternalType('array', $response->getRawHeaders());
        $this->assertCount(0, $response->getRawHeaders());

        $this->assertNotNull($response->getRawBody());
        $this->assertInternalType('string', $response->getRawBody());
        $this->assertEquals('', $response->getRawBody());
    }

    public function testConstructWithStatusCode()
    {
        $response = new Response(
            'HTTP/1.1 200 OK'
        );

        $this->assertNotNull($response->getStatusCode());
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertNotNull($response->getRawHeaders());
        $this->assertInternalType('array', $response->getRawHeaders());
        $this->assertCount(0, $response->getRawHeaders());

        $this->assertNotNull($response->getRawBody());
        $this->assertInternalType('string', $response->getRawBody());
        $this->assertEquals('', $response->getRawBody());
    }

    public function testConstructWithHeaders()
    {
        $response = new Response(
            'HTTP/1.1 200 OK' . "\r\n" .
            'Connection: Close' . "\r\n" .
            'Content-Type: text/html'
        );

        $this->assertNotNull($response->getStatusCode());
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertNotNull($response->getRawHeaders());
        $this->assertInternalType('array', $response->getRawHeaders());
        $this->assertCount(2, $response->getRawHeaders());
        $this->assertArrayHasKey('Connection', $response->getRawHeaders());
        $this->assertArrayHasKey('Content-Type', $response->getRawHeaders());
        $this->assertEquals('Close', $this->getObjectAttribute((object)$response->getRawHeaders(), 'Connection'));
        $this->assertEquals('text/html', $this->getObjectAttribute((object)$response->getRawHeaders(), 'Content-Type'));

        $this->assertNotNull($response->getRawBody());
        $this->assertInternalType('string', $response->getRawBody());
        $this->assertEquals('', $response->getRawBody());
    }

    public function testConstructWithInvalidHeaders()
    {
        $response = new Response(
            'HTTP/1.1 200 OK' . "\r\n" .
            'Connection: Close' . "\r\n" .
            "\0" . "\r\n" . // invalid because empty (true blank lines (\r\n\r\n) indicate boundary between headers and body)
            'Content-Type: text/html' . "\r\n" .
            'Content-Length 4096' // invalid because the semicolon is missing
        );

        $this->assertNotNull($response->getStatusCode());
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertNotNull($response->getRawHeaders());
        $this->assertInternalType('array', $response->getRawHeaders());
        $this->assertCount(2, $response->getRawHeaders());
        $this->assertArrayHasKey('Connection', $response->getRawHeaders());
        $this->assertArrayHasKey('Content-Type', $response->getRawHeaders());
        $this->assertEquals('Close', $this->getObjectAttribute((object)$response->getRawHeaders(), 'Connection'));
        $this->assertEquals('text/html', $this->getObjectAttribute((object)$response->getRawHeaders(), 'Content-Type'));

        $this->assertNotNull($response->getRawBody());
        $this->assertInternalType('string', $response->getRawBody());
        $this->assertEquals('', $response->getRawBody());
    }

    public function testConstructWithBody()
    {
        $response = new Response(
            'HTTP/1.1 200 OK' . "\r\n" .
            'Connection: Close' . "\r\n" .
            'Content-Type: text/html' . "\r\n" .
            "\r\n" .
            '<!DOCTYPE html><html><head>Test</head><body>Test Body</body></html>'
        );

        $this->assertNotNull($response->getStatusCode());
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertNotNull($response->getRawHeaders());
        $this->assertInternalType('array', $response->getRawHeaders());
        $this->assertCount(2, $response->getRawHeaders());
        $this->assertArrayHasKey('Connection', $response->getRawHeaders());
        $this->assertArrayHasKey('Content-Type', $response->getRawHeaders());
        $this->assertEquals('Close', $this->getObjectAttribute((object)$response->getRawHeaders(), 'Connection'));
        $this->assertEquals('text/html', $this->getObjectAttribute((object)$response->getRawHeaders(), 'Content-Type'));

        $this->assertNotNull($response->getRawBody());
        $this->assertInternalType('string', $response->getRawBody());
        $this->assertEquals('<!DOCTYPE html><html><head>Test</head><body>Test Body</body></html>', $response->getRawBody());
    }
}