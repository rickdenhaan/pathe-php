<?php
require_once(dirname(__FILE__) . '/../../init.php');

use Capirussa\Pathe\Client;

/**
 * Tests Capirussa\Pathe\Client
 *
 */
class ClientTest extends PHPUnit_Framework_TestCase
{
    /**
     * List of files to delete in tearDown, if any
     *
     * @type string[]
     */
    private $filesToDelete = array();

    /**
     * Delete any files after running the tests
     *
     * @return void
     */
    public function tearDown()
    {
        foreach ($this->filesToDelete as $idx => $path) {
            if (file_exists($path) && is_writable($path)) {
                unlink($path);
            }
            unset($this->filesToDelete[$idx]);
        }
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testConstructWithoutParameters()
    {
        /** @noinspection PhpParamsInspection (this is intentional) */
        new Client();
    }

    public function testConstructWithParameters()
    {
        $client = new Client('test@example.com', 'testPassword');

        $this->assertEquals('test@example.com', $client->getUsername());
        $this->assertEquals('testPassword', $client->getPassword());
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetUsernameWithoutParameters()
    {
        $client = new Client('test@example.com', 'testPassword');

        /** @noinspection PhpParamsInspection (this is intentional) */
        $client->setUsername();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid username
     */
    public function testSetUsernameWithInvalidUsername()
    {
        $client = new Client('test@example.com', 'testPassword');

        $client->setUsername('invalidUsername');
    }

    public function testSetUsernameWithValidUsername()
    {
        $client = new Client('test@example.com', 'testPassword');

        $this->assertEquals('test@example.com', $this->getObjectAttribute($client, 'username'));
        $this->assertEquals('test@example.com', $client->getUsername());

        $client->setUsername('testOther@example.com');

        $this->assertEquals('testOther@example.com', $this->getObjectAttribute($client, 'username'));
        $this->assertEquals('testOther@example.com', $client->getUsername());
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetPasswordWithoutParameters()
    {
        $client = new Client('test@example.com', 'testPassword');

        /** @noinspection PhpParamsInspection (this is intentional) */
        $client->setPassword();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid password
     */
    public function testSetPasswordWithInvalidPassword()
    {
        $client = new Client('test@example.com', 'testPassword');

        $client->setPassword('');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid password
     */
    public function testSetPasswordWithInvalidType()
    {
        $client = new Client('test@example.com', 'testPassword');

        $client->setPassword(array('foo'));
    }

    public function testSetPasswordWithValidPassword()
    {
        $client = new Client('test@example.com', 'testPassword');

        $this->assertEquals('testPassword', $this->getObjectAttribute($client, 'password'));
        $this->assertEquals('testPassword', $client->getPassword());

        $client->setPassword('testOtherPassword');

        $this->assertEquals('testOtherPassword', $this->getObjectAttribute($client, 'password'));
        $this->assertEquals('testOtherPassword', $client->getPassword());
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetCustomerIdWithoutParameters()
    {
        $client = new Client('test@example.com', 'testPassword');

        /** @noinspection PhpParamsInspection (this is intentional) */
        $client->setCustomerId();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid customer ID
     */
    public function testSetCustomerIdWithInvalidId()
    {
        $client = new Client('test@example.com', 'testPassword');

        $client->setCustomerId('invalidCustomerId');
    }

    public function testSetCustomerIdWithValidId()
    {
        $client = new Client('test@example.com', 'testPassword');

        $this->assertNull($this->getObjectAttribute($client, 'customerId'));
        $this->assertNull($client->getCustomerId());

        $client->setCustomerId(12345);

        $this->assertEquals(12345, $this->getObjectAttribute($client, 'customerId'));
        $this->assertEquals(12345, $client->getCustomerId());
    }

    public function testDisableSslVerification()
    {
        $client = new Client('test@example.com', 'testPassword');

        $this->assertTrue($this->getObjectAttribute($client, 'validateSsl'));

        $client->disableSslVerification();

        $this->assertFalse($this->getObjectAttribute($client, 'validateSsl'));
    }

    public function testDisableSslVerificationToRequestPassthrough()
    {
        $client = new Client('test@example.com', 'testPassword');

        $this->assertTrue($this->getObjectAttribute($client, 'validateSsl'));

        // getting the request is done through a protected function, we have to call that through reflection
        $reflectionClient = new ReflectionObject($client);
        $reflectionMethod = $reflectionClient->getMethod('getRequest');
        $reflectionMethod->setAccessible(true);

        $request = $reflectionMethod->invoke($client);

        $this->assertTrue($this->getObjectAttribute($request, 'validateSsl'));

        $client->disableSslVerification();

        $this->assertFalse($this->getObjectAttribute($client, 'validateSsl'));

        $request = $reflectionMethod->invoke($client);

        $this->assertFalse($this->getObjectAttribute($request, 'validateSsl'));
    }

    public function testGetCookieJar()
    {
        $client = new Client('test@example.com', 'testPassword');

        $this->assertNull($this->getObjectAttribute($client, 'cookieJar'));

        // getCookieJar is a protected method, so we need to use reflection to call it
        $reflectionClient = new ReflectionObject($client);
        $reflectionMethod = $reflectionClient->getMethod('getCookieJar');
        $reflectionMethod->setAccessible(true);

        // getCookieJar is lazy-loading, so it will never return null
        $cookieJar = $reflectionMethod->invoke($client);

        $this->assertNotNull($cookieJar);
        $this->assertFileExists($cookieJar);
        $this->assertTrue(is_readable($cookieJar));
        $this->assertTrue(is_writable($cookieJar));

        // if called a second time, it should return the same file
        $cookieJar2 = $reflectionMethod->invoke($client);

        $this->assertEquals($cookieJar, $cookieJar2);

        // delete the file
        unlink($cookieJar);
    }

    public function testClearCookieJar()
    {
        $client = new Client('test@example.com', 'testPassword');

        $this->assertNull($this->getObjectAttribute($client, 'cookieJar'));

        // getCookieJar is a protected method, so we need to use reflection to call it
        $reflectionClient = new ReflectionObject($client);
        $reflectionGet = $reflectionClient->getMethod('getCookieJar');
        $reflectionGet->setAccessible(true);

        $cookieJar = $reflectionGet->invoke($client);

        $this->assertNotNull($cookieJar);
        $this->assertFileExists($cookieJar);
        $this->assertTrue(is_readable($cookieJar));
        $this->assertTrue(is_writable($cookieJar));

        // clearCookieJar is a protected method, so we need to use reflection to call it
        $reflectionClear = $reflectionClient->getMethod('clearCookieJar');
        $reflectionClear->setAccessible(true);

        $reflectionClear->invoke($client);

        $this->assertFileNotExists($cookieJar);

        $cookieJar2 = $reflectionGet->invoke($client);

        $this->assertNotNull($cookieJar2);
        $this->assertNotEquals($cookieJar, $cookieJar2);
        $this->assertFileExists($cookieJar2);
        $this->assertTrue(is_readable($cookieJar2));
        $this->assertTrue(is_writable($cookieJar2));

        $reflectionClear->invoke($client);
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Unable to login
     */
    public function testAuthenticateInvalidCredentials()
    {
        $client = new MockClient('test@example.com', 'testIncorrectPassword');

        // getCookieJar is a protected method, so we need to use reflection to call it
        $reflectionClient = new ReflectionObject($client);
        $reflectionGet = $reflectionClient->getMethod('getCookieJar');
        $reflectionGet->setAccessible(true);

        // add the cookie jar to the files to delete in tearDown(), because we expect an exception and can't clear it
        $this->filesToDelete[] = $reflectionGet->invoke($client);

        // authenticate is a protected method, so we need to use reflection to call it
        $reflectionMethod = $reflectionClient->getMethod('authenticate');
        $reflectionMethod->setAccessible(true);

        $reflectionMethod->invoke($client);
    }

    public function testAuthenticateValidResponse()
    {
        $client = new MockClient('test@example.com', 'testPassword');

        // getCookieJar is a protected method, so we need to use reflection to call it
        $reflectionClient = new ReflectionObject($client);
        $reflectionGet = $reflectionClient->getMethod('getCookieJar');
        $reflectionGet->setAccessible(true);

        // add the cookie jar to the files to delete in tearDown()
        $this->filesToDelete[] = $reflectionGet->invoke($client);

        // authenticate is a protected method, so we need to use reflection to call it
        $reflectionMethod = $reflectionClient->getMethod('authenticate');
        $reflectionMethod->setAccessible(true);

        $reflectionMethod->invoke($client);

        // the customer ID should now be set to our test user's ID
        $this->assertEquals(12345678, $client->getCustomerId());
    }

    public function testLogout()
    {
        $client = new MockClient('test@example.com', 'testPassword');

        // getCookieJar is a protected method, so we need to use reflection to call it
        $reflectionClient = new ReflectionObject($client);
        $reflectionGet = $reflectionClient->getMethod('getCookieJar');
        $reflectionGet->setAccessible(true);

        // add the cookie jar to the files to delete in tearDown()
        $cookieJar = $reflectionGet->invoke($client);
        $this->filesToDelete[] = $cookieJar;

        // authenticate is a protected method, so we need to use reflection to call it
        $reflectionMethod = $reflectionClient->getMethod('authenticate');
        $reflectionMethod->setAccessible(true);

        $reflectionMethod->invoke($client);

        // the customer ID should now be set to our test user's ID
        $this->assertEquals(12345678, $client->getCustomerId());

        // logout is a protected method, so we need to use reflection to call it
        $reflectionMethod = $reflectionClient->getMethod('logout');
        $reflectionMethod->setAccessible(true);

        $reflectionMethod->invoke($client);

        // the cookie jar should now have been deleted
        $this->assertFileNotExists($cookieJar);
    }

    public function testGetCustomerHistory()
    {
        $client = new MockClient('test@example.com', 'testPassword');

        // getCookieJar is a protected method, so we need to use reflection to call it
        $reflectionClient = new ReflectionObject($client);
        $reflectionGet = $reflectionClient->getMethod('getCookieJar');
        $reflectionGet->setAccessible(true);

        // add the cookie jar to the files to delete in tearDown()
        $cookieJar = $reflectionGet->invoke($client);
        $this->filesToDelete[] = $cookieJar;

        $history = $client->getCustomerHistory();

        $this->assertInternalType('array', $history);
        $this->assertCount(9, $history);
        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $history[0]);
        $this->assertEquals('Dawn of the Planet of the Apes', $history[0]->getEvent()->getMovieName());
        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $history[1]);
        $this->assertEquals('How To Train Your Dragon 2', $history[1]->getEvent()->getMovieName());
        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $history[2]);
        $this->assertEquals('Transcendence', $history[2]->getEvent()->getMovieName());
        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $history[3]);
        $this->assertEquals('Edge of Tomorrow', $history[3]->getEvent()->getMovieName());
        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $history[4]);
        $this->assertEquals('X-Men: Days of Future Past', $history[4]->getEvent()->getMovieName());
        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $history[5]);
        $this->assertEquals('Need for Speed', $history[5]->getEvent()->getMovieName());
        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $history[6]);
        $this->assertEquals('LEGO Movie, The', $history[6]->getEvent()->getMovieName());
        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $history[7]);
        $this->assertEquals('Frozen', $history[7]->getEvent()->getMovieName());
        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $history[8]);
        $this->assertEquals('Hobbit, The: The Desolation of Smaug', $history[8]->getEvent()->getMovieName());

        $this->assertFileNotExists($cookieJar);
    }
}