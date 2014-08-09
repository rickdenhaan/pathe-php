<?php
require_once(dirname(__FILE__) . '/../../init.php');

use Capirussa\Pathe\HistoryItem;
use Capirussa\Pathe\Screen;
use Capirussa\Pathe\Event;
use Capirussa\Pathe\Reservation;

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
     * @expectedException PHPUnit_Framework_Error
     */
    public function testSetReservationWithoutParameters()
    {
        $testShowTime = new DateTime('2014-01-01 12:00:00');
        $testScreen   = Screen::createFromString('testTheater Zaal 1');
        $testEvent    = Event::createFromMovieName('testMovie');

        $historyItem = new HistoryItem($testShowTime, $testScreen, $testEvent);

        /** @noinspection PhpParamsInspection (this is intentional) */
        $historyItem->setReservation();
    }

    public function testSetReservationWithReservation()
    {
        $testShowTime = new DateTime('2014-01-01 12:00:00');
        $testScreen   = Screen::createFromString('testTheater Zaal 1');
        $testEvent    = Event::createFromMovieName('testMovie');

        $historyItem = new HistoryItem($testShowTime, $testScreen, $testEvent);

        $this->assertNull($this->getObjectAttribute($historyItem, 'reservation'));
        $this->assertNull($historyItem->getReservation());

        $testReservation = new Reservation();
        $testReservation->setTicketCount(1);

        $historyItem->setReservation($testReservation);

        $this->assertNotNull($this->getObjectAttribute($historyItem, 'reservation'));
        $this->assertEquals(1, $this->getObjectAttribute($historyItem, 'reservation')->getTicketCount());

        $this->assertNotNull($historyItem->getReservation());
        $this->assertInstanceOf('Capirussa\\Pathe\\Reservation', $historyItem->getReservation());
        $this->assertEquals(1, $historyItem->getReservation()->getTicketCount());
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
        $this->assertNull($historyItem->getReservation());
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

        // should have given us five history items

        $this->assertInternalType('array', $historyItems);
        $this->assertCount(5, $historyItems);

        $historyItem = $historyItems[0];

        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $historyItem);

        $this->assertEquals('2014-01-01 12:00:00', $historyItem->getShowTime()->format('Y-m-d H:i:s'));
        $this->assertEquals('testTheater', $historyItem->getScreen()->getTheater());
        $this->assertEquals('Zaal 1', $historyItem->getScreen()->getScreen());
        $this->assertEquals('testMovie 1', $historyItem->getEvent()->getMovieName());
        $this->assertNull($historyItem->getReservation());

        $historyItem = $historyItems[1];

        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $historyItem);

        $this->assertEquals('2014-02-06 13:00:00', $historyItem->getShowTime()->format('Y-m-d H:i:s'));
        $this->assertEquals('testTheater', $historyItem->getScreen()->getTheater());
        $this->assertEquals('Zaal 4', $historyItem->getScreen()->getScreen());
        $this->assertEquals('testMovie 2', $historyItem->getEvent()->getMovieName());
        $this->assertNull($historyItem->getReservation());

        $historyItem = $historyItems[2];

        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $historyItem);

        $this->assertEquals('2014-03-11 14:00:00', $historyItem->getShowTime()->format('Y-m-d H:i:s'));
        $this->assertEquals('testTheater', $historyItem->getScreen()->getTheater());
        $this->assertEquals('Zaal 2', $historyItem->getScreen()->getScreen());
        $this->assertEquals('testMovie 3', $historyItem->getEvent()->getMovieName());
        $this->assertNull($historyItem->getReservation());

        $historyItem = $historyItems[3];

        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $historyItem);

        $this->assertEquals('2014-04-16 15:00:00', $historyItem->getShowTime()->format('Y-m-d H:i:s'));
        $this->assertEquals('testTheater', $historyItem->getScreen()->getTheater());
        $this->assertEquals('Zaal 1', $historyItem->getScreen()->getScreen());
        $this->assertEquals('testMovie 4', $historyItem->getEvent()->getMovieName());
        $this->assertNull($historyItem->getReservation());

        $historyItem = $historyItems[4];

        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $historyItem);

        $this->assertEquals('2014-05-21 16:00:00', $historyItem->getShowTime()->format('Y-m-d H:i:s'));
        $this->assertEquals('testTheater', $historyItem->getScreen()->getTheater());
        $this->assertEquals('Zaal 5', $historyItem->getScreen()->getScreen());
        $this->assertEquals('testMovie 5', $historyItem->getEvent()->getMovieName());
        $this->assertNull($historyItem->getReservation());
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testParseHistoryItemsFromReservationHistoryWithoutHtmlFile()
    {
        /** @noinspection PhpParamsInspection (this is intentional) */
        HistoryItem::parseHistoryItemsFromReservationHistory();
    }

    public function testParseHistoryItemsFromReservationHistoryWithInvalidData()
    {
        $historyItems = HistoryItem::parseHistoryItemsFromReservationHistory('abcd');

        // should not have given an error, but should also not have been parsed into a history item

        $this->assertInternalType('array', $historyItems);
        $this->assertCount(0, $historyItems);
    }

    public function testParseHistoryItemsFromReservationHistoryWithSingleEntryWithoutReservation()
    {
        $historyItems = HistoryItem::parseHistoryItemsFromReservationHistory(
            '<table class="chooseCardTable">' . "\n" .
            '    <tr>' . "\n" .
            '        <th width="100">DATUM + TIJD</th>' . "\n" .
            '        <th width="100">BIOSCOOP/ZAAL</th>' . "\n" .
            '        <th width="150">TITEL</th>' . "\n" .
            '        <th width="100">KAARTEN</th>' . "\n" .
            '    </tr>' . "\n" .
            '    <tr>' . "\n" .
            '        <td>18-7-2014&nbsp;21:30</td>' . "\n" .
            '        <td>Path&eacute; de Kuip/Zaal  9</td>' . "\n" .
            '        <td>Dawn of the Planet of the</td>' . "\n" .
            '        <td>2 Kaart(en)<br></td>' . "\n" .
            '    </tr>' . "\n" .
            '</table>'
        );

        // should have given us one history item

        $this->assertInternalType('array', $historyItems);
        $this->assertCount(1, $historyItems);

        $historyItem = $historyItems[0];

        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $historyItem);

        $this->assertEquals('2014-07-18 21:30:00', $historyItem->getShowTime()->format('Y-m-d H:i:s'));
        $this->assertEquals('Pathé de Kuip', $historyItem->getScreen()->getTheater());
        $this->assertEquals('Zaal  9', $historyItem->getScreen()->getScreen());
        $this->assertEquals('Dawn of the Planet of the', $historyItem->getEvent()->getMovieName());
        $this->assertEquals(2, $historyItem->getReservation()->getTicketCount());
        $this->assertEquals(Reservation::STATUS_UNKNOWN, $historyItem->getReservation()->getStatus());
        $this->assertNull($historyItem->getReservation()->getPickupDateTime());
    }

    public function testParseHistoryItemsFromReservationHistoryWithSingleEntryWithReservation()
    {
        $historyItems = HistoryItem::parseHistoryItemsFromReservationHistory(
            '<table class="chooseCardTable">' . "\n" .
            '    <tr>' . "\n" .
            '        <th width="100">DATUM + TIJD</th>' . "\n" .
            '        <th width="100">BIOSCOOP/ZAAL</th>' . "\n" .
            '        <th width="150">TITEL</th>' . "\n" .
            '        <th width="100">KAARTEN</th>' . "\n" .
            '    </tr>' . "\n" .
            '    <tr>' . "\n" .
            '        <td>18-7-2014&nbsp;21:30</td>' . "\n" .
            '        <td>Path&eacute; de Kuip/Zaal  9</td>' . "\n" .
            '        <td>Dawn of the Planet of the</td>' . "\n" .
            '        <td>2 Kaart(en)<br></td>' . "\n" .
            '    </tr>' . "\n" .
            '    <tr>' . "\n" .
            '        <th width="100">DATUM + TIJD</th>' . "\n" .
            '        <th width="100">BIOSCOOP/ZAAL</th>' . "\n" .
            '        <th width="150">TITEL</th>' . "\n" .
            '        <th width="100">KAARTEN</th>' . "\n" .
            '    </tr>' . "\n" .
            '    <tr>' . "\n" .
            '        <td>18-7-2014&nbsp;21:30</td>' . "\n" .
            '        <td>Path&eacute; de Kuip/Zaal  9</td>' . "\n" .
            '        <td>Dawn of the Planet of the</td>' . "\n" .
            '        <td><!--ResNr. 27<br>--><a href="javascript:GetReservationDetails(\'25\', \'26\', \'27\', \'dvarmb1ng45bh7mi9iovgsioh3\');">Opgehaald</a></td>' . "\n" .
            '    </tr>' . "\n" .
            '    <tr><td colspan=4><div id="Reservation26"></div></td></tr>' . "\n" .
            '</table>'
        );

        // should have given us one history item

        $this->assertInternalType('array', $historyItems);
        $this->assertCount(1, $historyItems);

        $historyItem = $historyItems[0];

        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $historyItem);

        $this->assertEquals('2014-07-18 21:30:00', $historyItem->getShowTime()->format('Y-m-d H:i:s'));
        $this->assertEquals('Pathé de Kuip', $historyItem->getScreen()->getTheater());
        $this->assertEquals('Zaal  9', $historyItem->getScreen()->getScreen());
        $this->assertEquals('Dawn of the Planet of the', $historyItem->getEvent()->getMovieName());
        $this->assertEquals(2, $historyItem->getReservation()->getTicketCount());
        $this->assertEquals(Reservation::STATUS_COLLECTED, $historyItem->getReservation()->getStatus());
        $this->assertEquals(25, $historyItem->getReservation()->getShowIdentifier());
        $this->assertEquals(26, $historyItem->getReservation()->getReservationSetIdentifier());
        $this->assertEquals(27, $historyItem->getReservation()->getCollectionNumber());
        $this->assertNull($historyItem->getReservation()->getPickupDateTime());
    }

    public function testParseHistoryItemsFromReservationHistoryWithMultipleEntriesAndMixedReservations()
    {
        $historyItems = HistoryItem::parseHistoryItemsFromReservationHistory(
            '<table class="chooseCardTable">' . "\n" .
            '    <tr>' . "\n" .
            '        <th width="100">DATUM + TIJD</th>' . "\n" .
            '        <th width="100">BIOSCOOP/ZAAL</th>' . "\n" .
            '        <th width="150">TITEL</th>' . "\n" .
            '        <th width="100">KAARTEN</th>' . "\n" .
            '    </tr>' . "\n" .
            '    <tr>' . "\n" .
            '        <td>18-7-2014&nbsp;21:30</td>' . "\n" .
            '        <td>Path&eacute; de Kuip/Zaal  9</td>' . "\n" .
            '        <td>Dawn of the Planet of the</td>' . "\n" .
            '        <td>2 Kaart(en)<br></td>' . "\n" .
            '    </tr>' . "\n" .
            '    <tr>' . "\n" .
            '        <td>12-7-2014&nbsp;19:50</td>' . "\n" .
            '        <td>Path&eacute; de Kuip/Zaal  3</td>' . "\n" .
            '        <td>How to Train Your Dragon </td>' . "\n" .
            '        <td>4 Kaart(en)<br></td>' . "\n" .
            '    </tr>' . "\n" .
            '    <tr>' . "\n" .
            '        <td>20-6-2014&nbsp;22:45</td>' . "\n" .
            '        <td>Path&eacute; de Kuip/Zaal  6</td>' . "\n" .
            '        <td>Transcendence</td>' . "\n" .
            '        <td>4 Kaart(en)<br></td>' . "\n" .
            '    </tr>' . "\n" .
            '    <tr>' . "\n" .
            '        <td>29-5-2014&nbsp;21:20</td>' . "\n" .
            '        <td>Path&eacute; de Kuip/Zaal  5</td>' . "\n" .
            '        <td>Edge of Tomorrow</td>' . "\n" .
            '        <td>1 Kaart(en)<br></td>' . "\n" .
            '    </tr>' . "\n" .
            '    <tr>' . "\n" .
            '        <td>23-5-2014&nbsp;21:20</td>' . "\n" .
            '        <td>Path&eacute; de Kuip/Zaal  6</td>' . "\n" .
            '        <td>X-Men: Days of Future Pas</td>' . "\n" .
            '        <td>3 Kaart(en)<br></td>' . "\n" .
            '    </tr>' . "\n" .
            '    <tr>' . "\n" .
            '        <th width="100">DATUM + TIJD</th>' . "\n" .
            '        <th width="100">BIOSCOOP/ZAAL</th>' . "\n" .
            '        <th width="150">TITEL</th>' . "\n" .
            '        <th width="100">KAARTEN</th>' . "\n" .
            '    </tr>' . "\n" .
            '    <tr>' . "\n" .
            '        <td>18-7-2014&nbsp;21:30</td>' . "\n" .
            '        <td>KuiR/Zaal  9</td>' . "\n" .
            '        <td>Dawn of the Planet of the</td>' . "\n" .
            '        <td><!--ResNr. 26<br>--><a href="javascript:GetReservationDetails(\'25\', \'26\', \'27\', \'dvarmb1ng45bh7mi9iovgsioh3\');">Opgehaald</a></td>' . "\n" .
            '    </tr>' . "\n" .
            '    <tr><td colspan=4><div id="Reservation26"></div></td></tr>' . "\n" .
            '    <tr>' . "\n" .
            '        <td>20-6-2014&nbsp;22:45</td>' . "\n" .
            '        <td>KuiR/Zaal  6</td>' . "\n" .
            '        <td>Transcendence</td>' . "\n" .
            '        <td><!--ResNr. 20<br>--><a href="javascript:GetReservationDetails(\'19\', \'20\', \'21\', \'dvarmb1ng45bh7mi9iovgsioh3\');">Opgehaald</a></td>' . "\n" .
            '    </tr>' . "\n" .
            '    <tr><td colspan=4><div id="Reservation20"></div></td></tr>' . "\n" .
            '    <tr>' . "\n" .
            '        <td>23-5-2014&nbsp;21:20</td>' . "\n" .
            '        <td>KuiR/Zaal  6</td>' . "\n" .
            '        <td>X-Men: Days of Future Pas</td>' . "\n" .
            '        <td><!--ResNr. 14<br>--><a href="javascript:GetReservationDetails(\'13\', \'14\', \'15\', \'dvarmb1ng45bh7mi9iovgsioh3\');">Opgehaald</a></td>' . "\n" .
            '    </tr>' . "\n" .
            '    <tr><td colspan=4><div id="Reservation14"></div></td></tr>' . "\n" .
            '    <tr>' . "\n" .
            '        <td>2-3-2014&nbsp;20:05</td>' . "\n" .
            '        <td>KuiR/Zaal 10</td>' . "\n" .
            '        <td>LEGO Movie, The</td>' . "\n" .
            '        <td><!--ResNr. 11<br>--><a href="javascript:GetReservationDetails(\'10\', \'11\', \'12\', \'dvarmb1ng45bh7mi9iovgsioh3\');">Opgehaald</a></td>' . "\n" .
            '    </tr>' . "\n" .
            '    <tr><td colspan=4><div id="Reservation11"></div></td></tr>' . "\n" .
            '</table>'
        );

        // should have given us five history items

        $this->assertInternalType('array', $historyItems);
        $this->assertCount(5, $historyItems);

        $historyItem = $historyItems[0];

        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $historyItem);

        $this->assertEquals('2014-07-18 21:30:00', $historyItem->getShowTime()->format('Y-m-d H:i:s'));
        $this->assertEquals('Pathé de Kuip', $historyItem->getScreen()->getTheater());
        $this->assertEquals('Zaal  9', $historyItem->getScreen()->getScreen());
        $this->assertEquals('Dawn of the Planet of the', $historyItem->getEvent()->getMovieName());
        $this->assertEquals(2, $historyItem->getReservation()->getTicketCount());
        $this->assertEquals(Reservation::STATUS_COLLECTED, $historyItem->getReservation()->getStatus());
        $this->assertEquals(25, $historyItem->getReservation()->getShowIdentifier());
        $this->assertEquals(26, $historyItem->getReservation()->getReservationSetIdentifier());
        $this->assertEquals(27, $historyItem->getReservation()->getCollectionNumber());
        $this->assertNull($historyItem->getReservation()->getPickupDateTime());

        $historyItem = $historyItems[1];

        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $historyItem);

        $this->assertEquals('2014-07-12 19:50:00', $historyItem->getShowTime()->format('Y-m-d H:i:s'));
        $this->assertEquals('Pathé de Kuip', $historyItem->getScreen()->getTheater());
        $this->assertEquals('Zaal  3', $historyItem->getScreen()->getScreen());
        $this->assertEquals('How to Train Your Dragon', $historyItem->getEvent()->getMovieName());
        $this->assertEquals(4, $historyItem->getReservation()->getTicketCount());
        $this->assertEquals(Reservation::STATUS_UNKNOWN, $historyItem->getReservation()->getStatus());
        $this->assertNull($historyItem->getReservation()->getShowIdentifier());
        $this->assertNull($historyItem->getReservation()->getReservationSetIdentifier());
        $this->assertNull($historyItem->getReservation()->getCollectionNumber());
        $this->assertNull($historyItem->getReservation()->getPickupDateTime());

        $historyItem = $historyItems[2];

        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $historyItem);

        $this->assertEquals('2014-06-20 22:45:00', $historyItem->getShowTime()->format('Y-m-d H:i:s'));
        $this->assertEquals('Pathé de Kuip', $historyItem->getScreen()->getTheater());
        $this->assertEquals('Zaal  6', $historyItem->getScreen()->getScreen());
        $this->assertEquals('Transcendence', $historyItem->getEvent()->getMovieName());
        $this->assertEquals(4, $historyItem->getReservation()->getTicketCount());
        $this->assertEquals(Reservation::STATUS_COLLECTED, $historyItem->getReservation()->getStatus());
        $this->assertEquals(19, $historyItem->getReservation()->getShowIdentifier());
        $this->assertEquals(20, $historyItem->getReservation()->getReservationSetIdentifier());
        $this->assertEquals(21, $historyItem->getReservation()->getCollectionNumber());
        $this->assertNull($historyItem->getReservation()->getPickupDateTime());

        $historyItem = $historyItems[3];

        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $historyItem);

        $this->assertEquals('2014-05-29 21:20:00', $historyItem->getShowTime()->format('Y-m-d H:i:s'));
        $this->assertEquals('Pathé de Kuip', $historyItem->getScreen()->getTheater());
        $this->assertEquals('Zaal  5', $historyItem->getScreen()->getScreen());
        $this->assertEquals('Edge of Tomorrow', $historyItem->getEvent()->getMovieName());
        $this->assertEquals(1, $historyItem->getReservation()->getTicketCount());
        $this->assertEquals(Reservation::STATUS_UNKNOWN, $historyItem->getReservation()->getStatus());
        $this->assertNull($historyItem->getReservation()->getShowIdentifier());
        $this->assertNull($historyItem->getReservation()->getReservationSetIdentifier());
        $this->assertNull($historyItem->getReservation()->getCollectionNumber());
        $this->assertNull($historyItem->getReservation()->getPickupDateTime());

        $historyItem = $historyItems[4];

        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $historyItem);

        $this->assertEquals('2014-05-23 21:20:00', $historyItem->getShowTime()->format('Y-m-d H:i:s'));
        $this->assertEquals('Pathé de Kuip', $historyItem->getScreen()->getTheater());
        $this->assertEquals('Zaal  6', $historyItem->getScreen()->getScreen());
        $this->assertEquals('X-Men: Days of Future Pas', $historyItem->getEvent()->getMovieName());
        $this->assertEquals(3, $historyItem->getReservation()->getTicketCount());
        $this->assertEquals(Reservation::STATUS_COLLECTED, $historyItem->getReservation()->getStatus());
        $this->assertEquals(13, $historyItem->getReservation()->getShowIdentifier());
        $this->assertEquals(14, $historyItem->getReservation()->getReservationSetIdentifier());
        $this->assertEquals(15, $historyItem->getReservation()->getCollectionNumber());
        $this->assertNull($historyItem->getReservation()->getPickupDateTime());
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testParseHistoryItemsFromCardHistoryWithoutHtmlFile()
    {
        /** @noinspection PhpParamsInspection (this is intentional) */
        HistoryItem::parseHistoryItemsFromCardHistory();
    }

    public function testParseHistoryItemsFromCardHistoryWithInvalidData()
    {
        $historyItems = HistoryItem::parseHistoryItemsFromCardHistory('abcd');

        // should not have given an error, but should also not have been parsed into a history item

        $this->assertInternalType('array', $historyItems);
        $this->assertCount(0, $historyItems);
    }

    public function testParseHistoryItemsFromCardHistoryWithInvalidEntry()
    {
        $historyItems = HistoryItem::parseHistoryItemsFromCardHistory(
            '<table class="chooseCardTable">' . "\n" .
            '    <tr>' . "\n" .
            '        <th>DATUM+TIJD</th>' . "\n" .
            '        <th>BIOSCOOP/ZAAL</th>' . "\n" .
            '        <th colspan="2">ACTIE/TITEL</th>' . "\n" .
            '        <th class="price">KAARTEN</th>' . "\n" .
            '    </tr>' . "\n" .
            '    <tr>' . "\n" . // should not be parsed, because the first cell does not contain a valid date/time
            '        <td>&nbsp;</td>' . "\n" .
            '        <td> </td>' . "\n" .
            '        <td>aangeschaft</td><td>  &nbsp;</td>' . "\n" .
            '        <td class="price" style="padding-right:40px;"></td>' . "\n" .
            '    </tr>' . "\n" .
            '    <tr>' . "\n" . // should not be parsed, because there are only four cells instead of five
            '        <td>21-8-2012&nbsp;22:18:47</td>' . "\n" .
            '        <td><br>Pathe de Kuip Zaal  9</td>' . "\n" .
            '        <td><br>Expendables 2, The <br>2012-08-21 22:30 &nbsp;</td>' . "\n" .
            '        <td class="price" style="padding-right:40px;">1</td>' . "\n" .
            '    </tr>' . "\n" .
            '</table>'
        );

        $this->assertInternalType('array', $historyItems);
        $this->assertCount(0, $historyItems);
    }

    public function testParseHistoryItemsFromCardHistoryWithSingleEntry()
    {
        $historyItems = HistoryItem::parseHistoryItemsFromCardHistory(
            '<table class="chooseCardTable">' . "\n" .
            '    <tr>' . "\n" .
            '        <th>DATUM+TIJD</th>' . "\n" .
            '        <th>BIOSCOOP/ZAAL</th>' . "\n" .
            '        <th colspan="2">ACTIE/TITEL</th>' . "\n" .
            '        <th class="price">KAARTEN</th>' . "\n" .
            '    </tr>' . "\n" .
            '    <tr>' . "\n" . // should not be parsed, because the first cell does not contain a valid date/time
            '        <td>&nbsp;</td>' . "\n" .
            '        <td> </td>' . "\n" .
            '        <td>aangeschaft</td><td>  &nbsp;</td>' . "\n" .
            '        <td class="price" style="padding-right:40px;"></td>' . "\n" .
            '    </tr>' . "\n" .
            '    <tr>' . "\n" .
            '        <td>21-8-2012&nbsp;22:18:47</td>' . "\n" .
            '        <td><br>Pathe de Kuip Zaal  9</td>' . "\n" .
            '        <td>1 gebruikt</td><td><br>Expendables 2, The <br>2012-08-21 22:30 &nbsp;</td>' . "\n" .
            '        <td class="price" style="padding-right:40px;">1</td>' . "\n" .
            '    </tr>' . "\n" .
            '</table>'
        );

        // should have given us one history item

        $this->assertInternalType('array', $historyItems);
        $this->assertCount(1, $historyItems);

        $historyItem = $historyItems[0];

        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $historyItem);

        $this->assertEquals('2012-08-21 22:30:00', $historyItem->getShowTime()->format('Y-m-d H:i:s'));
        $this->assertEquals('Pathe de Kuip', $historyItem->getScreen()->getTheater());
        $this->assertEquals('Zaal  9', $historyItem->getScreen()->getScreen());
        $this->assertEquals('Expendables 2, The', $historyItem->getEvent()->getMovieName());
        $this->assertEquals(1, $historyItem->getReservation()->getTicketCount());
        $this->assertEquals(Reservation::STATUS_UNKNOWN, $historyItem->getReservation()->getStatus());
        $this->assertNull($historyItem->getReservation()->getShowIdentifier());
        $this->assertNull($historyItem->getReservation()->getReservationSetIdentifier());
        $this->assertNull($historyItem->getReservation()->getCollectionNumber());
        $this->assertNotNull($historyItem->getReservation()->getPickupDateTime());
        $this->assertEquals('2012-08-21 22:18:47', $historyItem->getReservation()->getPickupDateTime()->format('Y-m-d H:i:s'));
    }

    public function testParseHistoryItemsFromCardHistoryWithMultipleEntries()
    {
        $historyItems = HistoryItem::parseHistoryItemsFromCardHistory(
            '<table class="chooseCardTable">' . "\n" .
            '    <tr>' . "\n" .
            '        <th>DATUM+TIJD</th>' . "\n" .
            '        <th>BIOSCOOP/ZAAL</th>' . "\n" .
            '        <th colspan="2">ACTIE/TITEL</th>' . "\n" .
            '        <th class="price">KAARTEN</th>' . "\n" .
            '    </tr>' . "\n" .
            '    <tr>' . "\n" . // should not be parsed, because the first cell does not contain a valid date/time
            '        <td>&nbsp;</td>' . "\n" .
            '        <td> </td>' . "\n" .
            '        <td>aangeschaft</td><td>  &nbsp;</td>' . "\n" .
            '        <td class="price" style="padding-right:40px;"></td>' . "\n" .
            '    </tr>' . "\n" .
            '    <tr>' . "\n" .
            '        <td>21-8-2012&nbsp;22:18:47</td>' . "\n" .
            '        <td><br>Pathe de Kuip Zaal  9</td>' . "\n" .
            '        <td>1 gebruikt</td><td><br>Expendables 2, The <br>2012-08-21 22:30 &nbsp;</td>' . "\n" .
            '        <td class="price" style="padding-right:40px;">1</td>' . "\n" .
            '    </tr>' . "\n" .
            '    <tr>' . "\n" .
            '        <td>31-8-2012&nbsp;20:29:03</td>' . "\n" .
            '        <td><br>Pathe de Kuip Zaal  6</td>' . "\n" .
            '        <td>1 gebruikt</td><td><br>Bourne Legacy, The <br>2012-08-31 21:00 &nbsp;</td>' . "\n" .
            '        <td class="price" style="padding-right:40px;">1</td>' . "\n" .
            '    </tr>' . "\n" .
            '    <tr>' . "\n" .
            '        <td>3-9-2012&nbsp;20:59:56</td>' . "\n" .
            '        <td><br>Pathe de Kuip Zaal  7</td>' . "\n" .
            '        <td>1 gebruikt</td><td><br>Red Lights <br>2012-09-03 21:30 &nbsp;</td>' . "\n" .
            '        <td class="price" style="padding-right:40px;">1</td>' . "\n" .
            '    </tr>' . "\n" .
            '</table>'
        );

        // should have given us three history items

        $this->assertInternalType('array', $historyItems);
        $this->assertCount(3, $historyItems);

        $historyItem = $historyItems[0];

        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $historyItem);

        $this->assertEquals('2012-08-21 22:30:00', $historyItem->getShowTime()->format('Y-m-d H:i:s'));
        $this->assertEquals('Pathe de Kuip', $historyItem->getScreen()->getTheater());
        $this->assertEquals('Zaal  9', $historyItem->getScreen()->getScreen());
        $this->assertEquals('Expendables 2, The', $historyItem->getEvent()->getMovieName());
        $this->assertEquals(1, $historyItem->getReservation()->getTicketCount());
        $this->assertEquals(Reservation::STATUS_UNKNOWN, $historyItem->getReservation()->getStatus());
        $this->assertNull($historyItem->getReservation()->getShowIdentifier());
        $this->assertNull($historyItem->getReservation()->getReservationSetIdentifier());
        $this->assertNull($historyItem->getReservation()->getCollectionNumber());
        $this->assertNotNull($historyItem->getReservation()->getPickupDateTime());
        $this->assertEquals('2012-08-21 22:18:47', $historyItem->getReservation()->getPickupDateTime()->format('Y-m-d H:i:s'));

        $historyItem = $historyItems[1];

        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $historyItem);

        $this->assertEquals('2012-08-31 21:00:00', $historyItem->getShowTime()->format('Y-m-d H:i:s'));
        $this->assertEquals('Pathe de Kuip', $historyItem->getScreen()->getTheater());
        $this->assertEquals('Zaal  6', $historyItem->getScreen()->getScreen());
        $this->assertEquals('Bourne Legacy, The', $historyItem->getEvent()->getMovieName());
        $this->assertEquals(1, $historyItem->getReservation()->getTicketCount());
        $this->assertEquals(Reservation::STATUS_UNKNOWN, $historyItem->getReservation()->getStatus());
        $this->assertNull($historyItem->getReservation()->getShowIdentifier());
        $this->assertNull($historyItem->getReservation()->getReservationSetIdentifier());
        $this->assertNull($historyItem->getReservation()->getCollectionNumber());
        $this->assertNotNull($historyItem->getReservation()->getPickupDateTime());
        $this->assertEquals('2012-08-31 20:29:03', $historyItem->getReservation()->getPickupDateTime()->format('Y-m-d H:i:s'));

        $historyItem = $historyItems[2];

        $this->assertInstanceOf('Capirussa\\Pathe\\HistoryItem', $historyItem);

        $this->assertEquals('2012-09-03 21:30:00', $historyItem->getShowTime()->format('Y-m-d H:i:s'));
        $this->assertEquals('Pathe de Kuip', $historyItem->getScreen()->getTheater());
        $this->assertEquals('Zaal  7', $historyItem->getScreen()->getScreen());
        $this->assertEquals('Red Lights', $historyItem->getEvent()->getMovieName());
        $this->assertEquals(1, $historyItem->getReservation()->getTicketCount());
        $this->assertEquals(Reservation::STATUS_UNKNOWN, $historyItem->getReservation()->getStatus());
        $this->assertNull($historyItem->getReservation()->getShowIdentifier());
        $this->assertNull($historyItem->getReservation()->getReservationSetIdentifier());
        $this->assertNull($historyItem->getReservation()->getCollectionNumber());
        $this->assertNotNull($historyItem->getReservation()->getPickupDateTime());
        $this->assertEquals('2012-09-03 20:59:56', $historyItem->getReservation()->getPickupDateTime()->format('Y-m-d H:i:s'));
    }
}