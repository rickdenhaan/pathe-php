<?php
namespace RickDenHaan\Pathe;

use Buzz\Browser;
use Buzz\Client\Curl;
use Buzz\Listener\CookieListener;
use Buzz\Message\MessageInterface;
use Buzz\Message\Response;
use RickDenHaan\Pathe\Model\Card;
use RickDenHaan\Pathe\Model\Seat;
use RickDenHaan\Pathe\Model\Session;
use RickDenHaan\Pathe\Model\Reservation;
use RickDenHaan\Pathe\Model\Ticket;

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
    private $clientToken = '1f8ecc5806054255903af1f34157bf38';
    // @todo figure out how to generate a valid token!

    /**
     * Unique device token for the current machine
     *
     * @type string
     */
    private $deviceToken;

    /**
     * Contains the details for the current Mijn Pathé user session
     * 
     * @type Model\Session
     */
    private $session = null;

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
     * Authenticates the user with Mijn Pathé to allow us to access pages that require a login
     *
     * @param Browser $browser
     * @return void
     */
    private function login(Browser $browser)
    {
        // build the form data
        $formData = http_build_query(
            array(
                'email'    => $this->getUsername(),
                'password' => $this->getPassword(),
            )
        );

        // submit the form
        $result = $browser->post(
            'https://connect.pathe.nl/v1/sessions',
            $this->getRequestHeaders($formData),
            $formData
        );

        // the response code should be 201 Created
        if ($this->parseResponseCode($result) != 201) {
            throw new \UnexpectedValueException("Could not authenticate with Mijn Pathé");
        }

        $response = json_decode($result->getContent(), true);
        if (!is_array($response)) {
            throw new \UnexpectedValueException("Could not authenticate with Mijn Pathé");
        }

        $this->session = new Session();

        $this->session
             ->setId($response['id'])
             ->setUserId($response['userId'])
             ->setSessionToken($response['sessionToken']);
    }

    /**
     * Logs the user out after accessing pages that require a login
     *
     * @param Browser $browser
     * @return bool
     */
    private function logout(Browser $browser)
    {
        if ($this->session === null) {
            return false;
        }

        // terminate the session
        $result = $browser->delete(
            sprintf(
                'https://connect.pathe.nl/v1/sessions/%s',
                $this->session->getId()
            ),
            $this->getRequestHeaders()
        );

        // the response code should be 204 No Content
        $retValue = ($this->parseResponseCode($result) == 204);

        if ($retValue) {
            $this->session = null;
        }

        return $retValue;
    }

    /**
     * Requests the password reset instructions from Pathé and returns a boolean indicating success
     *
     * <code>
     * $client = new Client('email@example.com', 'dummy');
     * $success = $client->forgotPassword();
     * </code>
     *
     * @return bool
     */
    public function forgotPassword()
    {
        // set up a new browser
        $browser = $this->getBrowser();

        // build the form data
        $formData = http_build_query(
            array(
                'email' => $this->getUsername(),
            )
        );

        // submit the form
        $result = $browser->post(
            'https://connect.pathe.nl/v1/users/resetpassword',
            $this->getRequestHeaders($formData),
            $formData
        );

        $this->response = $result;

        // the response code should be 202 Accepted
        return ($this->parseResponseCode($result) == 202);
    }

    /**
     * Retrieves details of the Unlimited or Unlimited Gold card(s) this user has
     * 
     * @return Model\Card[]
     */
    public function getCards()
    {
        // set up a new browser
        $browser = $this->getBrowser();

        // fetching the cards requires a logged in user
        $this->login($browser);

        // retrieve the list of cards
        $this->response = $browser->get(
            sprintf(
                'https://connect.pathe.nl/v1/users/%d/cards',
                $this->session->getUserId()
            ),
            $this->getRequestHeaders()
        );

        // the response code should be 200 OK
        if ($this->parseResponseCode($this->response) != 200) {
            try {
                $this->logout($browser);
            } catch (\Exception $exception) {
                // ignored, because we're throwing our own exception
            }

            throw new \UnexpectedValueException("Could not retrieve cards from Mijn Pathé");
        }

        $retValue = array();

        $result = json_decode($this->response->getContent(), true);

        if (!is_array($result)) {
            try {
                $this->logout($browser);
            } catch (\Exception $exception) {
                // ignored, because we're throwing our own exception
            }

            throw new \UnexpectedValueException("Could not parse cards from Mijn Pathé");
        }

        foreach ($result['cards'] as $cardData) {
            $card = new Card();
            $card->setId($cardData['id'])
                 ->setType($cardData['cardType'])
                 ->setStatus($cardData['state'])
                 ->setStatusReason($cardData['stateComment'])
                 ->setStartDate(\DateTime::createFromFormat('d-m-Y', $cardData['startDate']))
                 ->setExpiryDate(\DateTime::createFromFormat('d-m-Y', $cardData['expiryDate']));

            // retrieve the PIN code for this card
            $pinResponse = $browser->get(
                sprintf(
                    'https://connect.pathe.nl/v1/users/%d/cards/%s/pin',
                    $this->session->getUserId(),
                    $card->getId()
                ),
                $this->getRequestHeaders()
            );

            // the response code should be 200 OK
            if ($this->parseResponseCode($pinResponse) == 200) {
                $pinResult = json_decode($pinResponse->getContent(), true);
                if (is_array($pinResult)) {
                    $card->setPinCode($pinResult['pin']);
                }
            }

            $retValue[] = $card;
        }

        $this->logout($browser);

        return $retValue;
    }

    /**
     * Retrieves the most recent movie and event reservations (max. 100) for this user
     *
     * @return Model\Reservation[]
     */
    public function getReservations()
    {
        // set up a new browser
        $browser = $this->getBrowser();

        // fetching the reservations requires a logged in user
        $this->login($browser);

        // retrieve the list of reservations
        $this->response = $browser->get(
            sprintf(
                'https://connect.pathe.nl/v1/users/%d/transactions_overview?pageSize=100',
                $this->session->getUserId()
            ),
            $this->getRequestHeaders()
        );

        // the response code should be 200 OK
        if ($this->parseResponseCode($this->response) != 200) {
            try {
                $this->logout($browser);
            } catch (\Exception $exception) {
                // ignored, because we're throwing our own exception
            }

            throw new \UnexpectedValueException("Could not retrieve reservations from Mijn Pathé");
        }

        $retValue = array();

        $result = json_decode($this->response->getContent(), true);

        if (!is_array($result)) {
            try {
                $this->logout($browser);
            } catch (\Exception $exception) {
                // ignored, because we're throwing our own exception
            }

            throw new \UnexpectedValueException("Could not parse reservations from Mijn Pathé");
        }

        foreach ($result['transactions'] as $reservationData) {
            $reservation = new Reservation();
            $reservation->setId($reservationData['id'])
                        ->setDate(\DateTime::createFromFormat(\DateTime::RFC3339, $reservationData['date']))
                        ->setTheater($reservationData['cinemaName'])
                        ->setScreen($reservationData['screenName'])
                        ->setName($reservationData['movieName'] === null ? $reservationData['specialName'] : $reservationData['movieName'])
                        ->setThumbnailFormat($reservationData['thumb'])
                        ->setShowTime(\DateTime::createFromFormat(\DateTime::RFC3339, $reservationData['showTime']))
                        ->setCancelable($reservationData['cancellable'])
                        ->setBarcodeUrl($reservationData['pdfUrl'])
                        ->setPassbookUrl($reservationData['passbookUrl'])
                        ->setCalendarUrl($reservationData['iCalUrl'])
                        ->setStatus($reservationData['state']);

            $retValue[] = $reservation;
        }

        $this->logout($browser);

        return $retValue;
    }

    /**
     * Retrieves the tickets for the provided reservation
     *
     * @param Model\Reservation $reservation
     * @return Model\Ticket[]
     */
    public function getTickets(Reservation $reservation)
    {
        // sanity check: we need a reservation ID
        if (!($reservation instanceof Reservation) || $reservation->getId() === null) {
            throw new \InvalidArgumentException("Invalid reservation, must be have a valid ID");
        }

        // set up a new browser
        $browser = $this->getBrowser();

        // fetching the tickets requires a logged in user
        $this->login($browser);

        // retrieve the list of tickets
        $this->response = $browser->get(
            sprintf(
                'https://connect.pathe.nl/v1/users/%d/transactions/%s',
                $this->session->getUserId(),
                $reservation->getId()
            ),
            $this->getRequestHeaders()
        );

        // the response code should be 200 OK
        if ($this->parseResponseCode($this->response) != 200) {
            try {
                $this->logout($browser);
            } catch (\Exception $exception) {
                // ignored, because we're throwing our own exception
            }

            throw new \UnexpectedValueException("Could not retrieve tickets from Mijn Pathé");
        }

        $retValue = array();

        $result = json_decode($this->response->getContent(), true);

        if (!is_array($result)) {
            try {
                $this->logout($browser);
            } catch (\Exception $exception) {
                // ignored, because we're throwing our own exception
            }

            throw new \UnexpectedValueException("Could not parse tickets from Mijn Pathé");
        }

        foreach ($result['tickets'] as $ticketData) {
            $ticket = new Ticket();
            $ticket->setId($ticketData['id'])
                   ->setOwnerName($ticketData['cardOwner'])
                   ->setType($ticketData['name'])
                   ->setPrice($ticketData['price'] === null ? null : ($ticketData['price'] / 100))
                   ->setCancelable($ticketData['canCancel'])
                   ->setStatus($ticketData['status']);

            foreach ($ticketData['seats'] as $seatData) {
                $seat = new Seat();
                $seat->setRow($seatData['row'])
                     ->setSeat($seatData['name']);

                $ticket->addSeat($seat);
            }

            $retValue[] = $ticket;
        }

        $this->logout($browser);

        return $retValue;
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
     * @param string|array $requestBody (Optional) Defaults to null
     * @return array
     */
    protected function getRequestHeaders($requestBody = null)
    {
        $retValue = array(
            'User-Agent' => 'RickDenHaan-Pathe/2.0 (+http://github.com/rickdenhaan/pathe-php)',
            'X-Client-Token' => $this->getClientToken(),
            'X-Device-Token' => $this->getDeviceToken(),
            //'Accept' => 'application/json',
            //'X-Software-Version' => '2.2.2',
            //'Proxy-Connection' => 'keep-alive',
            //'X-System-Version' => '9.3',
            //'X-Operating-System' => 'iPhone OS',
            //'Accept-Language' => 'nl-NL',
            //'Accept-Encoding' => 'gzip',
            //'Content-Type' => 'application/x-www-form-urlencoded; charset=utf-8',
            //'Content-Length' => '30',
            //'X-Site-Id' => '2',
            //'User-Agent' => 'Pathe/2 (iPhone; iOS 9.3; Scale/2.00)',
            //'Connection' => 'keep-alive',
            //'X-Device-Type' => 'iPhone',
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
            $machineName = gethostname();
            $userName = get_current_user();
            $pathName = realpath(__FILE__);

            $this->clientToken = md5(
                sprintf(
                    'pathe-php.client.%s.%s.%s',
                    $machineName,
                    $userName,
                    $pathName
                )
            );
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
            $userName = get_current_user();

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