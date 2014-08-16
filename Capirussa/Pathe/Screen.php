<?php
namespace Capirussa\Pathe;

/**
 * The Screen object contains information about a particular theater and viewing screen.
 *
 * @package Capirussa\Pathe
 */
class Screen
{
    /**
     * This property contains the name of the theater this viewing screen is in.
     *
     * @type string
     */
    private $theater;

    /**
     * This property contains the name of the viewing screen.
     *
     * @type string
     */
    private $screen;

    /**
     * This method is used to set the name of the theater. It accepts one argument, which must be a non-empty string,
     * and returns nothing.
     *
     * <code>
     * $screen->setTheater('Pathé Arena');
     * </code>
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
     * This method is used to set the name of the viewing screen. It accepts one argument, which must be a non-empty
     * string, and returns nothing.
     *
     * <code>
     * $screen->setScreen('Zaal 5');
     * </code>
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
     * This method is used to retrieve the name of the theater this viewing screen is in. This method accepts no
     * arguments and returns a string.
     *
     * <code>
     * $theaterName = $screen->getTheater();
     * </code>
     *
     * @return string
     */
    public function getTheater()
    {
        return $this->theater;
    }

    /**
     * This method is used to retrieve the name of the viewing screen. This method accepts no arguments and returns a
     * string.
     *
     * <code>
     * $screenName = $screen->getScreen();
     * </code>
     *
     * @return string
     */
    public function getScreen()
    {
        return $this->screen;
    }

    /**
     * This static method is used to build a Screen object by a single string that combines a theater name and viewing
     * screen. The keyword "Zaal" is used as a delimiter. This method accepts one argument, which must be a string,
     * and returns a Screen object.
     *
     * <code>
     * $screen = Screen::createFromString('Pathé de Kuip/Zaal 1');
     * $screen = Screen::createFromString('Pathé Groningen Zaal 5');
     * </code>
     *
     * @param string $screenString
     * @return Screen
     */
    public static function createFromString($screenString)
    {
        $retValue = new static();
        /* @type $retValue Screen */

        $retValue->setTheater(trim(preg_replace('/\/?[Z|z]aal[ ]{1,2}[0-9]{1,2}/s', '', $screenString)));
        $retValue->setScreen(trim(str_replace(array($retValue->getTheater(), '/'), '', $screenString)));

        return $retValue;
    }
}