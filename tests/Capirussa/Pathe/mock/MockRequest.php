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

            case 'pathe://mock/ticketweb.php?sign=30&UserCenterID=1':
                $simulatedResponse = $this->loadMockResponse('mock_30_not_logged_in.txt');
                break;

            case 'pathe://mock/ticketweb.php?sign=23':
                $postData = $this->getPostParameters();
                if ($postData[self::LOGIN_PASSWORD] == 'testIncorrectPassword') {
                    $simulatedResponse = $this->loadMockResponse('mock_23_incorrect.txt');
                } else {
                    $simulatedResponse = $this->loadMockResponse('mock_23_correct.txt');
                }
                break;

            case 'pathe://mock/ticketweb.php?sign=30':
                $simulatedResponse = $this->loadMockResponse('mock_30_logged_in.txt');
                break;

            case 'pathe://mock/ticketweb.php?sign=70&UserCenterID=1&template=0.cards.history.&CustomerID=12345678&LoadCards=0':
                $simulatedResponse = $this->loadMockResponse('mock_70.txt');
                break;

            case 'pathe://mock/CRM/soap.php?History=1&CustomerID=12345678&SelYear=0':
                $simulatedResponse = $this->loadMockResponse('mock_1070.txt');
                break;

            case 'pathe://mock/CRM/UD/Cache/tickets_dvarmb1ng45bh7mi9iovgsioh3.txt':
                $simulatedResponse = $this->loadMockResponse('mock_2070.txt');
                break;

            case 'pathe://mock/CRM/logout.php':
                $simulatedResponse = $this->loadMockResponse('mock_3023.txt');
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