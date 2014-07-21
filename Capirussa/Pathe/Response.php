<?php
namespace Capirussa\Pathe;

/**
 * Response object represents a Pathe response
 *
 * @package Capirussa\Pathe
 */
class Response
{
    /**
     * HTTP status code returned by the remote server
     *
     * @var int
     */
    protected $statusCode;

    /**
     * List of HTTP headers returned by the remote server
     *
     * @var string[]
     */
    protected $rawHeaders;

    /**
     * Raw HTTP body returned by the remote server
     *
     * @var string
     */
    protected $rawBody;

    /**
     * Builds the Response object from a raw API response string
     *
     * @param string $apiResponse
     */
    public function __construct($apiResponse)
    {
        $this->statusCode = 0;
        $this->rawHeaders = array();
        $this->rawBody = '';

        // parse the API response into sections
        $responseSections = explode("\r\n\r\n", $apiResponse);

        // the first section contains the headers
        $headerSection = array_shift($responseSections);
        $this->rawBody = implode("\r\n\r\n", $responseSections);

        $headerLines = explode("\r\n", $headerSection);
        foreach ($headerLines as $responseLine) {
            if (empty($responseLine)) continue;

            if (strtoupper(substr($responseLine, 0, 5)) == 'HTTP/') {
                $this->statusCode = substr($responseLine, 9, 3);
            } else {
                $header = explode(':', $responseLine, 2);

                // sanity check
                if (!isset($header[1])) {
                    continue;
                }

                $this->rawHeaders[trim($header[0])] = trim($header[1]);
            }
        }
    }

    /**
     * Returns the HTTP status code
     *
     * @return int|null
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Returns the headers (if any)
     *
     * @return string[]|null
     */
    public function getRawHeaders()
    {
        return $this->rawHeaders;
    }

    /**
     * Returns the raw body (if any)
     *
     * @return string|null
     */
    public function getRawBody()
    {
        return $this->rawBody;
    }
}