<?php
namespace Capirussa\Pathe;

/**
 * The Client is the main hub of this library. It contains methods to perform all actions currently supported.
 *
 * @package Capirussa\Pathe
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
     * Contains the customer ID that is needed to retrieve the customer history. Will be automatically set upon login,
     * but can also be set manually.
     *
     * @type int
     */
    protected $customerId;

    /**
     * A boolean indicator which can be set to false so that the Pathé SSL certificate is not verified. **Not
     * recommended.**
     *
     * @type bool
     */
    private $validateSsl = true;

    /**
     * Contains the full path to the cookie jar filename which will be passed between requests.
     *
     * @type string|null
     */
    protected $cookieJar = null;

    /**
     * Contains the Response object for the last submitted request.
     *
     * @type Response
     */
    protected $response;

    /**
     * The constructor requires two arguments: your Mijn Pathé username and your password. Both arguments must be
     * strings.
     *
     * <code>
     * $client = new Client('user@example.com', 'p4s5w0rd');
     * </code>
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
    }

    /**
     * This method is used to set the customer ID. It accepts one argument, which must be a numeric string. It returns
     * nothing.
     *
     * <code>
     * $client = new Client('user@example.com', 'p4s5w0rd');
     * $client->setCustomerId('12345678');
     * </code>
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
     * This method returns your Pathé customer id, but only after at least one request (other than forgotPassword) has
     * been submitted or after `setCustomerId()` has been called. It accepts no arguments and returns either an
     * integer or null.
     *
     * <code>
     * $client = new Client('user@example.com', 'p4s5w0rd');
     * $customerId = $client->getCustomerId();
     * </code>
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->customerId;
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
     * This method returns the full path to the current cookie jar. If no jar is set, a new cookie jar is created and
     * its filename is returned.
     *
     * <code>
     * $client = new Client('user@example.com', 'p4s5w0rd');
     * $cookieJar = $client->getCookieJar();
     * </code>
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
     * This method is called internally after all processing for an action is complete, to make sure the following
     * action starts with a new user session. It deletes the cookie jar so a new one is created for the next request.
     *
     * <code>
     * $this->clearCookieJar();
     * </code>
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
     * This method is called internally for most actions to authenticate the user with Mijn Pathé.
     *
     * <code>
     * $this->authenticate();
     * </code>
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
        $response = $request->send();

        // make sure the username and password were correct
        if (stripos($response->getRawBody(), 'Uw gebruikersnaam en/of wachtwoord is onjuist.') > 0) {
            throw new \LogicException(
                sprintf(
                    '%1$s: Unable to login to the Path&ecaute; Client Panel',
                    __METHOD__
                )
            );
        }

        // now perform another GET request on the dashboard, to make sure we were logged in
        $request = $this->getRequest(Request::SIGN_PERSONAL_DATA, Request::METHOD_GET);
        $request->setCookieJar($this->getCookieJar());
        $this->response = $request->send();

        // the response should have several links that contain the user's customer ID, find one
        $matches = array();
        preg_match('/<a.*href=\".*CustomerID=([0-9]+)\"/i', $this->response->getRawBody(), $matches);
        if (isset($matches[1])) {
            $this->setCustomerId($matches[1]);
        }
    }

    /**
     * This internal method destroys the current Mijn Pathé session and clears the cookie jar.
     *
     * <code>
     * $this->logout();
     * </code>
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
     * This method retrieves the last 100 movies you watched from Mijn Pathé and returns them as an array of
     * HistoryItem objects. It accepts no arguments.
     *
     * <code>
     * $client = new Client('user@example.com', 'p4s5w0rd');
     * $historyItems = $client->getCustomerHistory();
     * </code>
     *
     * @throws \LogicException
     * @return HistoryItem[]
     */
    public function getCustomerHistory()
    {
        // log into the client panel
        $this->authenticate();

        // get the customer history page
        $request = $this->getRequest(Request::SIGN_CARD_RELATED_DATA, Request::METHOD_GET);
        $request->setCookieJar($this->getCookieJar());
        $request->addQueryParameter(Request::QUERY_USER_CENTER_ID, Request::USER_CENTER_PATHE);
        $request->addQueryParameter(Request::QUERY_TEMPLATE, Request::TEMPLATE_CARD_HISTORY);
        $request->addQueryParameter(Request::QUERY_CUSTOMER_ID, $this->getCustomerId());
        $request->addQueryParameter(Request::QUERY_LOAD_CARDS, false);
        $response = $request->send();

        // on this page, JavaScript calls a SearchCustomerHistory method with a random hash, we need that hash
        $dataFile = null;
        $matches  = array();
        preg_match('/SearchCustomerHistory\(.*\'(.*)\'\);/i', $response->getRawBody(), $matches);
        if (isset($matches[1])) {
            $dataFile = $matches[1];
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
        $this->response = $request->send();

        // parse the text file into an array of history items
        $retValue = HistoryItem::parseHistoryItemsFromDataFile($this->response->getRawBody());

        // to be nice to Pathé and not flood their servers with session files, log the user out properly
        $this->logout();

        return $retValue;
    }

    /**
     * This method retrieves your personal information from Mijn Pathé and returns it as a PersonalData object. It
     * accepts no arguments.
     *
     * <code>
     * $client = new Client('user@example.com', 'p4s5w0rd');
     * $personalData = $client->getPersonalData();
     * </code>
     *
     * @return PersonalData
     */
    public function getPersonalData()
    {
        $this->authenticate();

        $retValue = PersonalData::parsePersonalDataFromHtmlForm($this->getLastResponse()->getRawBody());
        $retValue->setUsername($this->getUsername());

        $this->logout();

        return $retValue;
    }

    /**
     * This method can be used to update your personal details. It accepts one argument, which must be a PersonalData
     * object, and returns a PersonalData object containing your customer details after the changes have been
     * processed.
     *
     * <code>
     * $client = new Client('user@example.com', 'p4s5w0rd');
     * $personalData = $client->getPersonalData()
     * $personalData->setPassword('newPassword123');
     * $newData = $client->updatePersonalData($personalData);
     * </code>
     *
     * @param PersonalData $personalData
     * @return PersonalData
     */
    public function updatePersonalData(PersonalData $personalData)
    {
        $this->authenticate();

        // validate the required fields
        $personalData->assertValidForRegistrationAndUpdate();

        // build the request to update the personal data
        $request = $this->getRequest(Request::SIGN_UPDATE_PERSONAL_DATA, Request::METHOD_POST);
        $request->setCookieJar($this->getCookieJar());
        $request->addPostParameter(Request::PERSONAL_DATA_GENDER, $personalData->getGender());
        $request->addPostParameter(Request::PERSONAL_DATA_FIRST_NAME, $personalData->getFirstName());
        $request->addPostParameter(Request::PERSONAL_DATA_MIDDLE_NAME, $personalData->getMiddleName());
        $request->addPostParameter(Request::PERSONAL_DATA_LAST_NAME, $personalData->getLastName());
        $request->addPostParameter(Request::PERSONAL_DATA_EMAIL_ADDRESS, $personalData->getEmailAddress());
        $request->addPostParameter(Request::PERSONAL_DATA_COUNTRY, $personalData->getCountry());
        $request->addPostParameter(Request::PERSONAL_DATA_BIRTH_DAY, $personalData->getBirthDate()->format('j'));
        $request->addPostParameter(Request::PERSONAL_DATA_BIRTH_MONTH, $personalData->getBirthDate()->format('n'));
        $request->addPostParameter(Request::PERSONAL_DATA_BIRTH_YEAR, $personalData->getBirthDate()->format('Y'));
        $request->addPostParameter(Request::PERSONAL_DATA_STREET_NAME, $personalData->getStreetName());
        $request->addPostParameter(Request::PERSONAL_DATA_HOUSE_NUMBER, $personalData->getHouseNumber());
        $request->addPostParameter(Request::PERSONAL_DATA_HOUSE_NUMBER_SUFFIX, $personalData->getHouseNumberSuffix());
        $request->addPostParameter(Request::PERSONAL_DATA_POSTAL_CODE, $personalData->getPostalCode());
        $request->addPostParameter(Request::PERSONAL_DATA_CITY, $personalData->getCity());
        $request->addPostParameter(Request::PERSONAL_DATA_MOBILE_PHONE_NUMBER, $personalData->getMobilePhoneNumber());
        $request->addPostParameter(Request::PERSONAL_DATA_PASSWORD, $personalData->getPassword());

        if ($personalData->getNewsletter()) {
            $request->addPostParameter(Request::PERSONAL_DATA_NEWSLETTER, 'on');
        }

        $this->response = $request->send();

        $this->logout();

        $retValue = PersonalData::parsePersonalDataFromHtmlForm($this->response->getRawBody());
        $retValue->setUsername($this->getUsername());

        return $retValue;
    }

    /**
     * You can use this method if you've forgotten your password. Initialize the client by using an incorrect password
     * (it is ignored for the forgotPassword method). If you supply a valid email address, Pathé will send you a link
     * you can use to reset your password. This link will remain valid for 3 hours. This method expects one argument,
     * which must be your email address as registered with Mijn Pathé. It returns a boolean `true` or `false`
     * indicating whether the request was successful.
     *
     * <code>
     * $client = new Client('user@example.com', 'notMyPassword123');
     * $client->forgotPassword('email@example.com');
     * </code>
     *
     * @param string $email
     * @throws \InvalidArgumentException
     * @return bool
     */
    public function forgotPassword($email)
    {
        // validate that the email address is in fact an email address
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid email address',
                    __METHOD__
                )
            );
        }

        // first perform a GET request on the dashboard to initialize a remote session
        $request = $this->getRequest(Request::SIGN_PERSONAL_DATA, Request::METHOD_GET);
        $request->setCookieJar($this->getCookieJar());
        $request->addQueryParameter(Request::QUERY_USER_CENTER_ID, Request::USER_CENTER_PATHE);
        $request->send();

        // now build a POST request to request the password reminder
        $request = $this->getRequest(Request::SIGN_LOGIN, Request::METHOD_POST);
        $request->setCookieJar($this->getCookieJar());
        $request->addPostParameter(Request::LOGIN_EMAIL_ADDRESS, $email);
        $this->response = $request->send();

        $this->clearCookieJar();

        // check whether the request was successful
        $retValue = false;
        if (preg_match('/Een e\-mail met uw wachtwoord(.*)<br>' . $email . '/i', $this->response->getRawBody())) {
            $retValue = true;
        }

        return $retValue;
    }

    /**
     * This method is used to register a new account with Mijn Pathé. It expects one argument, which must be a
     * (filled) PersonalData object. It returns true or false, depending on whether registration succeeded. This
     * method is called on the Client, but the username and password supplied when instantiating the Client are not
     * used. You can make up anything you want when instantiating, as long as the username and password match their
     * expected syntax (an email address for the username, and a password of at least 6 characters containing at least
     * one number).
     *
     * <code>
     * $client = new Client('nobody@example.com', 'n0body123');
     * $personalData = new PersonalData();
     * $personalData->setUsername('user@example.com');
     * $personalData->setPassword('myP4ssword');
     * $personalData->setEmailAddress('pathe@example.com'); // can be different from your username
     * $success = $client->registerAccount($personalData);
     * </code>
     *
     * Note that quite a few fields are required when registering an account, because this library will automatically
     * submit an `updatePersonalData()` request to fill in the fields that aren't available in the registration form.
     * The required fields are:
     *
     * * Your first name
     * * Your last name
     * * Your email address
     * * Your birth date
     * * Your username
     * * Your password
     * * Your street name and house number
     * * Your postal code and city
     *
     * @param PersonalData $personalData with which to register
     * @throws \InvalidArgumentException
     * @return bool
     */
    public function registerAccount(PersonalData $personalData)
    {
        // sanity check, some information is required
        $personalData->assertValidForRegistrationAndUpdate(true);

        // first perform a GET request on the dashboard to initialize a remote session
        $request = $this->getRequest(Request::SIGN_PERSONAL_DATA, Request::METHOD_GET);
        $request->setCookieJar($this->getCookieJar());
        $request->addQueryParameter(Request::QUERY_USER_CENTER_ID, Request::USER_CENTER_PATHE);
        $request->send();

        // now build a POST request to request the password reminder
        $request = $this->getRequest(Request::SIGN_LOGIN, Request::METHOD_POST);
        $request->setCookieJar($this->getCookieJar());
        $request->addPostParameter(Request::PERSONAL_DATA_FIRST_NAME, $personalData->getFirstName());
        $request->addPostParameter(Request::PERSONAL_DATA_MIDDLE_NAME, $personalData->getMiddleName());
        $request->addPostParameter(Request::PERSONAL_DATA_LAST_NAME, $personalData->getLastName());
        $request->addPostParameter(Request::LOGIN_EMAIL_ADDRESS, $personalData->getUsername());
        $request->addPostParameter(Request::LOGIN_CONFIRM_EMAIL_ADDRESS, $personalData->getUsername());
        $request->addPostParameter(Request::PERSONAL_DATA_GENDER, $personalData->getGender());
        $request->addPostParameter(Request::PERSONAL_DATA_BIRTH_DAY, $personalData->getBirthDate()->format('j'));
        $request->addPostParameter(Request::PERSONAL_DATA_BIRTH_MONTH, $personalData->getBirthDate()->format('n'));
        $request->addPostParameter(Request::PERSONAL_DATA_BIRTH_YEAR, $personalData->getBirthDate()->format('Y'));
        $request->addPostParameter(Request::LOGIN_PASSWORD, $personalData->getPassword());
        $request->addPostParameter(Request::LOGIN_CONFIRM_PASSWORD, $personalData->getPassword());

        if ($personalData->getNewsletter()) {
            $request->addPostParameter(Request::LOGIN_NEWSLETTER, 'on');
        }

        $this->response = $request->send();

        $this->clearCookieJar();

        // check whether the request was successful
        $retValue = false;
        if (preg_match('/Uw gegevens zijn succesvol geregistreerd/i', $this->response->getRawBody())) {
            $retValue = true;
        }

        // if the registration was successful, immediately update the user details because during registration some
        // fields were not submitted (like the address and possibly the differing email address)
        if ($retValue === true) {
            $oldUsername = $this->getUsername();
            $oldPassword = $this->getPassword();

            $this->setUsername($personalData->getUsername());
            $this->setPassword($personalData->getPassword());

            $this->updatePersonalData($personalData);

            $this->setUsername($oldUsername);
            $this->setPassword($oldPassword);
        }

        return $retValue;
    }

    /**
     * This method allows you to delete your Mijn Pathé account. Note that for this to work you must not have an
     * active Unlimited or Gold card linked to your account. This method accepts no arguments and returns a boolean
     * indicating whether or not your account was successfully deleted.
     *
     * <code>
     * $client = new Client('user@example.com', 'p4s5w0rd');
     * $client->deleteAccount();
     * </code>
     *
     * @return bool
     */
    public function deleteAccount()
    {
        $retValue = true;

        $this->authenticate();

        // build the request to access the delete page
        $request = $this->getRequest(Request::SIGN_DELETE_ACCOUNT, Request::METHOD_GET);
        $request->setCookieJar($this->getCookieJar());
        $request->addQueryParameter(Request::QUERY_USER_CENTER_ID, Request::USER_CENTER_PATHE);
        $this->response = $request->send();

        // if the user has an Unlimited or Gold card linked to it, it cannot be deleted:
        if (stripos($this->response->getRawBody(), 'UserNotDeleteConnectedCard') > 0) {
            $retValue = false;
        }

        // if all is still well, actually delete the account
        if ($retValue) {
            // build the request to actually delete the account
            $request = $this->getRequest(Request::SIGN_DELETE_ACCOUNT, Request::METHOD_POST);
            $request->setCookieJar($this->getCookieJar());
            $request->addPostParameter(Request::DELETE_PASSWORD, $this->getPassword());
            $request->addPostParameter(Request::DELETE_CONFIRM, 'on');
            $this->response = $request->send();

            $retValue = (stripos($this->response->getRawBody(), 'Voor het gebruik van Mijn Path&eacute; dient u hieronder in te loggen.') > 0);
        }

        // if deleting the account was successful, we were automatically logged out. Otherwise, log out manually.
        if (!$retValue) {
            $this->logout();
        }

        return $retValue;
    }

    /**
     * This method retrieves a history of the movies you made reservations for, with their reservations if they could
     * be found. The result will be returned as an array of HistoryItem objects. This method accepts one optional
     * argument, which is the number of weeks (in the past) for which the reservations should be looked up.
     *
     * <code>
     * $client = new Client('user@example.com', 'p4s5w0rd');
     * $historyItems = $client->getReservationHistory(3);
     * $historyItems = $client->getReservationHistory(52);
     * </code>
     *
     * Valid values are 3, 4, 9, 13, 26, 52, 105 and 150 weeks. If you do not give a value, the default of 3 weeks is
     * assumed.
     *
     * @param int $weekCount (Optional) Defaults to 3, possible values: 3, 4, 9, 13, 26, 52, 105, 150
     * @throws \InvalidArgumentException
     * @return HistoryItem[]
     */
    public function getReservationHistory($weekCount = 3)
    {
        // sanity check
        if (!in_array($weekCount, array(3, 4, 9, 13, 26, 52, 105, 150))) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid number of weeks, must be one of 3, 4, 9, 13, 26, 52, 105 or 150',
                    __METHOD__
                )
            );
        }

        $this->authenticate();

        // build the request to retrieve the reservation history
        $request = $this->getRequest(Request::SIGN_RESERVATION_HISTORY, Request::METHOD_POST);
        $request->setCookieJar($this->getCookieJar());
        $request->addPostParameter(Request::RESERVATION_HISTORY_WEEK_COUNT, $weekCount);
        $this->response = $request->send();

        $retValue = HistoryItem::parseHistoryItemsFromReservationHistory($this->response->getRawBody());

        $this->logout();

        return $retValue;
    }

    /**
     * This method retrieves a list of movies visited using a given Pathé Unlimited or Gold Card. It accepts four
     * arguments, two of which are required:
     *
     * * Unlimited/Gold card number
     * * Card PIN code
     * * Month for which to get movies (optional)
     * * Year for which to get movies (optional)
     *
     * If you provide a month and year, it will only retrieve the movies visited in the given month. If you do not,
     * it will fetch all movies since day one of your Unlimited/Gold Card subscription. This method returns an array
     * of HistoryItem objects.
     *
     * <code>
     * $client = new Client('user@example.com', 'p4s5w0rd');
     * $historyItems = $client->getCardHistory('cardNumber', 'pinCode');
     * $historyItems = $client->getCardHistory('cardNumber', 'pinCode', 7, 2014);
     * </code>
     *
     * @param string $cardNumber
     * @param string $pinCode
     * @param int    $month (Optional) Defaults to retrieve all months
     * @param int    $year  (Optional) Defaults to retrieve all years
     * @throws \InvalidArgumentException
     * @return HistoryItem[]
     */
    public function getCardHistory($cardNumber, $pinCode, $month = null, $year = null)
    {
        // validate the card number
        if (strlen($cardNumber) != 16 || preg_match('/[^0-9]/', $cardNumber)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid card number, must be a 16-digit code',
                    __METHOD__
                )
            );
        }

        // validate the pin code
        if (strlen($pinCode) != 4 || preg_match('/[^0-9]/', $pinCode)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid PIN code, must a 4-digit code',
                    __METHOD__
                )
            );
        }

        // validate the month
        if ($month !== null) {
            $month = intval($month, 10);

            if ($month < 1 || $month > 12) {
                throw new \InvalidArgumentException(
                    sprintf(
                        '%1$s: Invalid month, must be a number between 1 and 12',
                        __METHOD__
                    )
                );
            }
        } else {
            $month = 9999;
        }

        // validate the year
        if ($year !== null) {
            $year = intval($year, 10);

            if ($year < 2006 || $year > (date('Y') + 1)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        '%1$s: Invalid year, must be between 2006 and next year',
                        __METHOD__
                    )
                );
            }
        } else {
            $year = 9999;
        }

        $this->clearCookieJar();

        // build the request to access the card history form and start a session
        $request = $this->getRequest(Request::SIGN_CARD_HISTORY, Request::METHOD_GET);
        $request->setCookieJar($this->getCookieJar());
        $request->addQueryParameter(Request::QUERY_USER_CENTER_ID, Request::USER_CENTER_PATHE);
        $request->send();

        // submit the card number form now that we have a session
        $request = $this->getRequest(Request::SIGN_CARD_HISTORY, Request::METHOD_POST);
        $request->setCookieJar($this->getCookieJar());
        $request->addQueryParameter(Request::QUERY_USER_CENTER_ID, Request::USER_CENTER_PATHE);
        $request->addPostParameter(Request::CARD_HISTORY_CARD_NUMBER, $cardNumber);
        $request->addPostParameter(Request::CARD_HISTORY_PIN_CODE, $pinCode);
        $request->addPostParameter(Request::CARD_HISTORY_MONTH, $month);
        $request->addPostParameter(Request::CARD_HISTORY_YEAR, $year);
        $this->response = $request->send();

        $this->clearCookieJar();

        // handle validation errors
        if (stripos($this->response->getRawBody(), 'Deze klantenkaart is niet gevonden.') > 0) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Unknown card number "%2$s" or incorrect PIN code "%3$s"',
                    __METHOD__,
                    $cardNumber,
                    $pinCode
                )
            );
        }

        return HistoryItem::parseHistoryItemsFromCardHistory($this->response->getRawBody());
    }

    /**
     * This method is used internally to retrieve a Request object. It accepts two parameters, the sign and request
     * method to use. When building the request, it also passes on whether or not to validate the remote SSL
     * certificate.
     *
     * <code>
     * $request = $this->getRequest(Request::SIGN_LOGOUT, Request::METHOD_GET);
     * </code>
     *
     * @param int    $sign          (Optional) Defaults to null
     * @param string $requestMethod (Optional) Defaults to Request::METHOD_GET
     * @return Request
     *
     * Unittests overwrite this method to retrieve a mock request, so
     * @codeCoverageIgnore
     */
    protected function getRequest($sign = null, $requestMethod = Request::METHOD_GET)
    {
        return new Request($sign, $requestMethod, $this->validateSsl);
    }

    /**
     * This method returns the Response object returned for the last request, in case the method itself returned
     * something else. For example, you can use this method after calling `getCustomerHistory()` (which returns an
     * array of HistoryItem objects) to retrieve the Response object. It accepts no arguments.
     *
     * <code>
     * $client = new Client('user@example.com', 'p4s5w0rd');
     * $historyItems = $client->getCustomerHistory();
     * $response = $client->getLastResponse();
     * </code>
     *
     * @return Response|null
     */
    public function getLastResponse()
    {
        return $this->response;
    }
}