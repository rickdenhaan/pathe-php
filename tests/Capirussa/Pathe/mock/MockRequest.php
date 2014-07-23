<?php
require_once(dirname(__FILE__) . '/../../../init.php');

use Capirussa\Pathe;

class MockRequest extends Pathe\Request
{
    /**
     * Base URL for all calls
     *
     * @type string
     */
    protected $baseUrl = 'pathe://mock/';

    /**
     * Overrides the real request method to simulate a predefined response
     *
     * @return Pathe\Response
     */
    protected function doRequest()
    {
        // build the request URL
        $requestUrl = $this->buildRequestUrl();

        // read file contents
        switch ($requestUrl) {
            default:
                $simulatedResponse = $this->loadMockResponse('mock_generic_error.txt');
                break;
        }

        // the response should contain \r\n line endings, but Git sometimes screws that up
        if (!strpos($simulatedResponse, "\r\n")) {
            $simulatedResponse = str_replace(array("\r", "\n"), "\r\n", $simulatedResponse);
        }

        $this->response = new Pathe\Response($simulatedResponse);

        return $this->response;
    }

    private function loadMockResponse($filename)
    {
        return file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . $filename);
    }
}