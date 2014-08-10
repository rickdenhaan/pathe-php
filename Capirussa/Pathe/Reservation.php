<?php
namespace Capirussa\Pathe;

class Reservation
{
    /**
     * Possible reservation statuses
     */
    const STATUS_UNKNOWN   = 'Onbekend';
    const STATUS_COLLECTED = 'Opgehaald';
    const STATUS_DELETED   = 'Verwijderd';

    /**
     * Number of tickets
     *
     * @type int
     */
    protected $ticketCount = 0;

    /**
     * Reservation status
     *
     * @type string
     */
    protected $status = self::STATUS_UNKNOWN;

    /**
     * Show identifier
     *
     * @type string
     */
    protected $showId;

    /**
     * Reservation set identifier
     *
     * @type string
     */
    protected $reservationSetId;

    /**
     * Collection number
     *
     * @type string
     */
    protected $collectNumber;

    /**
     * Date/time the tickets for this reservation were picked up
     *
     * @type \DateTime
     */
    protected $collectDateTime;

    /**
     * Sets the number of tickets
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
     * Returns the ticket count
     *
     * @return int
     */
    public function getTicketCount()
    {
        return $this->ticketCount;
    }

    /**
     * Sets the reservation status
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
     * Returns the reservation status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the show identifier
     *
     * @param string $showIdentifier
     */
    public function setShowIdentifier($showIdentifier)
    {
        $this->showId = $showIdentifier;
    }

    /**
     * Returns the show identifier
     *
     * @return string|null
     */
    public function getShowIdentifier()
    {
        return $this->showId;
    }

    /**
     * Sets the reservation set identifier
     *
     * @param string $reservationSetIdentifier
     */
    public function setReservationSetIdentifier($reservationSetIdentifier)
    {
        $this->reservationSetId = $reservationSetIdentifier;
    }

    /**
     * Returns the reservation set identifier
     *
     * @return string|null
     */
    public function getReservationSetIdentifier()
    {
        return $this->reservationSetId;
    }

    /**
     * Sets the collection number
     *
     * @param string $collectionNumber
     */
    public function setCollectionNumber($collectionNumber)
    {
        $this->collectNumber = $collectionNumber;
    }

    /**
     * Returns the collection number
     *
     * @return string|null
     */
    public function getCollectionNumber()
    {
        return $this->collectNumber;
    }

    /**
     * Sets the date/time at which the tickets for this reservation were picked up
     *
     * @param \DateTime $collectDateTime
     */
    public function setCollectDateTime(\DateTime $collectDateTime)
    {
        $this->collectDateTime = clone $collectDateTime;
    }

    /**
     * Returns the date/time at which the tickets for this reservation were picked up
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