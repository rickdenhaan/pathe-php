<?php

namespace RickDenHaan\Pathe\Model;

/**
 * The Seat model represents a seat in a theater
 *
 * @package RickDenHaan\Pathe\Model
 */
class Seat
{
    /**
     * Contains the row number as a string, in case of theaters where rows are labeled A-Z
     *
     * @type string
     */
    private $row;

    /**
     * Contains the seat name, to support special seats as well as numbered seats
     *
     * @type string
     */
    private $seat;

    /**
     * Sets the seat's row
     *
     * @param string|int $row
     * @return $this
     */
    public function setRow($row)
    {
        $this->row = trim(strval($row));

        return $this;
    }

    /**
     * Returns the seat's row
     *
     * @return string|null
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * Sets the seat's number
     *
     * @param string|int $seat
     * @return $this
     */
    public function setSeat($seat)
    {
        $this->seat = trim(strval($seat));

        return $this;
    }

    /**
     * Returns the seat's number
     *
     * @return string|null
     */
    public function getSeat()
    {
        return $this->seat;
    }
}