Pathe PHP client
================

[![Build Status](https://travis-ci.org/rickdenhaan/pathe-php.png?branch=master)](https://travis-ci.org/rickdenhaan/pathe-php)
[![Coverage Status](https://coveralls.io/repos/rickdenhaan/pathe-php/badge.png?branch=master)](https://coveralls.io/r/rickdenhaan/pathe-php)

This PHP client connects to the Pathé Client Panel at [onlinetickets2.pathe.nl](https://onlinetickets2.pathe.nl/ticketweb.php?sign=30&UserCenterId=1) to retrieve information about an Unlimited or Gold Member's history.

Many thanks to [Robbert Noordzij](https://github.com/robbertnoordzij) for his efforts in getting this project started!


Usage
-----

```php
use Capirussa\Pathe;

try {
    $client = new Pathe\Client('username', 'password');

    $movieHistory = $client->findCustomerHistory();
} catch (\Exception $exception) {
    // something went wrong, fix it and try again!
}
```


Pathe\Client
------------

You need to supply your Mijn Pathé username and password when initializing the Pathe\Client.

On some servers, usually localhost development servers, you might encounter SSL errors. To skip the SSL validation, call $client->disableSslVerification().

For now, the only thing you can fetch is your movie history, by calling $client->findCustomerHistory(). This will return an array of HistoryItem entities.


Pathe\HistoryItem
-----------------

The Pathe\HistoryItem entity contains three properties:

* ShowTime - a DateTime object containing the date and time at which the movie started
* Screen - a Screen object containing the theater and screen at which the movie played
* Event - an Event object containing the name of the movie that you watched


Pathe\Screen
------------

The Pathe\Screen entity contains two properties:

* Theater - the name of the theater at which the movie played
* Screen - the screen on which the movie played


Pathe\Event
-----------

The Pathe\Event entity for now only contains the name of the movie you watched:

* MovieName - the name of the movie that you watched

If you find any bugs or have any feature requests, please raise an issue on Github or fork this project and create a pull request.

Happy coding!
