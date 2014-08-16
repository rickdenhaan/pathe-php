<?php
namespace Capirussa\Pathe;

/**
 * The HistoryItem object contains information about a movie from the customer's history.
 *
 * @package Capirussa\Pathe
 */
class HistoryItem
{
    /**
     * Contains the date/time at which this event occurred.
     *
     * @type \DateTime
     */
    protected $showTime;

    /**
     * Contains the theater and screen at which this event occurred.
     *
     * @type Screen
     */
    protected $screen;

    /**
     * Contains the Event that occurred.
     *
     * @type Event
     */
    protected $event;

    /**
     * Contains the Reservation details for this history item.
     *
     * @type Reservation
     */
    protected $reservation;

    /**
     * The constructor expects three arguments, all of which are required:
     *
     * * The date and time at which this movie played, as a DateTime object
     * * The theater and viewing screen at which this movie was shown, as a Screen object
     * * The movie that played, as an Event object
     *
     * <code>
     * $showTime = new \DateTime('2014-07-01 12:00:00');
     * $screen = Screen::createFromString('Pathé Delft/Zaal 1');
     * $event = Event::createFromMovieName('The Avengers');
     * $historyItem = new HistoryItem($showTime, $screen, $event);
     * </code>
     *
     * @param \DateTime $showTime
     * @param Screen    $screen
     * @param Event     $event
     */
    public function __construct(\DateTime $showTime, Screen $screen, Event $event)
    {
        $this->setShowTime($showTime);
        $this->setScreen($screen);
        $this->setEvent($event);
    }

    /**
     * This method is used to set the date and time at which the movie was shown. It expects one argument, which must
     * be a DateTime object. It returns nothing.
     *
     * <code>
     * $historyItem->setShowTime(new \DateTime('2014-07-13T20:50:00+02:00'));
     * </code>
     *
     * @param \DateTime $showTime
     */
    public function setShowTime(\DateTime $showTime)
    {
        $this->showTime = $showTime;
    }

    /**
     * This method is used to set the theater and viewing screen at which this movie was shown. It expects one
     * argument, which must be a Screen object. It returns nothing.
     *
     * <code>
     * $historyItem->setScreen(Screen::createFromString('Pathé de Kuip/Zaal 5'));
     * </code>
     *
     * @param Screen $screen
     */
    public function setScreen(Screen $screen)
    {
        $this->screen = $screen;
    }

    /**
     * This method is used to set the movie that was shown. It expects one argument, which must be an Event object. It
     * returns nothing.
     *
     * <code>
     * $historyItem->setEvent(Event::createFromMovieName('The Avengers'));
     * </code>
     *
     * @param Event $event
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;
    }

    /**
     * This method returns the date and time at which the movie played, as a DateTime object.
     *
     * <code>
     * $showTime = $historyItem->getShowTime();
     * </code>
     *
     * @return \DateTime
     */
    public function getShowTime()
    {
        return $this->showTime;
    }

    /**
     * This method returns the theater and viewing screen at which this movie was shown, as a Screen object.
     *
     * <code>
     * $screen = $historyItem->getScreen();
     * </code>
     *
     * @return Screen
     */
    public function getScreen()
    {
        return $this->screen;
    }

    /**
     * This method returns the movie that was shown as an Event object.
     *
     * <code>
     * $event = $historyItem->getEvent();
     * </code>
     *
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * This method is used to set the reservation that matches this history item. It expects one argument, which must
     * be a Reservation object. It returns nothing.
     *
     * <code>
     * $historyItem->setReservation($reservation);
     * </code>
     *
     * @param Reservation $reservation
     */
    public function setReservation(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * This method returns the reservation that matches this history item, if any. Returns either a Reservation object
     * or null.
     *
     * <code>
     * $reservation = $historyItem->getReservation();
     * </code>
     *
     * @return Reservation|null
     */
    public function getReservation()
    {
        return $this->reservation;
    }

    /**
     * This static method expects one parameter: a pipe-delimited plain text file containing a list of movies. It will
     * return an array of HistoryItem objects.
     *
     * <code>
     * $historyItems = HistoryItem::parseHistoryItemsFromDataFile($plainTextFile);
     * </code>
     *
     * @param string $dataFile
     * @return HistoryItem[]
     */
    public static function parseHistoryItemsFromDataFile($dataFile)
    {
        $retValue = array();

        // parse the data from the data file
        $dataFile = str_replace(array("\r\n", "\n\r", "\r", "\n"), "\n", $dataFile);
        $dataRows = explode("\n", $dataFile);
        foreach ($dataRows as $dataRow) {
            if (strrpos($dataRow, '|') > 0) {
                $dataRow = str_replace('&nbsp;', '', $dataRow);
                $dataRow = str_replace(chr(160), '', html_entity_decode($dataRow, ENT_QUOTES));
                $rawData = str_getcsv($dataRow, '|');

                if (count($rawData) > 2) {
                    $rawShowTime = \DateTime::createFromFormat('d-m-Y H:i', trim($rawData[0]));
                    $rawScreen   = trim($rawData[1]);
                    $rawEvent    = trim($rawData[2]);

                    if ($rawShowTime instanceof \DateTime) {
                        $retValue[] = new static(
                            $rawShowTime,
                            Screen::createFromString($rawScreen),
                            Event::createFromMovieName($rawEvent)
                        );
                    }
                }
            }
        }

        return $retValue;
    }

    /**
     * This static method expects one parameter: an HTML document containing the customer's reservation history. It
     * will parse this into an array of HistoryItem objects, containing matching Reservations (if a match could be
     * made).
     *
     * <code>
     * $historyItems = HistoryItem::parseHistoryItemsFromReservationHistory($htmlReservationHistory);
     * </code>
     *
     * @param string $htmlContent
     * @return HistoryItem[]
     */
    public static function parseHistoryItemsFromReservationHistory($htmlContent)
    {
        $retValue = array();
        /* @type $retValue HistoryItem[] */

        // parse the document
        libxml_use_internal_errors(true);

        $dom = new \DOMDocument();
        $dom->loadHTML($htmlContent);

        // get the reservation details table
        $reservationTable = null;
        $tables           = $dom->getElementsByTagName('table');
        for ($table = 0; $table < $tables->length; $table++) {
            $testTable = $tables->item($table);
            /* @type $testTable \DOMElement */

            if (stripos($testTable->getAttribute('class'), 'chooseCardTable') > -1) {
                $reservationTable = $testTable;
            }
        }

        if ($reservationTable !== null) {
            $tableRows = $reservationTable->getElementsByTagName('tr');

            // keep track of which list we're in (there are two in this one table)
            $list = 0;
            $idx  = 0;

            for ($row = 0; $row < $tableRows->length; $row++) {
                $tableRow = $tableRows->item($row);
                /* @type $tableRow \DOMElement */

                // check whether this row contains any headers
                $headers = $tableRow->getElementsByTagName('th');
                if ($headers->length > 0) {
                    $list++;
                    $idx = 0;
                    continue;
                }

                // get the cells in this row
                $cells = $tableRow->getElementsByTagName('td');
                if ($cells->length < 3) {
                    continue;
                }

                switch ($list) {
                    case 1:
                        $showTime = \DateTime::createFromFormat('j-n-Y  H:i', preg_replace('/[^0-9:\-]/', ' ', html_entity_decode($cells->item(0)->textContent)));
                        $screen   = Screen::createFromString($cells->item(1)->textContent);
                        $event    = Event::createFromMovieName($cells->item(2)->textContent);

                        $reservation = new Reservation();
                        $reservation->setTicketCount(preg_replace('/[^0-9]/', '', $cells->item(3)->textContent));

                        $retValue[$idx] = new HistoryItem($showTime, $screen, $event);
                        $retValue[$idx]->setReservation($reservation);
                        break;

                    case 2:
                        if (!isset($retValue[$idx])) {
                            break;
                        }

                        $showTime = \DateTime::createFromFormat('j-n-Y  H:i', preg_replace('/[^0-9:\-]/', ' ', html_entity_decode($cells->item(0)->textContent)));
                        $event    = Event::createFromMovieName($cells->item(2)->textContent);

                        // try to find this reservation in the first list we processed, because not all entries have a matching reservation
                        $reservationFound = ($retValue[$idx]->getShowTime()->format('Y-m-d H:i') == $showTime->format('Y-m-d H:i') && $event->getMovieName() == $retValue[$idx]->getEvent()->getMovieName());
                        $testIdx          = $idx;
                        while ($testIdx < count($retValue) && !$reservationFound) {
                            $testIdx++;
                            if (!isset($retValue[$testIdx])) {
                                break;
                            }
                            $reservationFound = ($retValue[$testIdx]->getShowTime()->format('Y-m-d H:i') == $showTime->format('Y-m-d H:i') && $event->getMovieName() == $retValue[$testIdx]->getEvent()->getMovieName());
                        }

                        if ($reservationFound && $testIdx != $idx) {
                            $idx = $testIdx;
                        } elseif (!$reservationFound) {
                            break;
                        }

                        // the reservation details are in a javascript link in the fourth cell
                        $cell = $cells->item(3);
                        /* @type $cell \DOMElement */

                        $links = $cell->getElementsByTagName('a');
                        $link  = $links->item(0);
                        /* @type $link \DOMElement */

                        $status = trim($link->textContent);

                        $matches = array();
                        preg_match('/GetReservationDetails\(\'([0-9a-z]+)\',[ ]?\'([0-9a-z]+)\',[ ]?\'([0-9a-z]+)\',[ ]?\'[^\']*\'\);/i', $link->getAttribute('href'), $matches);

                        $reservation = $retValue[$idx]->getReservation();
                        $reservation->setStatus($status);
                        $reservation->setShowIdentifier($matches[1]);
                        $reservation->setReservationSetIdentifier($matches[2]);
                        $reservation->setCollectionNumber($matches[3]);
                        $retValue[$idx]->setReservation($reservation);
                        break;
                }

                $idx++;
            }
        }

        return $retValue;
    }

    /**
     * This static method expects one parameter: an HTML document containing an Unlimited or Gold Card usage history.
     * It will parse this into an array of HistoryItem objects, containing Reservations with the collection date/times.
     *
     * <code>
     * $historyItems = HistoryItem::parseHistoryItemsFromCardHistory($htmlCardHistory);
     * </code>
     *
     * @param string $htmlContent
     * @return HistoryItem[]
     */
    public static function parseHistoryItemsFromCardHistory($htmlContent)
    {
        $retValue = array();
        /* @type $retValue HistoryItem[] */

        // parse the document
        libxml_use_internal_errors(true);

        $dom = new \DOMDocument();
        $dom->loadHTML($htmlContent);

        // get the history table
        $historyTable = null;
        $tables       = $dom->getElementsByTagName('table');
        for ($table = 0; $table < $tables->length; $table++) {
            $testTable = $tables->item($table);
            /* @type $testTable \DOMElement */

            if (stripos($testTable->getAttribute('class'), 'chooseCardTable') > -1) {
                $historyTable = $testTable;
                // note: do not break, because we need the LAST table with this class
            }
        }

        if ($historyTable !== null) {
            $tableRows = $historyTable->getElementsByTagName('tr');

            for ($row = 0; $row < $tableRows->length; $row++) {
                $tableRow = $tableRows->item($row);
                /* @type $tableRow \DOMElement */

                // check whether this row contains any headers
                $headers = $tableRow->getElementsByTagName('th');
                if ($headers->length > 0) {
                    continue;
                }

                // get the cells in this row
                $cells = $tableRow->getElementsByTagName('td');
                if ($cells->length != 5) {
                    continue;
                }

                // make sure the first cell contains a valid date
                $collectTime = \DateTime::createFromFormat('j-n-Y  H:i:s', trim(preg_replace('/[^0-9:\-]/', ' ', html_entity_decode($cells->item(0)->textContent))));
                if (!$collectTime) {
                    continue;
                }

                // get the screen for this movie
                $screen = Screen::createFromString(trim($cells->item(1)->textContent));

                // get the movie and showtime
                $showTime = \DateTime::createFromFormat('Y-m-d H:i', trim(preg_replace('/[^0-9:\-]/', ' ', html_entity_decode(substr($cells->item(3)->textContent, -19)))));
                $event    = Event::createFromMovieName(trim(substr($cells->item(3)->textContent, 0, -19)));

                $reservation = new Reservation();
                $reservation->setTicketCount(trim(preg_replace('/[^0-9]/', '', $cells->item(4)->textContent)));
                $reservation->setCollectDateTime($collectTime);

                $historyItem = new HistoryItem($showTime, $screen, $event);
                $historyItem->setReservation($reservation);
                $retValue[] = $historyItem;
            }
        }

        return $retValue;
    }
}