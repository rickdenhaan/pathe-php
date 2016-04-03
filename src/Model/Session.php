<?php

namespace RickDenHaan\Pathe\Model;

/**
 * The Session model represents a Mijn Pathé user session
 *
 * @package RickDenHaan\Pathe\Model
 */
class Session
{
    /**
     * Contains the session id
     *
     * @type int
     */
    private $id;

    /**
     * Contains the user id
     *
     * @type int
     */
    private $userId;

    /**
     * Contains the session token
     *
     * @type string
     */
    private $sessionToken;

    /**
     * Sets the session's id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        if (!is_numeric($id)) {
            throw new \InvalidArgumentException("Invalid id: must be a number");
        }

        $this->id = intval($id, 10);

        return $this;
    }

    /**
     * Returns the session's id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the user's id
     *
     * @param int $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        if (!is_numeric($userId)) {
            throw new \InvalidArgumentException("Invalid user id: must be a number");
        }

        $this->userId = intval($userId, 10);

        return $this;
    }

    /**
     * Returns the user's id
     *
     * @return int|null
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Sets the session's token
     *
     * @param string $sessionToken
     * @return $this
     */
    public function setSessionToken($sessionToken)
    {
        if (!preg_match('/^[0-9a-z+\/=]+$/i', $sessionToken)) {
            throw new \InvalidArgumentException("Invalid session token");
        }

        $this->sessionToken = $sessionToken;

        return $this;
    }

    /**
     * Returns the session's token
     *
     * @return string
     */
    public function getSessionToken()
    {
        return $this->sessionToken;
    }
}