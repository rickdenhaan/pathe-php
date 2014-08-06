<?php
require_once(dirname(__FILE__) . '/../../../init.php');

use Capirussa\Pathe;

class MockClient extends Pathe\Client
{
    /**
     * List of modifications to apply to the mock request after creating it
     *
     * @var array
     */
    protected $requestModifications = array();

    /**
     * Adds a request modification to perform after creating the request for the given action
     *
     * @param string $action
     * @param string $method
     * @param array  $arguments (Optional)
     * @return void
     */
    public function prepareRequest($action, $method, $arguments = array())
    {
        $this->requestModifications[] = array($action, $method, $arguments);
    }

    /**
     * Returns a new mock Request object
     *
     * @param int    $sign          (Optional) Defaults to null
     * @param string $requestMethod (Optional) Defaults to Request::METHOD_GET
     * @return MockRequest
     */
    protected function getRequest($sign = null, $requestMethod = Pathe\Request::METHOD_GET)
    {
        $retValue = new MockRequest($sign, $requestMethod);

        if (count($this->requestModifications) > 0) {
            foreach ($this->requestModifications as $modification) {
                $action    = $modification[0];
                $method    = $modification[1];
                $arguments = $modification[2];

                $stackTrace = debug_backtrace();
                $callee = $stackTrace[1];

                if ($callee['function'] == $action) {
                    call_user_func_array(array($retValue, $method), $arguments);
                }
            }
        }

        return $retValue;
    }
}