<?php
namespace Capirussa\Pathe;

class HistoryItem
{
    /**
     * Date/time at which this history item occurred
     *
     * @type \DateTime
     */
    protected $showTime;

    /**
     * Theater and screen at which this history item occurred
     *
     * @type Screen
     */
    protected $screen;

    /**
     * The event that occurred
     *
     * @type Event
     */
    protected $event;

    /**
     * The reservation details for this history item
     *
     * @type Reservation
     */
    protected $reservation;

    /**
     * Constructs a new HistoryItem
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
     * Sets the show time
     *
     * @param \DateTime $showTime
     */
    public function setShowTime(\DateTime $showTime)
    {
        $this->showTime = $showTime;
    }

    /**
     * Sets the screen
     *
     * @param Screen $screen
     */
    public function setScreen(Screen $screen)
    {
        $this->screen = $screen;
    }

    /**
     * Sets the event
     *
     * @param Event $event
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Returns the show time for this history item
     *
     * @return \DateTime
     */
    public function getShowTime()
    {
        return $this->showTime;
    }

    /**
     * Returns the screen at which this history item took place
     *
     * @return Screen
     */
    public function getScreen()
    {
        return $this->screen;
    }

    /**
     * Returns the event that took place
     *
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Sets the reservation details
     *
     * @param Reservation $reservation
     */
    public function setReservation(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Returns the reservation details for this history item
     *
     * @return Reservation|null
     */
    public function getReservation()
    {
        return $this->reservation;
    }

    /**
     * Parses a pipe-delimited data file into an array of history items
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
     * Parses the HTML response from the reservation history
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
     * Parses the HTML response from the card history
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