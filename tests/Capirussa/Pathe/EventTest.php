<?php
require_once(dirname(__FILE__) . '/../../init.php');

use Capirussa\Pathe\Event;

/**
 * Tests Capirussa\Pathe\Event
 *
 */
class EventTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetMovieNameWithoutParameters()
    {
        $event = new Event();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $event->setMovieName();
    }

    public function testSetMovieNameWithMovieName()
    {
        $event = new Event();

        $this->assertNull($this->getObjectAttribute($event, 'movieName'));

        $event->setMovieName('testMovieName');

        $this->assertNotNull($this->getObjectAttribute($event, 'movieName'));
        $this->assertEquals('testMovieName', $this->getObjectAttribute($event, 'movieName'));
        $this->assertEquals('testMovieName', $event->getMovieName());
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testCreateFromMovieNameWithoutMovieName()
    {
        /** @noinspection PhpParamsInspection (this is intentional) */
        Event::createFromMovieName();
    }

    public function testCreateFromMovieNameWithEmptyMovieName()
    {
        $event = Event::createFromMovieName('');

        $this->assertInstanceOf('Capirussa\\Pathe\\Event', $event);
        $this->assertNull($event->getMovieName());
    }

    public function testCreateFromMovieNameWithMovieName()
    {
        $event = Event::createFromMovieName('testMovieName');

        $this->assertInstanceOf('Capirussa\\Pathe\\Event', $event);
        $this->assertEquals('testMovieName', $event->getMovieName());
    }
}