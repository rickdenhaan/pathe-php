<?php
require_once(dirname(__FILE__) . '/../../init.php');

use Capirussa\Pathe\Reservation;

/**
 * Tests Capirussa\Pathe\Reservation
 *
 */
class ReservationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetTicketCountWithoutParameters()
    {
        $reservation = new Reservation();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $reservation->setTicketCount();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid ticket count
     */
    public function testSetTicketCountWithTooFewTickets()
    {
        $reservation = new Reservation();

        $reservation->setTicketCount(0);
    }

    public function testSetTicketCountWithValidTicketCount()
    {
        $reservation = new Reservation();

        $this->assertEquals(0, $this->getObjectAttribute($reservation, 'ticketCount'));
        $this->assertEquals(0, $reservation->getTicketCount());

        $reservation->setTicketCount(2);

        $this->assertEquals(2, $this->getObjectAttribute($reservation, 'ticketCount'));
        $this->assertEquals(2, $reservation->getTicketCount());
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetStatusWithoutParameters()
    {
        $reservation = new Reservation();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $reservation->setStatus();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid status
     */
    public function testSetStatusWithInvalidStatus()
    {
        $reservation = new Reservation();

        $reservation->setStatus('invalidStatus');
    }

    public function testSetStatusWithValidStatuses()
    {
        $reservation = new Reservation();
        $this->assertEquals(Reservation::STATUS_UNKNOWN, $this->getObjectAttribute($reservation, 'status'));
        $this->assertEquals(Reservation::STATUS_UNKNOWN, $reservation->getStatus());

        $validStatusesByConstants = array(
            Reservation::STATUS_COLLECTED,
            Reservation::STATUS_DELETED,
            Reservation::STATUS_UNKNOWN,
        );

        foreach ($validStatusesByConstants as $status) {
            $reservation->setStatus($status);

            $this->assertEquals($status, $this->getObjectAttribute($reservation, 'status'));
            $this->assertEquals($status, $reservation->getStatus());
        }

        $validStatusesByString = array(
            'Opgehaald',
            'Verwijderd',
            'Onbekend',
        );

        foreach ($validStatusesByString as $status) {
            $reservation->setStatus($status);

            $this->assertEquals($status, $this->getObjectAttribute($reservation, 'status'));
            $this->assertEquals($status, $reservation->getStatus());
        }
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetShowIdentifierWithoutParameters()
    {
        $reservation = new Reservation();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $reservation->setShowIdentifier();
    }

    public function testSetShowIdentifierWithIdentifier()
    {
        $reservation = new Reservation();

        $this->assertNull($this->getObjectAttribute($reservation, 'showId'));

        $reservation->setShowIdentifier('testIdentifier');

        $this->assertNotNull($this->getObjectAttribute($reservation, 'showId'));
        $this->assertEquals('testIdentifier', $this->getObjectAttribute($reservation, 'showId'));
        $this->assertEquals('testIdentifier', $reservation->getShowIdentifier());
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetReservationSetIdentifierWithoutParameters()
    {
        $reservation = new Reservation();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $reservation->setReservationSetIdentifier();
    }

    public function testSetReservationSetIdentifierWithIdentifier()
    {
        $reservation = new Reservation();

        $this->assertNull($this->getObjectAttribute($reservation, 'reservationSetId'));

        $reservation->setReservationSetIdentifier('testIdentifier');

        $this->assertNotNull($this->getObjectAttribute($reservation, 'reservationSetId'));
        $this->assertEquals('testIdentifier', $this->getObjectAttribute($reservation, 'reservationSetId'));
        $this->assertEquals('testIdentifier', $reservation->getReservationSetIdentifier());
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetCollectionNumberWithoutParameters()
    {
        $reservation = new Reservation();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $reservation->setCollectionNumber();
    }

    public function testSetCollectionNumberWithNumber()
    {
        $reservation = new Reservation();

        $this->assertNull($this->getObjectAttribute($reservation, 'collectNumber'));

        $reservation->setCollectionNumber('testNumber');

        $this->assertNotNull($this->getObjectAttribute($reservation, 'collectNumber'));
        $this->assertEquals('testNumber', $this->getObjectAttribute($reservation, 'collectNumber'));
        $this->assertEquals('testNumber', $reservation->getCollectionNumber());
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testSetCollectDateTimeWithoutParameters()
    {
        $reservation = new Reservation();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $reservation->setCollectDateTime();
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testSetCollectDateTimeWithInvalidParameter()
    {
        $reservation = new Reservation();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $reservation->setCollectDateTime('2014-01-01 12:00:00');
    }

    public function testSetCollectDateTimeWithValidDateTime()
    {
        $reservation = new Reservation();

        $this->assertNull($this->getObjectAttribute($reservation, 'collectDateTime'));

        $reservationDateTime = new \DateTime('2014-01-01 12:00:00');
        $reservation->setCollectDateTime($reservationDateTime);

        $this->assertNotNull($this->getObjectAttribute($reservation, 'collectDateTime'));
        $this->assertNotNull($reservation->getCollectDateTime());
        $this->assertEquals($this->getObjectAttribute($reservation, 'collectDateTime'), $reservation->getCollectDateTime());

        $this->assertEquals('2014-01-01 12:00:00', $reservation->getCollectDateTime()->format('Y-m-d H:i:s'));
    }
}