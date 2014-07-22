<?php
require_once(dirname(__FILE__) . '/../../init.php');

use Capirussa\Pathe\HistoryItem;
use Capirussa\Pathe\Screen;
use Capirussa\Pathe\Event;

/**
 * Tests Capirussa\Pathe\HistoryItem
 *
 */
class HistoryItemTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testConstructWithoutParameters()
    {
        /** @noinspection PhpParamsInspection (this is intentional) */
        new HistoryItem();
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testConstructWithIncorrectParameters()
    {
        /** @noinspection PhpParamsInspection (this is intentional) */
        new HistoryItem('foo', 'bar', 'baz');
    }

    public function testConstructWithCorrectParameters()
    {
        $testShowTime = new DateTime('2014-01-01 12:00:00');
        $testScreen   = Screen::createFromString('testTheater Zaal 1');
        $testEvent    = Event::createFromMovieName('testMovie');

        $historyItem = new HistoryItem($testShowTime, $testScreen, $testEvent);

        $this->assertNotNull($this->getObjectAttribute($historyItem, 'showTime'));
        $this->assertNotNull($this->getObjectAttribute($historyItem, 'screen'));
        $this->assertNotNull($this->getObjectAttribute($historyItem, 'event'));

        $this->assertEquals($testShowTime, $this->getObjectAttribute($historyItem, 'showTime'));
        $this->assertEquals($testScreen, $this->getObjectAttribute($historyItem, 'screen'));
        $this->assertEquals($testEvent, $this->getObjectAttribute($historyItem, 'event'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testSetShowTimeWithoutParameters()
    {
        $testShowTime = new DateTime('2014-01-01 12:00:00');
        $testScreen   = Screen::createFromString('testTheater Zaal 1');
        $testEvent    = Event::createFromMovieName('testMovie');

        $historyItem = new HistoryItem($testShowTime, $testScreen, $testEvent);

        /** @noinspection PhpParamsInspection (this is intentional) */
        $historyItem->setShowTime();
    }

    public function testSetShowTimeWithShowTime()
    {
        $testShowTime = new DateTime('2014-01-01 12:00:00');
        $testScreen   = Screen::createFromString('testTheater Zaal 1');
        $testEvent    = Event::createFromMovieName('testMovie');

        $historyItem = new HistoryItem($testShowTime, $testScreen, $testEvent);

        $this->assertNotNull($this->getObjectAttribute($historyItem, 'showTime'));
        $this->assertEquals('2014-01-01 12:00:00', $this->getObjectAttribute($historyItem, 'showTime')->format('Y-m-d H:i:s'));

        $testShowTime = new DateTime('2014-02-01 12:00:00');
        $historyItem->setShowTime($testShowTime);

        $this->assertNotNull($this->getObjectAttribute($historyItem, 'showTime'));
        $this->assertEquals('2014-02-01 12:00:00', $this->getObjectAttribute($historyItem, 'showTime')->format('Y-m-d H:i:s'));

        $this->assertNotNull($historyItem->getShowTime());
        $this->assertInstanceOf('DateTime', $historyItem->getShowTime());
        $this->assertEquals('2014-02-01 12:00:00', $historyItem->getShowTime()->format('Y-m-d H:i:s'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testSetScreenWithoutParameters()
    {
        $testShowTime = new DateTime('2014-01-01 12:00:00');
        $testScreen   = Screen::createFromString('testTheater Zaal 1');
        $testEvent    = Event::createFromMovieName('testMovie');

        $historyItem = new HistoryItem($testShowTime, $testScreen, $testEvent);

        /** @noinspection PhpParamsInspection (this is intentional) */
        $historyItem->setScreen();
    }

    public function testSetScreenWithScreen()
    {
        $testShowTime = new DateTime('2014-01-01 12:00:00');
        $testScreen   = Screen::createFromString('testTheater Zaal 1');
        $testEvent    = Event::createFromMovieName('testMovie');

        $historyItem = new HistoryItem($testShowTime, $testScreen, $testEvent);

        $this->assertNotNull($this->getObjectAttribute($historyItem, 'screen'));
        $this->assertEquals('Zaal 1', $this->getObjectAttribute($historyItem, 'screen')->getScreen());

        $testScreen = Screen::createFromString('testTheater Zaal 9');
        $historyItem->setScreen($testScreen);

        $this->assertNotNull($this->getObjectAttribute($historyItem, 'screen'));
        $this->assertEquals('Zaal 9', $this->getObjectAttribute($historyItem, 'screen')->getScreen());

        $this->assertNotNull($historyItem->getScreen());
        $this->assertInstanceOf('Capirussa\\Pathe\\Screen', $historyItem->getScreen());
        $this->assertEquals('Zaal 9', $historyItem->getScreen()->getScreen());
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testSetEventWithoutParameters()
    {
        $testShowTime = new DateTime('2014-01-01 12:00:00');
        $testScreen   = Screen::createFromString('testTheater Zaal 1');
        $testEvent    = Event::createFromMovieName('testMovie');

        $historyItem = new HistoryItem($testShowTime, $testScreen, $testEvent);

        /** @noinspection PhpParamsInspection (this is intentional) */
        $historyItem->setEvent();
    }

    public function testSetEventWithEvent()
    {
        $testShowTime = new DateTime('2014-01-01 12:00:00');
        $testScreen   = Screen::createFromString('testTheater Zaal 1');
        $testEvent    = Event::createFromMovieName('testMovie');

        $historyItem = new HistoryItem($testShowTime, $testScreen, $testEvent);

        $this->assertNotNull($this->getObjectAttribute($historyItem, 'event'));
        $this->assertEquals('testMovie', $this->getObjectAttribute($historyItem, 'event')->getMovieName());

        $testEvent = Event::createFromMovieName('testOtherMovie');
        $historyItem->setEvent($testEvent);

        $this->assertNotNull($this->getObjectAttribute($historyItem, 'event'));
        $this->assertEquals('testOtherMovie', $this->getObjectAttribute($historyItem, 'event')->getMovieName());

        $this->assertNotNull($historyItem->getEvent());
        $this->assertInstanceOf('Capirussa\\Pathe\\Event', $historyItem->getEvent());
        $this->assertEquals('testOtherMovie', $historyItem->getEvent()->getMovieName());
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testParseHistoryItemsFromDataFileWithoutDataFile()
    {
        /** @noinspection PhpParamsInspection (this is intentional) */
        HistoryItem::parseHistoryItemsFromDataFile();
    }

    public function testParseHistoryItemsFromDataFileWithoutSeparator()
    {
        $historyItems = HistoryItem::parseHistoryItemsFromDataFile('testInvalidDataFile');

        // should not have given an error, but should also not have been parsed into a history item

        $this->assertInternalType('array', $historyItems);
        $this->assertCount(0, $historyItems);
    }

    public function testParseHistoryItemsFromDataFileWithInsufficientData()
    {
        $historyItems = HistoryItem::parseHistoryItemsFromDataFile('abcd|efgh');

        // should not have given an error, but should also not have been parsed into a history item

        $this->assertInternalType('array', $historyItems);
        $this->assertCount(0, $historyItems);
    }

    public function testParseHistoryItemsFromDataFileWithInvalidData()
    {
        $historyItems = HistoryItem::parseHistoryItemsFromDataFile('abcd|efgh|ijkl');

        // should not have given an error, but should also not have been parsed into a history item because of an
        // unparseable date/time string

        $this->assertInternalType('array', $historyItems);
        $this->assertCount(0, $historyItems);
    }

    public function testParseHistoryItemsFromDataFileWithSingleEntry()
    {
        $historyItems = HistoryItem::parseHistoryItemsFromDataFile('31-01-2014 12:00|efgh|ijkl');

        // should have given us one history item

        $this->assertInternalType('array', $historyItems);
        $this->assertCount(1, $historyItems);

        $historyItem = $historyItems[0];

        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $historyItem);

        $this->assertEquals('2014-01-31 12:00:00', $historyItem->getShowTime()->format('Y-m-d H:i:s'));
        $this->assertEquals('efgh', $historyItem->getScreen()->getTheater());
        $this->assertNull($historyItem->getScreen()->getScreen());
        $this->assertEquals('ijkl', $historyItem->getEvent()->getMovieName());
    }

    public function testParseHistoryItemsFromDataFileWithMultipleEntries()
    {
        $historyItems = HistoryItem::parseHistoryItemsFromDataFile(
            '01-01-2014 12:00|testTheater Zaal 1|testMovie 1' . "\n" .
            '06-02-2014 13:00|testTheater Zaal 4|testMovie 2' . "\r" .
            '11-03-2014 14:00|testTheater Zaal 2|testMovie 3' . "\n\r" .
            '16-04-2014 15:00|testTheater Zaal 1|testMovie 4' . "\r\n" .
            '21-05-2014 16:00|testTheater Zaal 5|testMovie 5'
        );

        // should have given us one history item

        $this->assertInternalType('array', $historyItems);
        $this->assertCount(5, $historyItems);

        $historyItem = $historyItems[0];

        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $historyItem);

        $this->assertEquals('2014-01-01 12:00:00', $historyItem->getShowTime()->format('Y-m-d H:i:s'));
        $this->assertEquals('testTheater', $historyItem->getScreen()->getTheater());
        $this->assertEquals('Zaal 1', $historyItem->getScreen()->getScreen());
        $this->assertEquals('testMovie 1', $historyItem->getEvent()->getMovieName());

        $historyItem = $historyItems[1];

        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $historyItem);

        $this->assertEquals('2014-02-06 13:00:00', $historyItem->getShowTime()->format('Y-m-d H:i:s'));
        $this->assertEquals('testTheater', $historyItem->getScreen()->getTheater());
        $this->assertEquals('Zaal 4', $historyItem->getScreen()->getScreen());
        $this->assertEquals('testMovie 2', $historyItem->getEvent()->getMovieName());

        $historyItem = $historyItems[2];

        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $historyItem);

        $this->assertEquals('2014-03-11 14:00:00', $historyItem->getShowTime()->format('Y-m-d H:i:s'));
        $this->assertEquals('testTheater', $historyItem->getScreen()->getTheater());
        $this->assertEquals('Zaal 2', $historyItem->getScreen()->getScreen());
        $this->assertEquals('testMovie 3', $historyItem->getEvent()->getMovieName());

        $historyItem = $historyItems[3];

        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $historyItem);

        $this->assertEquals('2014-04-16 15:00:00', $historyItem->getShowTime()->format('Y-m-d H:i:s'));
        $this->assertEquals('testTheater', $historyItem->getScreen()->getTheater());
        $this->assertEquals('Zaal 1', $historyItem->getScreen()->getScreen());
        $this->assertEquals('testMovie 4', $historyItem->getEvent()->getMovieName());

        $historyItem = $historyItems[4];

        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $historyItem);

        $this->assertEquals('2014-05-21 16:00:00', $historyItem->getShowTime()->format('Y-m-d H:i:s'));
        $this->assertEquals('testTheater', $historyItem->getScreen()->getTheater());
        $this->assertEquals('Zaal 5', $historyItem->getScreen()->getScreen());
        $this->assertEquals('testMovie 5', $historyItem->getEvent()->getMovieName());
    }
}