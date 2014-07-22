<?php
require_once(dirname(__FILE__) . '/../../init.php');

use Capirussa\Pathe\Screen;

/**
 * Tests Capirussa\Pathe\Screen
 *
 */
class ScreenTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetTheaterWithoutParameters()
    {
        $screen = new Screen();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $screen->setTheater();
    }

    public function testSetTheaterWithTheaterName()
    {
        $screen = new Screen();

        $this->assertNull($this->getObjectAttribute($screen, 'theater'));

        $screen->setTheater('testTheater');

        $this->assertNotNull($this->getObjectAttribute($screen, 'theater'));
        $this->assertEquals('testTheater', $this->getObjectAttribute($screen, 'theater'));
        $this->assertEquals('testTheater', $screen->getTheater());
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetScreenWithoutParameters()
    {
        $screen = new Screen();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $screen->setScreen();
    }

    public function testSetScreenWithScreenName()
    {
        $screen = new Screen();

        $this->assertNull($this->getObjectAttribute($screen, 'screen'));

        $screen->setScreen('testScreen');

        $this->assertNotNull($this->getObjectAttribute($screen, 'screen'));
        $this->assertEquals('testScreen', $this->getObjectAttribute($screen, 'screen'));
        $this->assertEquals('testScreen', $screen->getScreen());
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testCreateFromStringWithoutString()
    {
        /** @noinspection PhpParamsInspection (this is intentional) */
        Screen::createFromString();
    }

    public function testCreateFromStringWithoutScreenIdentifier()
    {
        $screen = Screen::createFromString('testTheater noScreen');

        $this->assertInstanceOf('Capirussa\\Pathe\\Screen', $screen);
        $this->assertEquals('testTheater noScreen', $screen->getTheater());
        $this->assertNull($screen->getScreen());
    }

    public function testCreateFromStringWithOnlyScreenIdentifier()
    {
        $screen = Screen::createFromString('Zaal 1');

        $this->assertInstanceOf('Capirussa\\Pathe\\Screen', $screen);
        $this->assertNull($screen->getTheater());
        $this->assertEquals('Zaal 1', $screen->getScreen());
    }

    public function testCreateFromStringWithTheaterAndScreen()
    {
        $screen = Screen::createFromString('testTheater Zaal 1');

        $this->assertInstanceOf('Capirussa\\Pathe\\Screen', $screen);
        $this->assertEquals('testTheater', $screen->getTheater());
        $this->assertEquals('Zaal 1', $screen->getScreen());
    }
}