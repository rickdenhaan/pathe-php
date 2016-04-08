<?php

namespace RickDenHaan\Pathe\Model;

use RickDenHaan\Pathe\Client;

/**
 * The Reservation model represents a reservation for a movie or event, either past or future
 *
 * @package RickDenHaan\Pathe\Model
 */
class Reservation
{
    /**
     * Contains the reservation id as a string
     *
     * @type string
     */
    private $id;

    /**
     * Contains the date and time the ticket was reserved
     *
     * @type \DateTime
     */
    private $date;

    /**
     * Contains the name of the movie theater
     *
     * @type string
     */
    private $theater;

    /**
     * Contains the name of the screen in this theater
     *
     * @type string
     */
    private $screen;

    /**
     * Contains the name for the movie or event
     *
     * @type string
     */
    private $name;

    /**
     * Contains the URL format for the movie or event's thumbnail. Format contains
     * "[format]" where the desired image size can be entered, e.g. "180x254"
     *
     * @type string
     */
    private $thumbnailFormat;

    /**
     * Contains the date and time the movie or event starts
     *
     * @type \DateTime
     */
    private $showTime;

    /**
     * Indicates whether this reservation can still be cancelled
     *
     * @type bool
     */
    private $cancelable;

    /**
     * Contains the download URL to a PDF file containing the scannable barcode, if any
     *
     * @type string|null
     */
    private $barcodeUrl;

    /**
     * Contains the download URL to a file that can be added to Apple's Passbook/Wallet app, if any
     *
     * @type string|null
     */
    private $passbookUrl;

    /**
     * Contains the download URL to an iCal file that can be added to a calendar, if any
     *
     * @type string|null
     */
    private $calendarUrl;

    /**
     * Contains the reservation's status
     *
     * @type string
     */
    private $status;

    /**
     * Contains all tickets belonging to this reservation
     *
     * @type Ticket[]
     */
    private $tickets;

    /**
     * Sets the card's id
     *
     * @param string|int $id
     * @return $this
     */
    public function setId($id = null)
    {
        if ($id === null) {
            $this->id = null;
        } else {
            if (!is_numeric($id)) {
                throw new \InvalidArgumentException("Invalid id: must be numeric");
            }

            $this->id = trim(strval($id));
        }

        return $this;
    }

    /**
     * Returns the card's id
     *
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the date for the reservation
     *
     * @param \DateTime $date
     * @return $this
     */
    public function setDate(\DateTime $date = null)
    {
        if ($date === null) {
            $this->date = null;
        } else {
            // safety check, make sure we have a copy of $date instead of a reference to a shared object
            $date = clone $date;

            // sanity check, we want the date to be in the system's timezone
            $date->setTimezone(new \DateTimeZone(date_default_timezone_get()));

            $this->date = $date;
        }

        return $this;
    }

    /**
     * Returns the reservation date
     *
     * @return \DateTime|null
     */
    public function getDate()
    {
        if ($this->date === null) {
            return null;
        } else {
            // return a copy of the date instead of a reference
            $clone = clone $this->date;

            return $clone;
        }
    }

    /**
     * Sets the theater name
     *
     * @param string $theater
     * @return $this
     */
    public function setTheater($theater = null)
    {
        if ($theater === null) {
            $this->theater = null;
        } else {
            if (!is_string($theater)) {
                throw new \InvalidArgumentException("Invalid theater: must be a string");
            }

            $this->theater = trim($theater);
        }

        return $this;
    }

    /**
     * Returns the theater name
     *
     * @return string|null
     */
    public function getTheater()
    {
        return $this->theater;
    }

    /**
     * Sets the screen name
     *
     * @param string $screen
     * @return $this
     */
    public function setScreen($screen = null)
    {
        if ($screen === null) {
            $this->screen = null;
        } else {
            if (!is_string($screen)) {
                throw new \InvalidArgumentException("Invalid screen: must be a string");
            }

            $this->screen = trim($screen);
        }

        return $this;
    }

    /**
     * Returns the screen name
     *
     * @return string|null
     */
    public function getScreen()
    {
        return $this->screen;
    }

    /**
     * Sets the movie/event name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name = null)
    {
        if ($name === null) {
            $this->name = null;
        } else {
            if (!is_string($name)) {
                throw new \InvalidArgumentException("Invalid name: must be a string");
            }

            $this->name = trim($name);
        }

        return $this;
    }

    /**
     * Returns the movie/event name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the thumbnail URL format
     *
     * @param string $thumbnailFormat
     * @return $this
     */
    public function setThumbnailFormat($thumbnailFormat = null)
    {
        if ($thumbnailFormat === null) {
            $this->thumbnailFormat = null;
        } else {
            if (!is_string($thumbnailFormat)) {
                throw new \InvalidArgumentException("Invalid thumbnail format: must be a string");
            }

            $this->thumbnailFormat = trim($thumbnailFormat);
        }

        return $this;
    }

    /**
     * Returns the thumbnail URL format
     *
     * @return string|null
     */
    public function getThumbnailFormat()
    {
        return $this->thumbnailFormat;
    }

    /**
     * Returns a thumbnail URL for the given width and height
     *
     * @param int $width  Desired thumbnail width in pixels
     * @param int $height Desired thumbnail height in pixels
     * @return string|null
     */
    public function getThumbnailUrl($width, $height)
    {
        if ($this->thumbnailFormat === null) {
            return null;
        } else {
            $size = sprintf("%dx%d", $width, $height);

            return str_replace("[format]", $size, $this->thumbnailFormat);
        }
    }

    /**
     * Sets the showtime for the movie/event
     *
     * @param \DateTime $showTime
     * @return $this
     */
    public function setShowTime(\DateTime $showTime = null)
    {
        if ($showTime === null) {
            $this->showTime = null;
        } else {
            // safety check, make sure we have a copy of $showTime instead of a reference to a shared object
            $showTime = clone $showTime;

            // sanity check, we want the date to be in the system's timezone
            $showTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));

            $this->showTime = $showTime;
        }

        return $this;
    }

    /**
     * Returns the showtime for the movie/event
     *
     * @return \DateTime|null
     */
    public function getShowTime()
    {
        if ($this->showTime === null) {
            return null;
        } else {
            // return a copy of the showtime instead of a reference
            $clone = clone $this->showTime;

            return $clone;
        }
    }

    /**
     * Sets whether the reservation can be cancelled
     *
     * @param bool $cancelable
     * @return $this
     */
    public function setCancelable($cancelable = null)
    {
        if ($cancelable === null) {
            $this->cancelable = null;
        } else {
            if (!is_bool($cancelable)) {
                throw new \InvalidArgumentException("Invalid cancelable: must be a boolean");
            }

            $this->cancelable = $cancelable;
        }

        return $this;
    }

    /**
     * Returns whether the reservation can be cancelled
     *
     * @return bool|null
     */
    public function getCancelable()
    {
        return $this->cancelable;
    }

    /**
     * Sets the barcode URL
     *
     * @param string $barcodeUrl
     * @return $this
     */
    public function setBarcodeUrl($barcodeUrl = null)
    {
        if ($barcodeUrl === null) {
            $this->barcodeUrl = null;
        } else {
            if (!is_string($barcodeUrl)) {
                throw new \InvalidArgumentException("Invalid barcode URL: must be a string");
            }

            $this->barcodeUrl = trim($barcodeUrl);
        }

        return $this;
    }

    /**
     * Returns the barcode URL
     *
     * @return string|null
     */
    public function getBarcodeUrl()
    {
        return $this->barcodeUrl;
    }

    /**
     * Sets the Apple Passbook/Wallet URL
     *
     * @param string $passbookUrl
     * @return $this
     */
    public function setPassbookUrl($passbookUrl = null)
    {
        if ($passbookUrl === null) {
            $this->passbookUrl = null;
        } else {
            if (!is_string($passbookUrl)) {
                throw new \InvalidArgumentException("Invalid Passbook URL: must be a string");
            }

            $this->passbookUrl = trim($passbookUrl);
        }

        return $this;
    }

    /**
     * Returns the Apple Passbook/Wallet URL
     *
     * @return string|null
     */
    public function getPassbookUrl()
    {
        return $this->passbookUrl;
    }

    /**
     * Sets the iCal URL
     *
     * @param string $calendarUrl
     * @return $this
     */
    public function setCalendarUrl($calendarUrl = null)
    {
        if ($calendarUrl === null) {
            $this->calendarUrl = null;
        } else {
            if (!is_string($calendarUrl)) {
                throw new \InvalidArgumentException("Invalid iCal URL: must be a string");
            }

            $this->calendarUrl = trim($calendarUrl);
        }

        return $this;
    }

    /**
     * Returns the iCal URL
     *
     * @return string|null
     */
    public function getCalendarUrl()
    {
        return $this->calendarUrl;
    }

    /**
     * Sets the reservation's status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status = null)
    {
        if ($status === null) {
            $this->status = null;
        } else {
            if (!is_string($status)) {
                throw new \InvalidArgumentException("Invalid status: must be a string");
            }

            $this->status = trim($status);
        }

        return $this;
    }

    /**
     * Returns the reservation's status
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Retrieves all reservations for the current user in the given year, or the
     * current year if no year was given
     *
     * @param int     $year    (Optional) Defaults to null
     * @param Session $session (Optional) Defaults to null
     * @return static[]
     * @throws \Exception
     */
    public static function getAll($year = null, Session $session = null)
    {
        if ($year === null) {
            $year = date('Y');
        }

        $retValue = array();

        $ownSession = false;
        if ($session === null) {
            $session    = Session::create();
            $ownSession = true;
        }

        try {
            $result = Client::getInstance()
                            ->get(
                                sprintf(
                                    "users/%d/transactions_overview?year=%s",
                                    $session->getUserId(),
                                    $year
                                ),
                                200,
                                $session
                            );
        } catch (\Exception $ex) {
            if ($ownSession) {
                $session->close();
            }

            throw $ex;
        }

        foreach ($result['transactions'] as $reservationData) {
            $reservation = new static();
            $reservation->setId($reservationData['id']);
            $reservation->setDate(\DateTime::createFromFormat(\DateTime::RFC3339, $reservationData['date']));
            $reservation->setTheater($reservationData['cinemaName']);
            $reservation->setScreen($reservationData['screenName']);
            $reservation->setName($reservationData['movieName'] === null ? $reservationData['specialName'] : $reservationData['movieName']);
            $reservation->setThumbnailFormat($reservationData['thumb']);
            $reservation->setShowTime(\DateTime::createFromFormat(\DateTime::RFC3339, $reservationData['showTime']));
            $reservation->setCancelable($reservationData['cancellable']);
            $reservation->setBarcodeUrl($reservationData['pdfUrl']);
            $reservation->setPassbookUrl($reservationData['passbookUrl']);
            $reservation->setCalendarUrl($reservationData['iCalUrl']);
            $reservation->setStatus($reservationData['state']);

            $retValue[] = $reservation;
        }

        if ($ownSession) {
            $session->close();
        }

        return $retValue;
    }

    /**
     * Gets all Tickets for this reservation
     *
     * @param Session $session (Optional) Defaults to null
     * @return Ticket[]
     */
    public function getTickets(Session $session = null)
    {
        if ($this->tickets === null) {
            $this->tickets = Ticket::getAll($this, $session);
        }

        return $this->tickets;
    }
}