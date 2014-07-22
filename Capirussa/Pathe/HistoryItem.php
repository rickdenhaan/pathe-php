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

                if (count($rawData) >= 2) {
                    $rawShowTime = trim($rawData[0]);
                    $rawScreen   = trim($rawData[1]);
                    $rawEvent    = trim($rawData[2]);

                    $retValue[] = new static(
                        \DateTime::createFromFormat('d-m-Y H:i', $rawShowTime),
                        Screen::createFromString($rawScreen),
                        Event::createFromMovieName($rawEvent)
                    );
                }
            }
        }

        return $retValue;
    }
}