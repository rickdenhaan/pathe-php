<?php
namespace Capirussa\Pathe;

class Request
{
    /**
     * Page identifiers
     */
    const SIGN_LOGIN                = 23;
    const SIGN_LOGOUT               = 3023;
    const SIGN_RESERVATION_HISTORY  = 24;
    const SIGN_PERSONAL_DATA        = 30;
    const SIGN_UPDATE_PERSONAL_DATA = 31;
    const SIGN_CARD_HISTORY         = 69;
    const SIGN_CARD_RELATED_DATA    = 70;
    const SIGN_DELETE_ACCOUNT       = 91;

    /**
     * SOAP identifiers
     */
    const SIGN_SOAP_CUSTOMER_HISTORY = 1070;

    /**
     * Data identifiers
     */
    const SIGN_DATA_CUSTOMER_HISTORY = 2070;

    /**
     * Query string parameters
     */
    const QUERY_CUSTOMER_ID    = 'CustomerID';
    const QUERY_HISTORY        = 'History';
    const QUERY_LOAD_CARDS     = 'LoadCards';
    const QUERY_SELECTED_YEAR  = 'SelYear';
    const QUERY_USER_CENTER_ID = 'UserCenterID';
    const QUERY_TEMPLATE       = 'template';

    /**
     * Post parameters
     */
    const DELETE_PASSWORD                   = 'Password';
    const DELETE_CONFIRM                    = 'Check';
    const LOGIN_USERNAME                    = 'Login';
    const LOGIN_PASSWORD                    = 'Password';
    const LOGIN_CONFIRM_PASSWORD            = 'PasswordConfirm';
    const LOGIN_EMAIL_ADDRESS               = 'Email';
    const LOGIN_CONFIRM_EMAIL_ADDRESS       = 'EmailConfirm';
    const LOGIN_NEWSLETTER                  = 'WantInfoPerMail';
    const LOGIN_SUBMIT_X                    = 'submitButton.x';
    const LOGIN_SUBMIT_Y                    = 'submitButton.y';
    const PERSONAL_DATA_GENDER              = 'Gender';
    const PERSONAL_DATA_FIRST_NAME          = 'FirstName';
    const PERSONAL_DATA_MIDDLE_NAME         = 'MiddleName';
    const PERSONAL_DATA_LAST_NAME           = 'LastName';
    const PERSONAL_DATA_EMAIL_ADDRESS       = 'Email';
    const PERSONAL_DATA_COUNTRY             = 'CountryID';
    const PERSONAL_DATA_BIRTH_DAY           = 'BirthDay';
    const PERSONAL_DATA_BIRTH_MONTH         = 'BirthMonth';
    const PERSONAL_DATA_BIRTH_YEAR          = 'BirthYear';
    const PERSONAL_DATA_STREET_NAME         = 'Address1';
    const PERSONAL_DATA_HOUSE_NUMBER        = 'HouseNbr';
    const PERSONAL_DATA_HOUSE_NUMBER_SUFFIX = 'HouseNbrPostFix';
    const PERSONAL_DATA_POSTAL_CODE         = 'ZIP';
    const PERSONAL_DATA_CITY                = 'City';
    const PERSONAL_DATA_MOBILE_PHONE_NUMBER = 'Mobile';
    const PERSONAL_DATA_PASSWORD            = 'LoginPassword';
    const PERSONAL_DATA_NEWSLETTER          = 'InfoWanted';
    const RESERVATION_HISTORY_WEEK_COUNT    = 'Weeks';
    const CARD_HISTORY_CARD_NUMBER          = 'CustomerCardNumber';
    const CARD_HISTORY_PIN_CODE             = 'PIN';
    const CARD_HISTORY_MONTH                = 'Month';
    const CARD_HISTORY_YEAR                 = 'Year';

    /**
     * Possible user centers
     */
    const USER_CENTER_PATHE = 1;

    /**
     * Possible templates
     */
    const TEMPLATE_CARD_HISTORY = '0.cards.history.';

    /**
     * Possible request methods
     */
    const METHOD_GET  = 'GET';
    const METHOD_POST = 'POST';

    /**
     * Base URL for all calls
     *
     * @type string
     */
    protected $baseUrl = 'https://onlinetickets2.pathe.nl/';

    /**
     * URL suffixes for all page identifiers
     *
     * @type array
     */
    protected $urlSuffix = array(
        self::SIGN_LOGIN                 => 'ticketweb.php?sign=%1$s',
        self::SIGN_LOGOUT                => 'CRM/logout.php',
        self::SIGN_PERSONAL_DATA         => 'ticketweb.php?sign=%1$s',
        self::SIGN_UPDATE_PERSONAL_DATA  => 'ticketweb.php?sign=%1$s',
        self::SIGN_CARD_HISTORY          => 'crm/cardTransactions.php',
        self::SIGN_CARD_RELATED_DATA     => 'ticketweb.php?sign=%1$s',
        self::SIGN_SOAP_CUSTOMER_HISTORY => 'CRM/soap.php',
        self::SIGN_DATA_CUSTOMER_HISTORY => 'CRM/UD/Cache/tickets_%%1$s.txt',
        self::SIGN_RESERVATION_HISTORY   => 'ticketweb.php?sign=%1$s',
        self::SIGN_DELETE_ACCOUNT        => 'ticketweb.php?sign=%1$s',
    );

    /**
     * Which page to load
     *
     * @type int
     */
    protected $sign;

    /**
     * Which request method to use for this request
     *
     * @type string
     */
    protected $requestMethod = self::METHOD_GET;

    /**
     * Extra URL parameters that will be printf()'d into the URL
     *
     * @var array
     */
    protected $urlParameters = array();

    /**
     * Extra parameters to append to the URL
     *
     * @type mixed[]
     */
    protected $queryParameters = array();

    /**
     * Post data to submit with POST requests
     *
     * @type mixed[]
     */
    protected $postParameters = array();

    /**
     * Whether or not to validate the SSL certificates
     *
     * @type bool
     */
    protected $validateSsl = true;

    /**
     * The response for the last submitted request
     *
     * @type Response
     */
    protected $response;

    /**
     * Cookie jar to use for requests
     *
     * @type string|null
     */
    protected $cookieJar;

    /**
     * Constructor. Initializes the request
     *
     * @param int    $sign          (Optional) Defaults to null
     * @param string $requestMethod (Optional) Defaults to self::METHOD_GET
     * @param bool   $validateSsl   (Optional) Defaults to true, only set to false for debugging!
     */
    public function __construct($sign = null, $requestMethod = self::METHOD_GET, $validateSsl = true)
    {
        // if a sign was given, set it
        if ($sign !== null) {
            $this->setSign($sign);
        }

        // if a request method was given, set it
        if ($requestMethod !== null) {
            $this->setRequestMethod($requestMethod);
        }

        $this->validateSsl = $validateSsl;
    }

    /**
     * Sets the page identifier to load
     *
     * @param int $sign
     * @throws \InvalidArgumentException
     */
    public function setSign($sign)
    {
        // validate the sign by checking whether it is defined as a constant in this class
        if (!self::isValidSign($sign)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid sign \'%2$s\' given',
                    __METHOD__,
                    $sign
                )
            );
        }

        $this->sign = $sign;
    }

    /**
     * Sets the request method to use
     *
     * @param string $requestMethod
     * @throws \InvalidArgumentException
     */
    public function setRequestMethod($requestMethod)
    {
        // validate the request method by checking whether it is defined as a constant in this class
        if (!self::isValidRequestMethod($requestMethod)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid request method \'%2$s\' given',
                    __METHOD__,
                    $requestMethod
                )
            );
        }

        $this->requestMethod = $requestMethod;
    }

    /**
     * Sets the cookie jar file name
     *
     * @param string $cookieJar
     * @throws \InvalidArgumentException
     */
    public function setCookieJar($cookieJar)
    {
        // validate that the given file name actually exists and is both writable and readable
        if (!file_exists($cookieJar) || !is_file($cookieJar) || !is_readable($cookieJar) || !is_writable($cookieJar)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid cookie jar \'%2$s\' given, file does not exist or read/write access is missing',
                    __METHOD__,
                    $cookieJar
                )
            );
        }

        $this->cookieJar = $cookieJar;
    }

    /**
     * Returns the cookie jar
     *
     * @return string|null
     */
    public function getCookieJar()
    {
        return $this->cookieJar;
    }

    /**
     * Adds a parameter to the list of parameters to put into the URL
     *
     * @param mixed $value
     */
    public function addUrlParameter($value)
    {
        $this->urlParameters[] = $value;
    }

    /**
     * Adds a query parameter to the list of parameters
     *
     * @param string $parameter
     * @param mixed  $value
     */
    public function addQueryParameter($parameter, $value)
    {
        $this->queryParameters[$parameter] = $value;
    }

    /**
     * Adds a post parameter to the list of parameters
     *
     * @param string $parameter
     * @param mixed  $value
     */
    public function addPostParameter($parameter, $value)
    {
        $this->postParameters[$parameter] = $value;
    }

    /**
     * Returns the request method to use
     *
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }

    /**
     * Sends the request and returns a Response
     *
     * @return Response
     */
    public function send()
    {
        return $this->doRequest();
    }

    /**
     * Sends the request and returns a Response
     *
     * @throws \Exception
     * @return Response
     *
     * Unittests should never talk to the live Pathe panel, they use a mock request, so:
     * @codeCoverageIgnore
     */
    protected function doRequest()
    {
        // build the request URL
        $requestUrl = $this->buildRequestUrl();

        // set up the CURL request options
        $curlOptions = array(
            CURLOPT_SSL_VERIFYPEER => $this->validateSsl,
            CURLOPT_SSL_VERIFYHOST => $this->validateSsl ? 2 : 0,
            CURLOPT_FAILONERROR    => false,
            CURLOPT_HEADER         => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT      => 'Capirussa/1.0 (+http://github.com/rickdenhaan/pathe-php)'
        );

        // add the cookie jar if we have one
        if ($this->getCookieJar() !== null) {
            $curlOptions[CURLOPT_COOKIEFILE] = $this->getCookieJar();
            $curlOptions[CURLOPT_COOKIEJAR]  = $this->getCookieJar();
        }

        // if this is a post request, tell CURL that
        if ($this->getRequestMethod() == self::METHOD_POST) {
            $curlOptions[CURLOPT_POST] = true;

            // check whether any post data was set
            if (count($this->getPostParameters()) > 0) {
                $curlOptions[CURLOPT_POSTFIELDS] = $this->getPostParameters();
            }
        }

        // initialize and configure the CURL request
        $curl = curl_init($requestUrl);
        curl_setopt_array(
            $curl,
            $curlOptions
        );

        // execute the CURL request
        $result = curl_exec($curl);

        // check whether the server threw a fit (would have nothing to do with the remote server, because we configured the CURL request not to throw an error if the HTTP request fails)
        $error = curl_error($curl);
        if ($error != '') {
            throw new \Exception($error);
        }

        // close the CURL request
        curl_close($curl);

        // parse the response body and return the Response object
        $this->response = new Response($result);

        return $this->response;
    }

    /**
     * Builds the full request URL for this request
     *
     * @return string
     */
    protected function buildRequestUrl()
    {
        $retValue = $this->getBaseUrl();

        $retValue .= $this->getUrlSuffix();

        if (count($this->getUrlParameters()) > 0) {
            $retValue = vsprintf($retValue, $this->getUrlParameters());
        }

        $queryParameters = $this->getQueryParameters();

        if (count($queryParameters) > 0) {
            $retValue .= (strpos($retValue, '?') > 0 ? '&' : '?') . http_build_query($queryParameters);
        }

        return $retValue;
    }

    /**
     * Returns the base URL
     *
     * @return string
     */
    protected function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Returns the URL suffix for the current sign
     *
     * @return string
     */
    protected function getUrlSuffix()
    {
        $retValue = '';

        if (isset($this->urlSuffix[$this->getSign()])) {
            $retValue = sprintf($this->urlSuffix[$this->getSign()], $this->getSign());
        }

        return $retValue;
    }

    /**
     * Returns the current page identifier
     *
     * @return int
     */
    public function getSign()
    {
        return $this->sign;
    }

    /**
     * Returns the URL parameters
     *
     * @return mixed[]
     */
    public function getUrlParameters()
    {
        return $this->urlParameters;
    }

    /**
     * Returns the query parameters
     *
     * @return mixed[]
     */
    public function getQueryParameters()
    {
        return $this->queryParameters;
    }

    /**
     * Returns the post parameters
     *
     * @return mixed[]
     */
    public function getPostParameters()
    {
        return $this->postParameters;
    }

    /**
     * Returns the last response
     *
     * @return Response
     */
    public function getLastResponse()
    {
        return $this->response;
    }

    /**
     * Uses reflection to determine whether a given sign is valid
     *
     * @param int $sign
     * @return bool
     */
    public static function isValidSign($sign)
    {
        // validate the sign by checking whether it is defined as a constant in this class
        $reflectionClass  = new \ReflectionClass(get_class());
        $definedConstants = $reflectionClass->getConstants();

        $signIsValid = false;
        foreach ($definedConstants as $constantName => $constantValue) {
            if ($constantValue == $sign && strlen($constantName) > 5 && strtoupper(substr($constantName, 0, 5)) == 'SIGN_') {
                $signIsValid = true;
                break;
            }
        }

        return $signIsValid;
    }

    /**
     * Uses reflection to determine whether a given request method is valid
     *
     * @param string $requestMethod
     * @return bool
     */
    public static function isValidRequestMethod($requestMethod)
    {
        // validate the request method by checking whether it is defined as a constant in this class
        $reflectionClass  = new \ReflectionClass(get_class());
        $definedConstants = $reflectionClass->getConstants();

        $requestMethodIsValid = false;
        foreach ($definedConstants as $constantName => $constantValue) {
            if ($constantValue == $requestMethod && strlen($constantName) > 7 && strtoupper(substr($constantName, 0, 7)) == 'METHOD_') {
                $requestMethodIsValid = true;
                break;
            }
        }

        return $requestMethodIsValid;
    }
}