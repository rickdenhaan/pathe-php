<?php
namespace RickDenHaan\Pathe;

use Buzz\Browser;
use Buzz\Client\Curl;
use Buzz\Listener\CookieListener;
use Buzz\Message\MessageInterface;
use Buzz\Message\Response;
use RickDenHaan\Pathe\Model\Session;

/**
 * The Client is the main hub of this library. It contains methods to perform all actions currently supported.
 *
 * @package RickDenHaan\Pathe
 */
class Client
{
    /**
     * Contains the username to use when authenticating with Mijn Pathé.
     *
     * @type string
     */
    protected $username;

    /**
     * Contains the password to use when authenticating with Mijn Pathé.
     *
     * @type string
     */
    protected $password;

    /**
     * Url to the Mijn Pathé API
     *
     * @type string
     */
    private $apiUrl = 'https://connect.pathe.nl/v1/';

    /**
     * A boolean indicator which can be set to false so that the Pathé SSL certificate is not verified. **Not
     * recommended.**
     *
     * @type bool
     */
    private $validateSsl = true;

    /**
     * The timeout to use when connecting to Pathé (in seconds).
     *
     * @type int
     */
    private $timeout = 30;

    /**
     * Contains the Response object for the last submitted request.
     *
     * @type Response
     */
    protected $response;

    /**
     * Unique client token for the current script
     *
     * @type string
     */
    private $clientToken;

    /**
     * Unique device token for the current machine
     *
     * @type string
     */
    private $deviceToken;

    /**
     * Contains a static reference to the current Client instance
     *
     * @type Client
     */
    private static $instance;

    /**
     * The client's browsing session
     *
     * @type Session
     */
    private $session;

    /**
     * Returns the current Client instance
     *
     * @return static
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            throw new \LogicException("Client has not been initialized. Did you call ::init()?");
        }

        return self::$instance;
    }

    /**
     * Initializes a new Client instance
     *
     * @param string $username
     * @param string $password
     * @return static
     */
    public static function init($username, $password)
    {
        $client = new static();
        $client->setUsername($username);
        $client->setPassword($password);

        $client->createSession(false);

        self::$instance = $client;

        return self::$instance;
    }

    /**
     * Properly close the current session when the client is no longer needed
     *
     */
    public function __destruct()
    {
        if ($this->session !== null) {
            $this->session->close();
        }
    }

    /**
     * This method can be used to update your username, for example if you're logging in using your email address
     * instead of your username and you've just changed your email address. It accepts one argument which must be a
     * valid email address, and returns nothing.
     *
     * <code>
     * $client = new Client('user@example.com', 'p4s5w0rd');
     * $client->setUsername('newuser@example.com');
     * </code>
     *
     * @param string $username
     * @throws \InvalidArgumentException
     */
    public function setUsername($username)
    {
        // validate that the username is an email address
        if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid username, the username must be a valid email address',
                    __METHOD__
                )
            );
        }

        $this->username = $username;

        // if we were logged in, create a new session
        if ($this->session !== null) {
            $this->createSession();
        }
    }

    /**
     * This method can be used to update your password, for example if you've just changed it and don't want to
     * re-initialize the Client. It accepts one argument, which must be a non-empty string, and returns nothing.
     *
     * <code>
     * $client = new Client('user@example.com', 'p4s5w0rd');
     * $client->setPassword('newPassword123');
     * </code>
     *
     * @param string $password
     * @throws \InvalidArgumentException
     */
    public function setPassword($password)
    {
        // the password must a string
        if (!is_string($password) || mb_strlen($password) == 0) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid password, it cannot be empty',
                    __METHOD__
                )
            );
        }

        $this->password = $password;

        // if we were logged in, create a new session
        if ($this->session !== null) {
            $this->createSession();
        }
    }

    /**
     * Creates a new session, closing the previous one if one existed
     *
     * @param bool $assert If false, will not throw an exception if the session could not be created
     * @return bool
     * @throws \Exception
     */
    public function createSession($assert = true)
    {
        try {
            $session = Session::create();

            if ($this->session !== null) {
                $this->session->close();
            }

            $this->session = $session;

            return true;
        } catch (\Exception $ex) {
            if ($assert) {
                throw $ex;
            }

            return false;
        }
    }

    /**
     * Returns the current session
     *
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Returns the username configured in the client. Returns a string, and accepts no arguments.
     *
     * <code>
     * $client = new Client('user@example.com', 'p4s5w0rd');
     * $username = $client->getUsername();
     * </code>
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * This method returns the password configured in the client. It accepts no arguments and returns a string.
     *
     * <code>
     * $client = new Client('user@example.com', 'p4s5w0rd');
     * $password = $client->getPassword();
     * </code>
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Parses the HTTP status header from the provided response
     *
     * @param MessageInterface $response
     * @return int
     */
    private function parseResponseCode(MessageInterface $response)
    {
        $retValue = 0;

        foreach ($response->getHeaders() as $header) {
            $matches = array();
            if (preg_match('/^HTTP\/[0-9.]+ ([0-9]+) /i', $header, $matches)) {
                $retValue = intval($matches[1], 10);
                break;
            }
        }

        return $retValue;
    }

    /**
     * Sends a GET request to Mijn Pathé
     *
     * @param string        $uri
     * @param int           $expectedStatusCode (Optional) Defaults to 200
     * @return array
     */
    public function get($uri, $expectedStatusCode = 200)
    {
        // get a Browser
        $browser = $this->getBrowser();

        // submit the request
        $this->response = $browser->get(
            $this->apiUrl . $uri,
            $this->getRequestHeaders()
        );

        // validate the response code
        if ($this->parseResponseCode($this->response) != $expectedStatusCode) {
            throw new \UnexpectedValueException("Status code does not match expected code");
        }

        $response = json_decode($this->response->getContent(), true);
        if (!is_array($response)) {
            throw new \UnexpectedValueException("Could not decode JSON data");
        }

        return $response;
    }

    /**
     * Sends a POST request to Mijn Pathé
     *
     * @param string        $uri
     * @param array         $data               (Optional) Defaults to an empty array
     * @param int           $expectedStatusCode (Optional) Defaults to 200
     * @return array
     */
    public function post($uri, $data = array(), $expectedStatusCode = 200)
    {
        // get a Browser
        $browser = $this->getBrowser();

        // build the form data
        $formData = http_build_query($data);

        // submit the form
        $this->response = $browser->post(
            $this->apiUrl . $uri,
            $this->getRequestHeaders($formData),
            $formData
        );

        // validate the response code
        if ($this->parseResponseCode($this->response) != $expectedStatusCode) {
            throw new \UnexpectedValueException("Status code does not match expected code");
        }

        $response = json_decode($this->response->getContent(), true);
        if (!is_array($response)) {
            throw new \UnexpectedValueException("Could not decode JSON data");
        }

        return $response;
    }

    /**
     * Sends a DELETE request to Mijn Pathé
     *
     * @param string        $uri
     * @param int           $expectedStatusCode (Optional) Defaults to 200
     * @return void
     */
    public function delete($uri, $expectedStatusCode = 200)
    {
        // get a Browser
        $browser = $this->getBrowser();

        // submit the request
        $this->response = $browser->delete(
            $this->apiUrl . $uri,
            $this->getRequestHeaders(null)
        );

        // validate the response code
        if ($this->parseResponseCode($this->response) != $expectedStatusCode) {
            throw new \UnexpectedValueException("Status code does not match expected code");
        }
    }

    /**
     * This method should really only be used if your server is having problems verifying the SSL certificates used by
     * Pathé. Disabling the SSL verification opens the door to potential man-in-the-middle attacks by not checking
     * whether the SSL certificate has been spoofed. But, if you **really** want to do this, just call this method. It
     * takes no arguments and returns nothing.
     *
     * <code>
     * $client = new Client('user@example.com', 'p4s5w0rd');
     * $client->disableSslVerification();
     * </code>
     *
     * @return void
     */
    public function disableSslVerification()
    {
        $this->validateSsl = false;
    }

    /**
     * This method can be used to configure the connection timeout. Provide an integer in seconds.
     *
     * <code>
     * $client = new Client();
     * $client->setTimeout(10);
     * </code>
     *
     * @param int $timeout
     * @return void
     */
    public function setTimeout($timeout)
    {
        $this->timeout = (int)$timeout;
    }

    /**
     * This method is used internally to retrieve a Buzz\Browser object, used to perform requests.
     *
     * <code>
     * $browser = $this->getBrowser();
     * $response = $browser->get('http://...');
     * </code>
     *
     * @return Browser
     *
     * Unittests overwrite this method to retrieve a mock browser, so
     * @codeCoverageIgnore
     */
    protected function getBrowser()
    {
        $client = new Curl();
        $client->setTimeout($this->timeout);
        $client->setVerifyPeer($this->validateSsl);

        $browser = new Browser($client);

        $listener = new CookieListener();
        $browser->setListener($listener);

        return $browser;
    }

    /**
     * This method is used internally to build the default request headers.
     *
     * @param string|array  $requestBody (Optional) Defaults to null
     * @return array
     */
    protected function getRequestHeaders($requestBody = null)
    {
        $retValue = array(
            'User-Agent'     => 'RickDenHaan-Pathe/2.0 (+http://github.com/rickdenhaan/pathe-php)',
            'X-Client-Token' => $this->getClientToken(),
            'X-Device-Token' => $this->getDeviceToken(),
        );

        if ($this->session !== null) {
            $retValue['X-Session-Token'] = $this->session->getSessionToken();
        }

        if ($requestBody !== null) {
            if (is_array($requestBody)) {
                $requestBody = http_build_query($requestBody);
            }

            $retValue['Content-Length'] = mb_strlen($requestBody);
        }

        return $retValue;
    }

    /**
     * Determines the unique client token for the current script location
     *
     * @return string
     */
    private function getClientToken()
    {
        if ($this->clientToken === null) {
            $this->clientToken = '1f8ecc5806054255903af1f34157bf38';
        }

        return $this->clientToken;
    }

    /**
     * Determines the unique device token for the current script location
     *
     * @return string
     */
    private function getDeviceToken()
    {
        if ($this->deviceToken === null) {
            $machineName = gethostname();
            $userName    = get_current_user();

            $token = strtoupper(
                md5(
                    sprintf(
                        'pathe-php.client.%s.%s',
                        $machineName,
                        $userName
                    )
                )
            );

            $this->deviceToken = sprintf(
                '%s-%s-%s-%s-%s',
                substr($token, 0, 8),
                substr($token, 8, 4),
                substr($token, 12, 4),
                substr($token, 16, 4),
                substr($token, 20, 12)
            );
        }

        return $this->deviceToken;
    }

    /**
     * This method returns the Response object returned for the last request, in case the method itself returned
     * something else. It accepts no arguments.
     *
     * <code>
     * $client = new Client(1234);
     * $history = $client->getHistory();
     * $response = $client->getLastResponse();
     * </code>
     *
     * @return \Buzz\Message\Response|null
     */
    public function getLastResponse()
    {
        return $this->response;
    }
}