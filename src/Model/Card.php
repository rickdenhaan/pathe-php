<?php

namespace RickDenHaan\Pathe\Model;

use RickDenHaan\Pathe\Client;

/**
 * The Card model represents an Unlimited or Unlimited Gold card
 *
 * @package RickDenHaan\Pathe\Model
 */
class Card
{
    /**
     * Contains the card id (as a string)
     *
     * @type string
     */
    private $id;

    /**
     * Contains the card type
     *
     * @type string
     */
    private $type;

    /**
     * Contains the card's status
     *
     * @type string
     */
    private $status;

    /**
     * Contains the reason for the card's status, if applicable
     *
     * @type string
     */
    private $statusReason;

    /**
     * Contains the card's start date
     *
     * @type \DateTime
     */
    private $startDate;

    /**
     * Contains the card's expiry date, if the contract is going to expire, or null
     * if the contract is ongoing
     *
     * @type \DateTime|null
     */
    private $expiryDate;

    /**
     * Contains the card's PIN code as a string (because it may have leading zeroes)
     *
     * @type string
     */
    private $pinCode;

    /**
     * Sets the card's id
     *
     * @param string|int $id
     * @return $this
     */
    public function setId($id)
    {
        if (!is_numeric($id)) {
            throw new \InvalidArgumentException("Invalid id: must be numeric");
        }

        $this->id = trim(strval($id));

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
     * Sets the card's type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        if (!is_string($type)) {
            throw new \InvalidArgumentException("Invalid type: must be a string");
        }

        $this->type = strtolower(trim($type));

        return $this;
    }

    /**
     * Returns the card's type
     *
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the card's status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        if (!is_string($status)) {
            throw new \InvalidArgumentException("Invalid status: must be a string");
        }

        $this->status = trim($status);

        return $this;
    }

    /**
     * Returns the card's status
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the reason for the card's status
     *
     * @param string $statusReason
     * @return $this
     */
    public function setStatusReason($statusReason)
    {
        if ($statusReason !== null && !is_string($statusReason)) {
            throw new \InvalidArgumentException("Invalid status reason: must be a string");
        }

        $this->statusReason = trim($statusReason);

        // sanity check: set to null in case of an empty string
        if ($this->statusReason == '') {
            $this->statusReason = null;
        }

        return $this;
    }

    /**
     * Returns the reason for the card's status
     *
     * @return string|null
     */
    public function getStatusReason()
    {
        return $this->statusReason;
    }

    /**
     * Sets the start date for the card
     *
     * @param \DateTime $startDate
     * @return $this
     */
    public function setStartDate(\DateTime $startDate)
    {
        // safety check, make sure we have a copy of $startDate instead of a reference to a shared object
        $startDate = clone $startDate;

        // sanity check, we only need the date
        $startDate->setTime(0, 0, 0);

        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Returns the card's start date
     *
     * @return \DateTime|null
     */
    public function getStartDate()
    {
        if ($this->startDate === null) {
            return null;
        } else {
            // return a copy of the start date instead of a reference
            $clone = clone $this->startDate;

            return $clone;
        }
    }

    /**
     * Sets the expiry date for the card
     *
     * @param \DateTime|null $expiryDate
     * @return $this
     */
    public function setExpiryDate(\DateTime $expiryDate = null)
    {
        if ($expiryDate === null) {
            $this->expiryDate = null;
        }

        // safety check, make sure we have a copy of $expiryDate instead of a reference to a shared object
        $expiryDate = clone $expiryDate;

        // sanity check, we only need the date
        $expiryDate->setTime(0, 0, 0);

        $this->expiryDate = $expiryDate;

        return $this;
    }

    /**
     * Returns the card's expiry date, if any
     *
     * @return \DateTime|null
     */
    public function getExpiryDate()
    {
        if ($this->expiryDate === null) {
            return null;
        } else {
            // return a copy of the expiry date instead of a reference
            $clone = clone $this->expiryDate;

            return $clone;
        }
    }

    /**
     * Sets the card's PIN code
     *
     * @param string|int $pinCode
     * @return $this
     */
    public function setPinCode($pinCode)
    {
        if (!is_numeric($pinCode)) {
            throw new \InvalidArgumentException("Invalid PIN code: must be numeric");
        }

        $this->pinCode = trim(strval($pinCode));

        return $this;
    }

    /**
     * Returns the card's PIN code
     *
     * @return string
     * @throws \Exception
     */
    public function getPinCode()
    {
        if ($this->pinCode === null && $this->getId() !== null) {
            $client = Client::getInstance();

            if ($client->getSession() === null) {
                $client->createSession();
            }

            $result = $client->get(
                sprintf(
                    "users/%d/cards/%s/pin",
                    $client->getSession()->getUserId(),
                    $this->getId()
                ),
                200
            );

            $this->pinCode = $result['pin'];
        }

        return $this->pinCode;
    }

    /**
     * Retrieves all cards for the current user
     *
     * @return static[]
     * @throws \Exception
     */
    public static function getAll()
    {
        $retValue = array();

        $client = Client::getInstance();

        if ($client->getSession() === null) {
            $client->createSession();
        }

        $result = $client->get(
            sprintf(
                "users/%d/cards",
                $client->getSession()->getUserId()
            ),
            200
        );

        foreach ($result['cards'] as $cardData) {
            $card = new static();
            $card->setId($cardData['id']);
            $card->setType($cardData['cardType']);
            $card->setStatus($cardData['state']);
            $card->setStatusReason($cardData['stateComment']);
            $card->setStartDate(\DateTime::createFromFormat('d-m-Y', $cardData['startDate']));
            $card->setExpiryDate(\DateTime::createFromFormat('d-m-Y', $cardData['expiryDate']));

            $retValue[] = $card;
        }

        return $retValue;
    }
}