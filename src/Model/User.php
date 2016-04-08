<?php

namespace RickDenHaan\Pathe\Model;

use RickDenHaan\Pathe\Client;

/**
 * The User model represents a Mijn Pathé user
 *
 * @package RickDenHaan\Pathe\Model
 */
class User
{
    /**
     * Requests the password reset instructions from Pathé and returns a boolean indicating success
     *
     * @return bool
     */
    public static function forgotPassword()
    {
        $client = Client::getInstance();

        try {
            $client->post(
                "users/resetpassword",
                array(
                    'email' => $client->getUsername(),
                ),
                202
            );
        } catch (\Exception $ex) {
            return false;
        }

        return true;
    }
}