<?php
require_once(dirname(__FILE__) . '/../../init.php');

use Capirussa\Pathe\Request;

/**
 * Tests Capirussa\Pathe\Request
 *
 */
class RequestTest extends PHPUnit_Framework_TestCase
{
    public function testConstructWithoutParameters()
    {
        $request = new Request();

        $this->assertNull($request->getSign());
        $this->assertEquals(Request::METHOD_GET, $request->getRequestMethod());
        $this->assertInternalType('array', $request->getUrlParameters());
        $this->assertCount(0, $request->getUrlParameters());
        $this->assertInternalType('array', $request->getQueryParameters());
        $this->assertCount(0, $request->getQueryParameters());
        $this->assertInternalType('array', $request->getPostParameters());
        $this->assertCount(0, $request->getPostParameters());
        $this->assertTrue($this->getObjectAttribute($request, 'validateSsl'));
        $this->assertNull($request->getLastResponse());
        $this->assertNull($request->getCookieJar());
    }

    public function testConstructWithSign()
    {
        $request = new Request(Request::SIGN_LOGIN);

        $this->assertNotNull($request->getSign());
        $this->assertEquals(Request::SIGN_LOGIN, $request->getSign());
    }

    public function testConstructWithRequestMethod()
    {
        $request = new Request(null, Request::METHOD_POST);

        $this->assertEquals(Request::METHOD_POST, $request->getRequestMethod());
    }

    public function testConstructWithDisableSslVerification()
    {
        $request = new Request(null, null, false);

        $this->assertFalse($this->getObjectAttribute($request, 'validateSsl'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testIsValidSignWithoutSign()
    {
        /** @noinspection PhpParamsInspection (this is intentional) */
        Request::isValidSign();
    }

    public function testIsValidSignWithSign()
    {
        $validSignsByConstant = array(
            Request::SIGN_DATA_CUSTOMER_HISTORY,
            Request::SIGN_CARD_RELATED_DATA,
            Request::SIGN_LOGIN,
            Request::SIGN_LOGOUT,
            Request::SIGN_PERSONAL_DATA,
            Request::SIGN_SOAP_CUSTOMER_HISTORY,
            Request::SIGN_RESERVATION_HISTORY,
            Request::SIGN_UPDATE_PERSONAL_DATA,
            Request::SIGN_CARD_HISTORY,
            Request::SIGN_DELETE_ACCOUNT,
        );

        foreach ($validSignsByConstant as $sign) {
            $this->assertTrue(Request::isValidSign($sign));
        }

        $validSignsByValue = array(
            23,
            30,
            70,
            1070,
            2070,
            3023,
        );

        foreach ($validSignsByValue as $sign) {
            $this->assertTrue(Request::isValidSign($sign));
        }

        for ($idx = 0; $idx < 1000; $idx++) {
            $sign = '';

            for ($chr = 0; $chr < mt_rand(0, 5); $chr++) {
                $sign .= ord(mt_rand(0, 255));
            }

            if (!in_array($sign, $validSignsByConstant) && !in_array($sign, $validSignsByValue)) {
                $this->assertFalse(Request::isValidSign($sign));
            }
        }
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testIsValidRequestMethodWithoutRequestMethod()
    {
        /** @noinspection PhpParamsInspection (this is intentional) */
        Request::isValidRequestMethod();
    }

    public function testIsValidRequestMethodWithRequestMethod()
    {
        $validRequestMethodsByConstant = array(
            Request::METHOD_GET,
            Request::METHOD_POST,
        );

        foreach ($validRequestMethodsByConstant as $requestMethod) {
            $this->assertTrue(Request::isValidRequestMethod($requestMethod));
        }

        $validRequestMethodsByValue = array(
            'GET',
            'POST',
        );

        foreach ($validRequestMethodsByValue as $requestMethod) {
            $this->assertTrue(Request::isValidRequestMethod($requestMethod));
        }

        for ($idx = 0; $idx < 1000; $idx++) {
            $requestMethod = '';

            for ($chr = 0; $chr < mt_rand(0, 10); $chr++) {
                $requestMethod .= ord(mt_rand(0, 255));
            }

            if (!in_array($requestMethod, $validRequestMethodsByConstant) && !in_array($requestMethod, $validRequestMethodsByValue)) {
                $this->assertFalse(Request::isValidRequestMethod($requestMethod));
            }
        }
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetSignWithoutParameters()
    {
        $request = new Request();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $request->setSign();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid sign
     */
    public function testSetSignWithInvalidSign()
    {
        $request = new Request();

        $request->setSign('invalidSign');
    }

    public function testSetSignWithValidSign()
    {
        $request = new Request();

        $this->assertNull($request->getSign());

        $request->setSign(Request::SIGN_PERSONAL_DATA);

        $this->assertNotNull($request->getSign());
        $this->assertEquals(Request::SIGN_PERSONAL_DATA, $request->getSign());
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetRequestMethodWithoutParameters()
    {
        $request = new Request();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $request->setRequestMethod();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid request method
     */
    public function testSetRequestMethodWithInvalidRequestMethod()
    {
        $request = new Request();

        $request->setRequestMethod('invalidRequestMethod');
    }

    public function testSetRequestMethodWithValidRequestMethod()
    {
        $request = new Request();

        $this->assertEquals(Request::METHOD_GET, $request->getRequestMethod());

        $request->setRequestMethod(Request::METHOD_POST);

        $this->assertEquals(Request::METHOD_POST, $request->getRequestMethod());
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetCookieJarWithoutParameters()
    {
        $request = new Request();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $request->setCookieJar();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid cookie jar
     */
    public function testSetCookieJarWithInvalidCookieJar()
    {
        $request = new Request();

        $request->setCookieJar(dirname(__FILE__) . '/invalidCookieJarPath');
    }

    public function testSetCookieJarWithValidCookieJar()
    {
        $request = new Request();

        $this->assertNull($request->getCookieJar());

        $cookieJar = tempnam(sys_get_temp_dir(), 'test');
        $request->setCookieJar($cookieJar);
        unlink($cookieJar);

        $this->assertEquals($cookieJar, $request->getCookieJar());
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testAddUrlParameterWithoutParameters()
    {
        $request = new Request();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $request->addUrlParameter();
    }

    public function testAddUrlParameterWithParameter()
    {
        $request = new Request();

        $this->assertCount(0, $request->getUrlParameters());

        $request->addUrlParameter('testParameter');

        $this->assertCount(1, $request->getUrlParameters());
        $this->assertEquals('testParameter', current($request->getUrlParameters()));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testAddQueryParameterWithoutParameters()
    {
        $request = new Request();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $request->addQueryParameter();
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testAddQueryParameterWithInvalidParameter()
    {
        $request = new Request();

        $this->assertCount(0, $request->getQueryParameters());

        $request->addQueryParameter(array('foo'), 'bar');
    }

    public function testAddQueryParameterWithValidParameter()
    {
        $request = new Request();

        $this->assertCount(0, $request->getQueryParameters());

        $request->addQueryParameter('testParameter', 'testValue');

        $this->assertCount(1, $request->getQueryParameters());
        $this->assertArrayHasKey('testParameter', $request->getQueryParameters());
        $this->assertEquals('testValue', current($request->getQueryParameters()));
    }

    public function testAddQueryParameterWithArrayParameter()
    {
        $request = new Request();

        $this->assertCount(0, $request->getQueryParameters());

        $request->addQueryParameter('testParameter', array('testValue1', 'testValue2'));

        $this->assertCount(1, $request->getQueryParameters());
        $queryParameters = $request->getQueryParameters();

        $this->assertArrayHasKey('testParameter', $queryParameters);
        $this->assertInternalType('array', $queryParameters['testParameter']);
        $this->assertCount(2, $queryParameters['testParameter']);

        $this->assertEquals('testValue1', $queryParameters['testParameter'][0]);
        $this->assertEquals('testValue2', $queryParameters['testParameter'][1]);
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testAddPostParameterWithoutParameters()
    {
        $request = new Request();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $request->addPostParameter();
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testAddPostParameterWithInvalidParameter()
    {
        $request = new Request();

        $this->assertCount(0, $request->getPostParameters());

        $request->addPostParameter(array('foo'), 'bar');
    }

    public function testAddPostParameterWithValidParameter()
    {
        $request = new Request();

        $this->assertCount(0, $request->getPostParameters());

        $request->addPostParameter('testParameter', 'testValue');

        $this->assertCount(1, $request->getPostParameters());
        $this->assertArrayHasKey('testParameter', $request->getPostParameters());
        $this->assertEquals('testValue', current($request->getPostParameters()));
    }

    public function testAddPostParameterWithArrayParameter()
    {
        $request = new Request();

        $this->assertCount(0, $request->getPostParameters());

        $request->addPostParameter('testParameter', array('testValue1', 'testValue2'));

        $this->assertCount(1, $request->getPostParameters());
        $queryParameters = $request->getPostParameters();

        $this->assertArrayHasKey('testParameter', $queryParameters);
        $this->assertInternalType('array', $queryParameters['testParameter']);
        $this->assertCount(2, $queryParameters['testParameter']);

        $this->assertEquals('testValue1', $queryParameters['testParameter'][0]);
        $this->assertEquals('testValue2', $queryParameters['testParameter'][1]);
    }

    public function testGetBaseUrl()
    {
        $request = new MockRequest();

        // getBaseUrl is a protected method, to test it we need to call it via reflection
        $reflectionRequest = new ReflectionObject($request);
        $reflectionMethod  = $reflectionRequest->getMethod('getBaseUrl');
        $reflectionMethod->setAccessible(true);

        $this->assertEquals('pathe://mock/', $reflectionMethod->invoke($request));
    }

    public function testGetUrlSuffix()
    {
        $request = new MockRequest();

        // getUrlSuffix is a protected method, to test it we need to call it via reflection
        $reflectionRequest = new ReflectionObject($request);
        $reflectionMethod  = $reflectionRequest->getMethod('getUrlSuffix');
        $reflectionMethod->setAccessible(true);

        // since we haven't set a sign, it should initially return an empty suffix
        $this->assertEquals('', $reflectionMethod->invoke($request));

        $request->setSign(Request::SIGN_LOGIN);

        $this->assertEquals('ticketweb.php?sign=23', $reflectionMethod->invoke($request));
    }

    public function testBuildRequestUrl()
    {
        $request = new MockRequest();

        // buildRequestUrl is a protected method, to test it we need to call it via reflection
        $reflectionRequest = new ReflectionObject($request);
        $reflectionMethod  = $reflectionRequest->getMethod('buildRequestUrl');
        $reflectionMethod->setAccessible(true);

        // since we haven't set a sign, the request URL should be the base URL
        $this->assertEquals('pathe://mock/', $reflectionMethod->invoke($request));

        $request->setSign(Request::SIGN_LOGIN);

        $this->assertEquals('pathe://mock/ticketweb.php?sign=23', $reflectionMethod->invoke($request));

        $request->setSign(Request::SIGN_DATA_CUSTOMER_HISTORY);
        $request->addUrlParameter('testUrlParameter');

        $this->assertEquals('pathe://mock/CRM/UD/Cache/tickets_testUrlParameter.txt', $reflectionMethod->invoke($request));

        $request->addQueryParameter('testQueryParameter1', 'testValue1');

        $this->assertEquals('pathe://mock/CRM/UD/Cache/tickets_testUrlParameter.txt?testQueryParameter1=testValue1', $reflectionMethod->invoke($request));

        $request->addQueryParameter('testQueryParameter2', array('testValue2', 'testValue3'));

        $this->assertEquals('pathe://mock/CRM/UD/Cache/tickets_testUrlParameter.txt?testQueryParameter1=testValue1&testQueryParameter2%5B0%5D=testValue2&testQueryParameter2%5B1%5D=testValue3', $reflectionMethod->invoke($request));

        $request->setSign(Request::SIGN_LOGIN);

        $request->addQueryParameter('testQueryParameter3', array('four' => 'testValue4', 'five' => 'testValue5'));

        $this->assertEquals('pathe://mock/ticketweb.php?sign=23&testQueryParameter1=testValue1&testQueryParameter2%5B0%5D=testValue2&testQueryParameter2%5B1%5D=testValue3&testQueryParameter3%5Bfour%5D=testValue4&testQueryParameter3%5Bfive%5D=testValue5', $reflectionMethod->invoke($request));
    }

    public function testSend()
    {
        $request = new MockRequest();

        $response = $request->send();

        $this->assertInstanceOf('Capirussa\\Pathe\\Response', $response);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Timeout. Please login again, and enter UserCenterID', $response->getRawBody());
    }

    public function testGetLastResponse()
    {
        $request = new MockRequest();

        $this->assertNull($request->getLastResponse());

        $response = $request->send();

        $this->assertInstanceOf('Capirussa\\Pathe\\Response', $response);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Timeout. Please login again, and enter UserCenterID', $response->getRawBody());

        $response = $request->getLastResponse();

        $this->assertInstanceOf('Capirussa\\Pathe\\Response', $response);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Timeout. Please login again, and enter UserCenterID', $response->getRawBody());
    }
}