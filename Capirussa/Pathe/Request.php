<?php
namespace Capirussa\Pathe;

/**
 * The Request object is used to submit a request to Mijn Pathé. You will probably never need to use this object,
 * because all logic for building a Request object is contained in the Client object.
 *
 * @package Capirussa\Pathe
 */
class Request
{
    /**
     * This constant is a unique page identifier that refers to the login page.
     */
    const SIGN_LOGIN = 23;

    /**
     * This constant is a unique page identifier that refers to the logout page.
     */
    const SIGN_LOGOUT = 3023;

    /**
     * This constant is a unique page identifier that refers to the page where a customer's reservation history can be
     * retrieved.
     */
    const SIGN_RESERVATION_HISTORY = 24;

    /**
     * This constant is a unique page identifier that refers to the Mijn Pathé dashboard containing the customer's
     * details.
     */
    const SIGN_PERSONAL_DATA = 30;

    /**
     * This constant is a unique page identifier that refers to the page where a user's personal details can be
     * modified.
     */
    const SIGN_UPDATE_PERSONAL_DATA = 31;

    /**
     * This constant is a unique page identifier used for requesting an Unlimited or Gold Card's usage history.
     */
    const SIGN_CARD_HISTORY = 69;

    /**
     * This constant is a unique page identifier used for requesting various card-related details.
     */
    const SIGN_CARD_RELATED_DATA = 70;

    /**
     * This constant is a unique page identifier that refers to the page where an account can be deleted.
     */
    const SIGN_DELETE_ACCOUNT = 91;

    /**
     * This constant is a unique page identifier that refers to a URL that needs to be called to trigger the
     * generation of the pipe-delimited text file containing a customer's history.
     */
    const SIGN_SOAP_CUSTOMER_HISTORY = 1070;

    /**
     * This constant is a unique page identifier that refers to the pipe-delimited text file that contains a
     * customer's history.
     */
    const SIGN_DATA_CUSTOMER_HISTORY = 2070;

    /**
     * This constant refers to the name of a query parameter added to some request URLs.
     */
    const QUERY_CUSTOMER_ID = 'CustomerID';

    /**
     * This constant refers to the name of a query parameter added to some request URLs.
     */
    const QUERY_HISTORY = 'History';

    /**
     * This constant refers to the name of a query parameter added to some request URLs.
     */
    const QUERY_LOAD_CARDS = 'LoadCards';

    /**
     * This constant refers to the name of a query parameter added to some request URLs.
     */
    const QUERY_SELECTED_YEAR = 'SelYear';

    /**
     * This constant refers to the name of a query parameter added to some request URLs.
     */
    const QUERY_USER_CENTER_ID = 'UserCenterID';

    /**
     * This constant refers to the name of a query parameter added to some request URLs.
     */
    const QUERY_TEMPLATE = 'template';

    /**
     * This constant is used when deleting a Mijn Pathé account, and is the name of the form field where you need to
     * enter your password to confirm that you really want to delete the account.
     */
    const DELETE_PASSWORD = 'Password';

    /**
     * This constant is used when deleting a Mijn Pathé account, and is the name of the form's checkbox that is used
     * to confirm that you really want to delete the account.
     */
    const DELETE_CONFIRM = 'Check';

    /**
     * This constant is used when logging into an existing Mijn Pathé account, and is the name of the form field where
     * you need to enter your username.
     */
    const LOGIN_USERNAME = 'Login';

    /**
     * This constant is used when registering a new Mijn Pathé account or when logging into an existing account, and is
     * the name of the form field where you need to enter your password.
     */
    const LOGIN_PASSWORD = 'Password';

    /**
     * This constant is used when registering a new Mijn Pathé account, and is the name of the form field where you
     * need to confirm your password.
     */
    const LOGIN_CONFIRM_PASSWORD = 'PasswordConfirm';

    /**
     * This constant is used when registering a new Mijn Pathé account or when requesting a forgotten password, and is
     * the name of the form field where you need to enter your email address.
     */
    const LOGIN_EMAIL_ADDRESS = 'Email';

    /**
     * This constant is used when registering a new Mijn Pathé account, and is the name of the form field where you
     * need to confirm your email address.
     */
    const LOGIN_CONFIRM_EMAIL_ADDRESS = 'EmailConfirm';

    /**
     * This constant is used when registering a new Mijn Pathé account, and is the name of the form's checkbox that is
     * used to indicate whether or not you want to receive the weekly newsletter.
     */
    const LOGIN_NEWSLETTER = 'WantInfoPerMail';

    /**
     * This constant is used when registering a new Mijn Pathé account or when updating an existing account, and is
     * the name of the form's radio buttons where you need to select your gender.
     */
    const PERSONAL_DATA_GENDER = 'Gender';

    /**
     * This constant is used when registering a new Mijn Pathé account or when updating an existing account, and is
     * the name of the form field where you need to enter your first name.
     */
    const PERSONAL_DATA_FIRST_NAME = 'FirstName';

    /**
     * This constant is used when registering a new Mijn Pathé account or when updating an existing account, and is
     * the name of the form field where you need to enter your optional middle name.
     */
    const PERSONAL_DATA_MIDDLE_NAME = 'MiddleName';

    /**
     * This constant is used when registering a new Mijn Pathé account or when updating an existing account, and is
     * the name of the form field where you need to enter your last name.
     */
    const PERSONAL_DATA_LAST_NAME = 'LastName';

    /**
     * This constant is used when updating an existing Mijn Pathé account and is the name of the form field where you
     * need to enter your email address.
     */
    const PERSONAL_DATA_EMAIL_ADDRESS = 'Email';

    /**
     * This constant is used when updating an existing Mijn Pathé account and is the name of the form field where you
     * need to enter the country where you live.
     */
    const PERSONAL_DATA_COUNTRY = 'CountryID';

    /**
     * This constant is used when registering a new Mijn Pathé account or when updating an existing account, and is
     * the name of the form field where you need to enter the day part of your date of birth.
     */
    const PERSONAL_DATA_BIRTH_DAY = 'BirthDay';

    /**
     * This constant is used when registering a new Mijn Pathé account or when updating an existing account, and is
     * the name of the form field where you need to enter the month part of your date of birth.
     */
    const PERSONAL_DATA_BIRTH_MONTH = 'BirthMonth';

    /**
     * This constant is used when registering a new Mijn Pathé account or when updating an existing account, and is
     * the name of the form field where you need to enter the year part of your date of birth.
     */
    const PERSONAL_DATA_BIRTH_YEAR = 'BirthYear';

    /**
     * This constant is used when updating an existing Mijn Pathé account and is the name of the form field where you
     * need to enter the name of the street where you live.
     */
    const PERSONAL_DATA_STREET_NAME = 'Address1';

    /**
     * This constant is used when updating an existing Mijn Pathé account and is the name of the form field where you
     * need to enter your house number.
     */
    const PERSONAL_DATA_HOUSE_NUMBER = 'HouseNbr';

    /**
     * This constant is used when updating an existing Mijn Pathé account and is the name of the form field where you
     * need to enter the optional suffix to your house number.
     */
    const PERSONAL_DATA_HOUSE_NUMBER_SUFFIX = 'HouseNbrPostFix';

    /**
     * This constant is used when updating an existing Mijn Pathé account and is the name of the form field where you
     * need to enter your postal code.
     */
    const PERSONAL_DATA_POSTAL_CODE = 'ZIP';

    /**
     * This constant is used when updating an existing Mijn Pathé account and is the name of the form field where you
     * need to enter the city where you live.
     */
    const PERSONAL_DATA_CITY = 'City';

    /**
     * This constant is used when updating an existing Mijn Pathé account and is the name of the form field where you
     * need to enter your mobile phone number.
     */
    const PERSONAL_DATA_MOBILE_PHONE_NUMBER = 'Mobile';

    /**
     * This constant is used when updating an existing Mijn Pathé account and is the name of the form field you can
     * use to change your password.
     */
    const PERSONAL_DATA_PASSWORD = 'LoginPassword';

    /**
     * This constant is used when updating an existing Mijn Pathé account and is the name of the form's checkbox where
     * you indicate whether or not you want to receive the weekly newsletter.
     */
    const PERSONAL_DATA_NEWSLETTER = 'InfoWanted';

    /**
     * This constant is used when requesting the reservation history and is used to limit the number of weeks in the
     * result.
     */
    const RESERVATION_HISTORY_WEEK_COUNT = 'Weeks';

    /**
     * This constant is used when retrieving the usage history for a specific Unlimited or Gold Card from Mijn Pathé,
     * and is the name of the form field that contains the card number.
     */
    const CARD_HISTORY_CARD_NUMBER = 'CustomerCardNumber';

    /**
     * This constant is used when retrieving the usage history for a specific Unlimited or Gold Card from Mijn Pathé,
     * and is the name of the form field that contains the card's PIN code.
     */
    const CARD_HISTORY_PIN_CODE = 'PIN';

    /**
     * This constant is used when retrieving the usage history for a specific Unlimited or Gold Card from Mijn Pathé,
     * and is the name of the form field that contains the month number to filter by.
     */
    const CARD_HISTORY_MONTH = 'Month';

    /**
     * This constant is used when retrieving the usage history for a specific Unlimited or Gold Card from Mijn Pathé,
     * and is the name of the form field that contains the year to filter by.
     */
    const CARD_HISTORY_YEAR = 'Year';

    /**
     * For some requests a User Center Identifier needs to be provided in the URL. This constant refers to the
     * default Pathé user center for those requests.
     */
    const USER_CENTER_PATHE = 1;

    /**
     * For card-related details, a template needs to be provided in the URL. This particular template indicates that
     * we're interested in the card's usage history.
     */
    const TEMPLATE_CARD_HISTORY = '0.cards.history.';

    /**
     * This constant is used to define one of the possible HTTP request methods for this request. This one indicates
     * that a GET request should be submitted.
     */
    const METHOD_GET = 'GET';

    /**
     * This constant is used to define one of the possible HTTP request methods for this request. This one indicates
     * that a POST request should be submitted.
     */
    const METHOD_POST = 'POST';

    /**
     * This property contains the base URL for all requests. All signs are mapped to specific URIs that will be
     * appended to this global base URL.
     *
     * @type string
     */
    protected $baseUrl = 'https://onlinetickets2.pathe.nl/';

    /**
     * This property contains an array that maps signs to URIs.
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
     * This property refers to the sign this request should attempt to fetch.
     *
     * @type int
     */
    protected $sign;

    /**
     * This property contains the request method to use for this request, must be one of the methods defined in the
     * constants.
     *
     * @type string
     */
    protected $requestMethod = self::METHOD_GET;

    /**
     * This property contains an array of optional parameters that should be inserted into the URI for this sign via
     * `sprintf()`.
     *
     * @var array
     */
    protected $urlParameters = array();

    /**
     * This property contains an array of data that should be appended to the URL in its query string.
     *
     * @type mixed[]
     */
    protected $queryParameters = array();

    /**
     * This property contains an array of data that should be posted with the request, if it is a POST request.
     *
     * @type mixed[]
     */
    protected $postParameters = array();

    /**
     * This property is a boolean indicating whether the SSL certificate for the remote server should be validated.
     * Defaults to `true`, I recommend you keep it that way.
     *
     * @type bool
     */
    protected $validateSsl = true;

    /**
     * This property will contain the response to this request after it has been submitted.
     *
     * @type Response
     */
    protected $response;

    /**
     * This property refers to a filename that is used to store cookies across requests.
     *
     * @type string|null
     */
    protected $cookieJar;

    /**
     * The constructor can be used to quickly instantiate a Request with a sign and request method. In some
     * circumstances it may be necessary to disable SSL verification on the response. Usually when your server is not
     * properly configured, but this can also happen if Pathé ever forgets to renew their SSL certificates and is
     * working with old ones. If you ever need to (**which is not recommended!**), you can use the third argument of
     * the constructor to disable SSL verification, or you can configure this via the Client. The constructor accepts
     * three optional arguments:
     *
     * * The sign, which must be null or one of the signs defined in this class (see `getSign()`)
     * * The request method, which must be one of the methods defined in this class (see `getRequestMethod()`), defaults to `Request::METHOD_GET`
     * * A boolean flag which indicates whether or not to validate the Pathé SSL certificate, defaults to `true`
     *
     * <code>
     * $request = new Request(Request::SIGN_LOGIN);
     * $request = new Request(Request::SIGN_UPDATE_PERSONAL_DATA, Request::METHOD_POST, false);
     * </code>
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
     * This method is used to set the sign for this request. It accepts one argument, which must be one of the signs
     * defined in this class, and returns nothing.
     *
     * <code>
     * $request->setSign(Request::SIGN_LOGIN);
     * </code>
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
     * This method is used to set the request method for this request. It accepts one argument, which must be one of
     * the request methods defined in this class (see `getRequestMethod()`) and returns nothing.
     *
     * <code>
     * $request->setRequestMethod(Request::METHOD_GET);
     * </code>
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
     * This method is used to set the cookie jar filename to use for this request. It accepts one argument, which must
     * be a full path to a readable and writable file. This method returns nothing.
     *
     * <code>
     * $request->setCookieJar('/tmp/cookies.txt');
     * </code>
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
     * This method returns the filename for the cookie jar to use, or null if one has not been set. The cookie jar is
     * used to store cookies so they can be re-used in a future request.
     *
     * <code>
     * $cookieJar = $request->getCookieJar();
     * </code>
     *
     * @return string|null
     */
    public function getCookieJar()
    {
        return $this->cookieJar;
    }

    /**
     * This method is used to set data that should be inserted into the URL for the current sign, if it expects
     * parameters. This method expects one argument, which must be a string or integer, and returns nothing.
     *
     * <code>
     * $request->addUrlParameter('value');
     * </code>
     *
     * @param mixed $value
     */
    public function addUrlParameter($value)
    {
        $this->urlParameters[] = $value;
    }

    /**
     * This method is used to set data that should be appended to the URL's query string. The method returns nothing
     * and accepts two arguments:
     *
     * * The parameter that is being set, which must be valid for use as an array index (string or integer)
     * * The value that is being set, which can be anything
     *
     * <code>
     * $request->addQueryParameter('key1', 'value');
     * $request->addQueryParameter('key2', array('value 1', 'value 2'));
     * </code>
     *
     * @param string $parameter
     * @param mixed  $value
     */
    public function addQueryParameter($parameter, $value)
    {
        $this->queryParameters[$parameter] = $value;
    }

    /**
     * This method is used to set data that should be submitted in a POST request. The method returns nothing and
     * accepts two arguments:
     *
     * * The parameter that is being set, which must be valid for use as an array index (string or integer)
     * * The value that is being set, which can be anything
     *
     * <code>
     * $request->addPostParameter('key1', 'value');
     * $request->addPostParameter('key2', array('value 1', 'value 2'));
     * </code>
     *
     * @param string $parameter
     * @param mixed  $value
     */
    public function addPostParameter($parameter, $value)
    {
        $this->postParameters[$parameter] = $value;
    }

    /**
     * This method returns the currently configured request method. It will return one of the `METHOD_*` constants
     * defined in this class.
     *
     * <code>
     * $requestMethod = $request->getRequestMethod();
     * </code>
     *
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }

    /**
     * This method submits this request to Pathé and returns a Response object containing the resulting response.
     *
     * <code>
     * $response = $request->send();
     * </code>
     *
     * @return Response
     */
    public function send()
    {
        return $this->doRequest();
    }

    /**
     * This method actually performs the CURL request to the remote server and retrieves the response. May be merged
     * into `send()` at some point in the future.
     *
     * <code>
     * $response = $this->doRequest();
     * </code>
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
     * This method is used internally to build the full request URL by combining the base URL with the suffix for the
     * current sign, and applying any URL or query parameters.
     *
     * <code>
     * $this->setSign(self::SIGN_LOGIN);
     * $fullUrl = $this->buildRequestUrl();
     * </code>
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
     * This method is used internally to retrieve the base URL.
     *
     * <code>
     * $baseUrl = $this->getBaseUrl();
     * </code>
     *
     * @return string
     */
    protected function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * This method is used internally to retrieve the URI that is mapped to the currently configured sign.
     *
     * <code>
     * $urlSuffix = $this->getUrlSuffix();
     * </code>
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
     * This method returns the currently configured sign (page identifier). It will return one of the `SIGN_*`
     * constants defined in this class.
     *
     * <code>
     * $sign = $request->getSign();
     * </code>
     *
     * @return int
     */
    public function getSign()
    {
        return $this->sign;
    }

    /**
     * This method returns an array of all url parameters that have been added to this request.
     *
     * <code>
     * $urlParameters = $request->getUrlParameters();
     * </code>
     *
     * @return mixed[]
     */
    public function getUrlParameters()
    {
        return $this->urlParameters;
    }

    /**
     * This method returns an array of all query parameters that have been added to this request.
     *
     * <code>
     * $queryParameters = $request->getQueryParameters();
     * </code>
     *
     * @return mixed[]
     */
    public function getQueryParameters()
    {
        return $this->queryParameters;
    }

    /**
     * This method returns an array of all post parameters that have been added to this request.
     *
     * <code>
     * $postParameters = $request->getPostParameters();
     * </code>
     *
     * @return mixed[]
     */
    public function getPostParameters()
    {
        return $this->postParameters;
    }

    /**
     * This method returns the last response, in case this request is submitted multiple times or this instance is
     * reused for several requests. It returns either a Response object or null, if the request has not been submitted
     * yet.
     *
     * <code>
     * $response = $request->getLastResponse();
     * </code>
     *
     * @return Response
     */
    public function getLastResponse()
    {
        return $this->response;
    }

    /**
     * This static method validates whether a given value is one of the defined signs (page identifiers).
     *
     * <code>
     * $isValid = Request::isValidSign(Request::SIGN_LOGIN);
     * </code>
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
     * This static method validates whether a given value is one of the defined request methods.
     *
     * <code>
     * $isValid = Request::isValidRequestMethod(Request::METHOD_GET);
     * </code>
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