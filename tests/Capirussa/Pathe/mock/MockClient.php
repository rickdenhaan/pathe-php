<?php
require_once(dirname(__FILE__) . '/../../../init.php');

use Capirussa\Pathe;

class MockClient extends Pathe\Client
{
    /**
     * Returns a new mock Request object
     *
     * @param int    $sign          (Optional) Defaults to null
     * @param string $requestMethod (Optional) Defaults to Request::METHOD_GET
     * @return MockRequest
     */
    protected function getRequest($sign = null, $requestMethod = Pathe\Request::METHOD_GET)
    {
        return new MockRequest($sign, $requestMethod);
    }
}