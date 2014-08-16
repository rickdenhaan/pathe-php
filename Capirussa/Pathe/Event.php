<?php
namespace Capirussa\Pathe;

/**
 * The Event object contains information about a specific event that took place. At the moment, that means it contains
 * the name of a movie, but in the future this could also be other events (such as live streaming events or musicals).
 *
 * @package Capirussa\Pathe
 */
class Event
{
    /**
     * Contains the name of the movie.
     *
     * @type string
     */
    private $movieName;

    /**
     * This method is used to set the name of the movie. It expects one argument, which must be a non-empty string. It
     * returns nothing.
     *
     * <code>
     * $event = new Event();
     * $event->setMovieName('The Avengers');
     * </code>
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
     * This method is used to retrieve the name of the movie from this Event. It returns a string, or null if the
     * movie name is not set.
     *
     * <code>
     * $event = Event::createFromMovieName('The Avengers');
     * $movieName = $event->getMovieName();
     * </code>
     *
     * @return string
     */
    public function getMovieName()
    {
        return $this->movieName;
    }

    /**
     * This static method expects one parameter: a string containing the name of a movie. It will return a new Event
     * object.
     *
     * <code>
     * $event = Event::createFromMovieName('The Avengers');
     * </code>
     *
     * @param string $movieNameString
     * @return Event
     */
    public static function createFromMovieName($movieNameString)
    {
        $retValue = new static();
        /* @type $retValue Event */

        $retValue->setMovieName(trim($movieNameString));

        return $retValue;
    }
}