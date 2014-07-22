<?php
namespace Capirussa\Pathe;

class Event
{
    /**
     * Name of the movie
     *
     * @type string
     */
    private $movieName;

    /**
     * Sets the movie name
     *
     * @param string $movieName
     */
    public function setMovieName($movieName)
    {
        // sanity check, we want to make sure empty strings are interpreted as null
        if (trim($movieName) == '') {
            $movieName = null;
        }

        $this->movieName = $movieName;
    }

    /**
     * Returns the movie name
     *
     * @return string
     */
    public function getMovieName()
    {
        return $this->movieName;
    }

    /**
     * Creates a new Event entity from the given movie name and returns it
     *
     * @param string $movieNameString
     * @return Event
     */
    public static function createFromMovieName($movieNameString)
    {
        $retValue = new static();

        $retValue->setMovieName(trim($movieNameString));

        return $retValue;
    }
}