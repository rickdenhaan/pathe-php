<?php
namespace Capirussa\Pathe;

class Screen
{
    /**
     * The name of the theater
     *
     * @type string
     */
    private $theater;

    /**
     * The screen within the theater
     *
     * @type string
     */
    private $screen;

    /**
     * Sets the theater name
     *
     * @param string $theater
     */
    public function setTheater($theater)
    {
        // sanity check, we want to make sure empty strings are interpreted as null
        if (trim($theater) == '') {
            $theater = null;
        }

        $this->theater = $theater;
    }

    /**
     * Sets the screen name
     *
     * @param string $screen
     */
    public function setScreen($screen)
    {
        // sanity check, we want to make sure empty strings are interpreted as null
        if (trim($screen) == '') {
            $screen = null;
        }

        $this->screen = $screen;
    }

    /**
     * Returns the theater name
     *
     * @return string
     */
    public function getTheater()
    {
        return $this->theater;
    }

    /**
     * Returns the screen name
     *
     * @return string
     */
    public function getScreen()
    {
        return $this->screen;
    }

    /**
     * Creates a new Screen entity from the given string and returns it
     *
     * @param string $screenString
     * @return Screen
     */
    public static function createFromString($screenString)
    {
        $retValue = new static();

        $retValue->setTheater(trim(preg_replace('/[Z|z]aal[ ]{1,2}[0-9]{1,2}/s', '', $screenString)));
        $retValue->setScreen(trim(str_replace($retValue->getTheater(), '', $screenString)));

        return $retValue;
    }
}