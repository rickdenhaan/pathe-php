<?php

namespace RickDenHaan\Pathe\Model;

use RickDenHaan\Pathe\Client;

/**
 * The Ticket model represents a single ticket in a reservation
 *
 * @package RickDenHaan\Pathe\Model
 */
class Ticket
{
    /**
     * Contains the ticket id
     *
     * @type int
     */
    private $id;

    /**
     * Contains the type of ticket
     *
     * @type string
     */
    private $type;

    /**
     * Contains the name of the card owner for this ticket
     *
     * @type string
     */
    private $ownerName;

    /**
     * Contains the price for this ticket in Euros
     *
     * @type float
     */
    private $price;

    /**
     * Indicates whether this ticket can still be cancelled
     *
     * @type bool
     */
    private $cancelable;

    /**
     * Contains the transaction's status
     *
     * @type string
     */
    private $status;

    /**
     * Contains the seats for this ticket
     *
     * @type Seat[]
     */
    private $seats = array();

    /**
     * Sets the card's id
     *
     * @param string|int $id
     * @return $this
     */
    public function setId($id = null)
    {
        if ($id === null) {
            $this->id = null;
        } else {
            if (!is_numeric($id)) {
                throw new \InvalidArgumentException("Invalid id: must be numeric");
            }

            $this->id = trim(strval($id));
        }

        return $this;
    }

    /**
     * Returns the card's id
     *
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the ticket type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type = null)
    {
        if ($type === null) {
            $this->type = null;
        } else {
            if (!is_string($type)) {
                throw new \InvalidArgumentException("Invalid type: must be a string");
            }

            $this->type = trim($type);
        }

        return $this;
    }

    /**
     * Returns the ticket type
     *
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the owner name
     *
     * @param string $ownerName
     * @return $this
     */
    public function setOwnerName($ownerName = null)
    {
        if ($ownerName === null) {
            $this->ownerName = null;
        } else {
            if (!is_string($ownerName)) {
                throw new \InvalidArgumentException("Invalid owner name: must be a string");
            }

            $this->ownerName = trim($ownerName);
        }

        return $this;
    }

    /**
     * Returns the owner name
     *
     * @return string|null
     */
    public function getOwnerName()
    {
        return $this->ownerName;
    }

    /**
     * Sets the ticket price
     *
     * @param float $price
     * @return $this
     */
    public function setPrice($price = null)
    {
        if ($price === null) {
            $this->price = null;
        } else {
            if (!is_numeric($price)) {
                throw new \InvalidArgumentException("Invalid price: must be a number");
            }

            $this->price = floatval($price);
        }

        return $this;
    }

    /**
     * Returns the ticket price
     *
     * @return float|null
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Sets whether the ticket can be cancelled
     *
     * @param bool $cancelable
     * @return $this
     */
    public function setCancelable($cancelable = null)
    {
        if ($cancelable === null) {
            $this->cancelable = null;
        } else {
            if (!is_bool($cancelable)) {
                throw new \InvalidArgumentException("Invalid cancelable: must be a boolean");
            }

            $this->cancelable = $cancelable;
        }

        return $this;
    }

    /**
     * Returns whether the ticket can be cancelled
     *
     * @return bool|null
     */
    public function getCancelable()
    {
        return $this->cancelable;
    }

    /**
     * Sets the ticket's status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status = null)
    {
        if ($status === null) {
            $this->status = null;
        } else {
            if (!is_string($status)) {
                throw new \InvalidArgumentException("Invalid status: must be a string");
            }

            $this->status = trim($status);
        }

        return $this;
    }

    /**
     * Returns the ticket's status
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the list of seats for this ticket
     *
     * @param Seat[] $seats
     * @return $this
     */
    public function setSeats(array $seats = array())
    {
        $this->seats = $seats;

        return $this;
    }

    /**
     * Adds a single seat to the list of seats for this ticket
     *
     * @param Seat $seat
     * @return $this
     */
    public function addSeat(Seat $seat)
    {
        $this->seats[] = $seat;

        return $this;
    }

    /**
     * Returns the list of seats for this ticket
     *
     * @return Seat[]
     */
    public function getSeats()
    {
        return $this->seats;
    }

    /**
     * Retrieves all tickets for the supplied reservation.
     *
     * @param Reservation $reservation
     * @param Session     $session (Optional) Defaults to null
     * @return static[]
     * @throws \Exception
     */
    public static function getAll(Reservation $reservation, Session $session = null)
    {
        $retValue = array();

        $ownSession = false;
        if ($session === null) {
            $session    = Session::create();
            $ownSession = true;
        }

        try {
            $result = Client::getInstance()
                            ->get(
                                sprintf(
                                    "users/%d/transactions/%s",
                                    $session->getUserId(),
                                    $reservation->getId()
                                ),
                                200,
                                $session
                            );
        } catch (\Exception $ex) {
            if ($ownSession) {
                $session->close();
            }

            throw $ex;
        }

        foreach ($result['tickets'] as $ticketData) {
            $ticket = new static();
            $ticket->setId($ticketData['id']);
            $ticket->setOwnerName($ticketData['cardOwner']);
            $ticket->setType($ticketData['name']);
            $ticket->setPrice($ticketData['price'] === null ? null : ($ticketData['price'] / 100));
            $ticket->setCancelable($ticketData['canCancel']);
            $ticket->setStatus($ticketData['status']);

            foreach ($ticketData['seats'] as $seatData) {
                $seat = new Seat();
                $seat->setRow($seatData['row'])
                     ->setSeat($seatData['name']);

                $ticket->addSeat($seat);
            }

            $retValue[] = $ticket;
        }

        if ($ownSession) {
            $session->close();
        }

        return $retValue;
    }
}