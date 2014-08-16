<?php
namespace Capirussa\Pathe;

/**
 * The Reservation object contains information about a movie reservation.
 *
 * @package Capirussa\Pathe
 */
class Reservation
{
    /**
     * This status constant indicates that there is no information available about the ticket status for this
     * reservation.
     */
    const STATUS_UNKNOWN = 'Onbekend';

    /**
     * This status constant indicates that the tickets for this reservation have been collected.
     */
    const STATUS_COLLECTED = 'Opgehaald';

    /**
     * This status constant indicates that the tickets for this reservation were not collected, and have been deleted.
     */
    const STATUS_DELETED = 'Verwijderd';

    /**
     * This property contains the number of tickets in this reservation. Defaults to 0.
     *
     * @type int
     */
    protected $ticketCount = 0;

    /**
     * This property contains the ticket status for this reservation. Possible values are the status constants defined
     * in this class. This property defaults to `Reservation::STATUS_UNKNOWN`.
     *
     * @type string
     */
    protected $status = self::STATUS_UNKNOWN;

    /**
     * This property contains the show identifier from Mijn Pathé. Since I haven't found a use for this yet, it may be
     * removed in the future.
     *
     * @type string
     */
    protected $showId;

    /**
     * This property contains the reservation set identifier from Mijn Pathé. Since I haven't found a use for this
     * yet, it may be removed in the future.
     *
     * @type string
     */
    protected $reservationSetId;

    /**
     * This property contains the collection number from Mijn Pathé. Since I haven't found a use for this yet, it may
     * be removed in the future.
     *
     * @type string
     */
    protected $collectNumber;

    /**
     * This property contains a DateTime object with the date and time at which the tickets for this reservation were
     * collected.
     *
     * @type \DateTime
     */
    protected $collectDateTime;

    /**
     * This method is used to set the number of tickets in this reservation. It accepts one argument, which must be a
     * positive integer, and returns nothing.
     *
     * <code>
     * $reservation->setTicketCount(4);
     * </code>
     *
     * @param int $ticketCount
     * @throws \InvalidArgumentException
     */
    public function setTicketCount($ticketCount)
    {
        $ticketCount = intval($ticketCount);

        // sanity check, ticket count must always be positive
        if ($ticketCount < 1) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid ticket count "%2$s", must be greater than 0',
                    __METHOD__,
                    $ticketCount
                )
            );
        }

        $this->ticketCount = $ticketCount;
    }

    /**
     * This method is used to retrieve the number of tickets in this reservation.
     *
     * <code>
     * $ticketCount = $reservation->getTicketCount();
     * </code>
     *
     * @return int
     */
    public function getTicketCount()
    {
        return $this->ticketCount;
    }

    /**
     * This method is used to set the status of this reservation. It accepts one argument, which must be one of the
     * defined status constants, and returns nothing.
     *
     * <code>
     * $reservation->setStatus(Reservation::STATUS_COLLECTED);
     * </code>
     *
     * @param string $status
     * @throws \InvalidArgumentException
     */
    public function setStatus($status)
    {
        // sanity check, status must be defined
        $reflectionClass = new \ReflectionClass(get_class());
        $constants       = $reflectionClass->getConstants();
        $isValid         = false;

        foreach ($constants as $constantName => $constantValue) {
            if (strtoupper(substr($constantName, 0, 7)) == 'STATUS_' && $constantValue == $status) {
                $isValid = true;
            }
        }

        if (!$isValid) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid status "%2$s"',
                    __METHOD__,
                    $status
                )
            );
        }

        $this->status = $status;
    }

    /**
     * This method is used to retrieve the status of a reservation. Returns a Dutch language string, which matches one
     * of the defined status constants.
     *
     * <code>
     * $status = $reservation->getStatus();
     * </code>
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * This method is used to set the show identifier of this reservation. It accepts one argument which, since I'm
     * not yet sure exactly what this is used for, can be anything. It returns nothing. This method may be removed in
     * the future.
     *
     * <code>
     * $reservation->setShowIdentifier('1234');
     * </code>
     *
     * @param string $showIdentifier
     */
    public function setShowIdentifier($showIdentifier)
    {
        $this->showId = $showIdentifier;
    }

    /**
     * This method is used to retrieve the show identifier of a reservation. May be removed in the future.
     *
     * <code>
     * $showIdentifier = $reservation->getShowIdentifier();
     * </code>
     *
     * @return string|null
     */
    public function getShowIdentifier()
    {
        return $this->showId;
    }

    /**
     * This method is used to set the reservation set identifier of this reservation. It accepts one argument which,
     * since I'm not yet sure exactly what this is used for, can be anything. It returns nothing. This method may be
     * removed in the future.
     *
     * <code>
     * $reservation->setReservationSetIdentifier('1234');
     * </code>
     *
     * @param string $reservationSetIdentifier
     */
    public function setReservationSetIdentifier($reservationSetIdentifier)
    {
        $this->reservationSetId = $reservationSetIdentifier;
    }

    /**
     * This method is used to retrieve the reservation set identifier of a reservation. May be removed in the future.
     *
     * <code>
     * $reservationSetId = $reservation->getReservationSetIdentifier();
     * </code>
     *
     * @return string|null
     */
    public function getReservationSetIdentifier()
    {
        return $this->reservationSetId;
    }

    /**
     * This method is used to set the collection number of this reservation. It accepts one argument which, since I'm
     * not yet sure exactly what this is used for, can be anything. It returns nothing. This method may be removed in
     * the future.
     *
     * <code>
     * $reservation->setCollectionNumber('1234');
     * </code>
     *
     * @param string $collectionNumber
     */
    public function setCollectionNumber($collectionNumber)
    {
        $this->collectNumber = $collectionNumber;
    }

    /**
     * This method is used to retrieve the collection number of a reservation. May be removed in the future.
     *
     * <code>
     * $collectionNumber = $reservation->getCollectionNumber();
     * </code>
     *
     * @return string|null
     */
    public function getCollectionNumber()
    {
        return $this->collectNumber;
    }

    /**
     * This method is used to set the date and time at which the tickets for this reservation were collected. It
     * accepts one argument, which must be a DateTime object, and returns nothing.
     *
     * <code>
     * $collectDateTime = new \DateTime('2014-01-01 12:00:00');
     * $reservation->setCollectDateTime($collectDateTime);
     * </code>
     *
     * @param \DateTime $collectDateTime
     */
    public function setCollectDateTime(\DateTime $collectDateTime)
    {
        $this->collectDateTime = clone $collectDateTime;
    }

    /**
     * This method returns a DateTime object indicating when the tickets for this reservation were collected, or null
     * if not known.
     *
     * <code>
     * $collectDateTime = $reservation->getCollectDateTime();
     * </code>
     *
     * @return \DateTime|null
     */
    public function getCollectDateTime()
    {
        $retValue = $this->collectDateTime;

        if ($retValue !== null) {
            $retValue = clone $retValue;
        }

        return $retValue;
    }
}