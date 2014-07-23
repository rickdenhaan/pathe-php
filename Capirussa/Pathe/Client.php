<?php
namespace Capirussa\Pathe;

class Client
{
    /**
     * Username for the Pathé Client Panel
     *
     * @type string
     */
    protected $username;

    /**
     * Password for the Pathé Client Panel
     *
     * @type string
     */
    protected $password;

    /**
     * Pathé customer identifier, will be automatically determined upon successful authentication
     *
     * @type int
     */
    protected $customerId;

    /**
     * Indicates whether the Pathe SSL certificate should be verified (only disable for debug purposes!)
     *
     * @type bool
     */
    private $validateSsl = true;

    /**
     * File resource handle to a cookie jar we can use cross-request
     *
     * @type string|null
     */
    protected $cookieJar = null;

    /**
     * Constructor. Sets the username and password
     *
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password)
    {
        $this->setUsername($username);
        $this->setPassword($password);
    }

    /**
     * Sets the password for the Pathé Client Panel
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
    }

    /**
     * Sets the password for the Pathé Client Panel
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
    }

    /**
     * Sets the customer ID for the Pathé Client Panel
     *
     * @param int|string $customerId
     * @throws \InvalidArgumentException
     */
    public function setCustomerId($customerId)
    {
        // the customer ID must be numeric
        if (mb_strlen($customerId) == 0 || preg_match('/[^0-9]/', $customerId)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid customer ID "%2$s", must be numeric',
                    __METHOD__,
                    $customerId
                )
            );
        }

        $this->customerId = intval($customerId, 10);
    }

    /**
     * Returns the configured username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Returns the configured password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the determined customer ID or null if one has not been set/determined
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * Disables SSL verification -- Only do this if you're debugging!
     *
     * @return void
     */
    public function disableSslVerification()
    {
        $this->validateSsl = false;
    }

    /**
     * Returns the cookie jar filename
     *
     * @return string
     */
    protected function getCookieJar()
    {
        if ($this->cookieJar === null) {
            $this->cookieJar = tempnam(sys_get_temp_dir(), 'pathe');
        }

        return $this->cookieJar;
    }

    /**
     * Removes and resets the cookie jar
     *
     * @return void
     */
    protected function clearCookieJar()
    {
        if ($this->cookieJar !== null) {
            unlink($this->cookieJar);
            $this->cookieJar = null;
        }
    }

    /**
     * Authenticates with the Pathé Client Panel
     *
     * @throws \LogicException
     * @return void
     */
    protected function authenticate()
    {
        // first perform a GET request on the dashboard to initialize a remote session
        $request = $this->getRequest(Request::SIGN_PERSONAL_DATA, Request::METHOD_GET);
        $request->setCookieJar($this->getCookieJar());
        $request->addQueryParameter(Request::QUERY_USER_CENTER_ID, Request::USER_CENTER_PATHE);
        $request->send();

        // now perform a POST request with the login credentials
        $request = $this->getRequest(Request::SIGN_LOGIN, Request::METHOD_POST);
        $request->setCookieJar($this->getCookieJar());
        $request->addPostParameter(Request::LOGIN_USERNAME, $this->getUsername());
        $request->addPostParameter(Request::LOGIN_PASSWORD, $this->getPassword());
        $request->send();

        // now perform another GET request on the dashboard, to make sure we were logged in
        $request = $this->getRequest(Request::SIGN_PERSONAL_DATA, Request::METHOD_GET);
        $request->setCookieJar($this->getCookieJar());
        $response = $request->send();

        // if the response contains a link to delete the current user, we know we're logged into the dashboard
        if (stripos($response->getRawBody(), 'href="javascript: DelUser();"') < 1) {
            throw new \LogicException(
                sprintf(
                    '%1$s: Unable to login to the Path&ecaute; Client Panel',
                    __METHOD__
                )
            );
        }

        // the response should have several links that contain the user's customer ID, find one
        $matches = array();
        preg_match('/<a.*href=\".*CustomerID=([0-9]+)\"/i', $response->getRawBody(), $matches);
        if (isset($matches[1])) {
            $this->setCustomerId($matches[1]);
        }
    }

    /**
     * Logs the user out of the Pathé Client Panel
     *
     * @return void
     */
    protected function logout()
    {
        $request = $this->getRequest(Request::SIGN_LOGOUT, Request::METHOD_GET);
        $request->setCookieJar($this->getCookieJar());
        $request->send();

        $this->clearCookieJar();
    }

    /**
     * Retrieves the customer history from the Pathé Client Panel
     *
     * @throws \LogicException
     * @return HistoryItem[]
     */
    public function findCustomerHistory()
    {
        // log into the client panel
        $this->authenticate();

        // get the customer history page
        $request = $this->getRequest(Request::SIGN_CUSTOMER_HISTORY, Request::METHOD_GET);
        $request->setCookieJar($this->getCookieJar());
        $request->addQueryParameter(Request::QUERY_USER_CENTER_ID, Request::USER_CENTER_PATHE);
        $request->addQueryParameter(Request::QUERY_TEMPLATE, Request::TEMPLATE_CARD_HISTORY);
        $request->addQueryParameter(Request::QUERY_CUSTOMER_ID, $this->getCustomerId());
        $request->addQueryParameter(Request::QUERY_LOAD_CARDS, false);
        $response = $request->send();

        // on this page, JavaScript calls a SearchCustomerHistory method with a random hash, we need that hash
        $dataFile = null;
        $matches = array();
        preg_match('/SearchCustomerHistory\(.*\'(.*)\'\);/i', $response->getRawBody(), $matches);
        if (isset($matches[1])) {
            $dataFile = $matches[1];
        } else {
            throw new \LogicException(
                sprintf(
                    '%1$s: Failed to determine the unique history hash',
                    __METHOD__
                )
            );
        }

        // we need to call a SOAP method that will generate the data file
        $request = $this->getRequest(Request::SIGN_SOAP_CUSTOMER_HISTORY, Request::METHOD_GET);
        $request->setCookieJar($this->getCookieJar());
        $request->addQueryParameter(Request::QUERY_HISTORY, true);
        $request->addQueryParameter(Request::QUERY_CUSTOMER_ID, $this->getCustomerId());
        $request->addQueryParameter(Request::QUERY_SELECTED_YEAR, 0);
        $request->send();

        // now we can request the text file that contains the customer history
        $request = $this->getRequest(Request::SIGN_DATA_CUSTOMER_HISTORY, Request::METHOD_GET);
        $request->setCookieJar($this->getCookieJar());
        $request->addUrlParameter($dataFile);
        $response = $request->send();

        // parse the text file into an array of history items
        $retValue = HistoryItem::parseHistoryItemsFromDataFile($response->getRawBody());

        // to be nice to Pathé and not flood their servers with session files, log the user out properly
        $this->logout();

        return $retValue;
    }

    /**
     * Returns a new Request object
     *
     * @param int    $sign          (Optional) Defaults to null
     * @param string $requestMethod (Optional) Defaults to Request::METHOD_GET
     * @return Request
     */
    protected function getRequest($sign = null, $requestMethod = Request::METHOD_GET)
    {
        return new Request($sign, $requestMethod, $this->validateSsl);
    }
}