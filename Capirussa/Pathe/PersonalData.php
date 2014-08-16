<?php
namespace Capirussa\Pathe;

/**
 * The PersonalData object contains a customer's personal details and can be used to make changes.
 *
 * @package Capirussa\Pathe
 */
class PersonalData
{
    /**
     * The form field in the personal information dashboard that contains the customer's gender.
     */
    const FORM_GENDER = 'Gender';

    /**
     * The form field in the personal information dashboard that contains the customer's first name.
     */
    const FORM_FIRST_NAME = 'FirstName';

    /**
     * The form field in the personal information dashboard that contains the customer's middle name.
     */
    const FORM_MIDDLE_NAME = 'MiddleName';

    /**
     * The form field in the personal information dashboard that contains the customer's last name.
     */
    const FORM_LAST_NAME = 'LastName';

    /**
     * The form field in the personal information dashboard that contains the customer's email address.
     */
    const FORM_EMAIL = 'Email';

    /**
     * The form field in the personal information dashboard that contains the country identifier.
     */
    const FORM_COUNTRY = 'CountryID';

    /**
     * The form field in the personal information dashboard that contains the day portion of the birth date.
     */
    const FORM_BIRTH_DAY = 'BirthDay';

    /**
     * The form field in the personal information dashboard that contains the month portion of the birth date.
     */
    const FORM_BIRTH_MONTH = 'BirthMonth';

    /**
     * The form field in the personal information dashboard that contains the year portion of the birth date.
     */
    const FORM_BIRTH_YEAR = 'BirthYear';

    /**
     * The form field in the personal information dashboard that contains the name of the street on which the customer
     * lives.
     */
    const FORM_STREET_NAME = 'Address1';

    /**
     * The form field in the personal information dashboard that contains the house number portion of the customer's
     * address.
     */
    const FORM_HOUSE_NUMBER = 'HouseNbr';

    /**
     * The form field in the personal information dashboard that contains the suffix to the house number portion of
     * the customer's address.
     */
    const FORM_HOUSE_NUMBER_SUFFIX = 'HouseNbrPostFix';

    /**
     * The form field in the personal information dashboard that contains the customer's postal code.
     */
    const FORM_POSTAL_CODE = 'ZIP';

    /**
     * The form field in the personal information dashboard that contains the city in which the customer lives.
     */
    const FORM_CITY = 'City';

    /**
     * The form field in the personal information dashboard that contains the customer's mobile phone number.
     */
    const FORM_MOBILE_PHONE_NUMBER = 'Mobile';

    /**
     * The form field in the personal information dashboard that should be used when the customer wants to change
     * their password.
     */
    const FORM_PASSWORD = 'LoginPassword';

    /**
     * The checkbox in the personal information dashboard that contains the user's newsletter preference.
     */
    const FORM_NEWSLETTER = 'InfoWanted';

    /**
     * The Pathé identifier for the female gender.
     */
    const GENDER_FEMALE = 10273;

    /**
     * The Pathé identifier for the male gender.
     */
    const GENDER_MALE = 10274;

    /**
     * The Pathé country identifier for The Netherlands. Currently the only supported country.
     */
    const COUNTRY_NETHERLANDS = 11456;

    /**
     * The unique value to use when submitting changes to the customer's personal information if you do not also want
     * to change the password.
     */
    const PASSWORD_NO_CHANGE = 'ticket.international';

    /**
     * The username the customer currently has, or the username with which to register a new customer account.
     *
     * @type string
     */
    protected $username;

    /**
     * The customer's new password, if it needs to be changed. Otherwise, it defaults to the `PASSWORD_NO_CHANGE`
     * constant.
     *
     * @type string
     */
    protected $password = self::PASSWORD_NO_CHANGE;

    /**
     * The Pathé identifier for the customer's gender. Matches one of the `GENDER_*` constants defined in this class.
     *
     * @type int
     */
    protected $gender = self::GENDER_MALE;

    /**
     * The customer's first name.
     *
     * @type string
     */
    protected $firstName;

    /**
     * The customer's middle name.
     *
     * @type string
     */
    protected $middleName;

    /**
     * The customer's last name.
     *
     * @type string
     */
    protected $lastName;

    /**
     * The customer's email address.
     *
     * @type string
     */
    protected $emailAddress;

    /**
     * The Pathé identifier for the country in which the customer lives, matches one of the `COUNTRY_*` constants
     * defined in this class.
     *
     * @type int
     */
    protected $country = self::COUNTRY_NETHERLANDS;

    /**
     * A DateTime object referencing the customer's date of birth.
     *
     * @type \DateTime
     */
    protected $birthDate;

    /**
     * The street on which the customer lives.
     *
     * @type string
     */
    protected $streetName;

    /**
     * The house number portion of the customer's address.
     *
     * @type int
     */
    protected $houseNumber;

    /**
     * The suffix to the customer's house number.
     *
     * @type string
     */
    protected $houseNumberSuffix;

    /**
     * The customer's postal code.
     *
     * @type string
     */
    protected $postalCode;

    /**
     * The city in which the customer lives.
     *
     * @type string
     */
    protected $city;

    /**
     * The customer's mobile phone number.
     *
     * @type string
     */
    protected $mobilePhoneNumber;

    /**
     * A boolean indicating whether or not the customer wants to receive the weekly newsletter.
     *
     * @var bool
     */
    protected $newsletter = false;

    /**
     * This method is used to set the customer's username. It expects one argument, which must be a valid email
     * address. It returns nothing.
     *
     * <code>
     * $personalData->setUsername('username@example.com');
     * </code>
     *
     * @param string $username
     * @throws \InvalidArgumentException
     */
    public function setUsername($username)
    {
        // validate that the username is an email address
        if (!is_string($username) || !filter_var($username, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid username, the username must be a valid email address',
                    __METHOD__
                )
            );
        }

        // the username must not be the same as the password
        if (trim($username) == trim($this->getPassword())) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid username, the username must not be the same as the password',
                    __METHOD__
                )
            );
        }

        $this->username = $username;
    }

    /**
     * This method returns the customer's username. Returns null if the username is not set.
     *
     * <code>
     * $username = $personalData->getUsername();
     * </code>
     *
     * @return string|null
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * This method is used to set the user's password. It expects one argument, which must be at least six characters
     * long, contain at least one number and is not the same as the customer's email address or username. It returns
     * nothing.
     *
     * <code>
     * $personalData->setPassword('abcd123');
     * $personalData->setPassword(PersonalData::PASSWORD_NO_CHANGE);
     * </code>
     *
     * @param string $password
     * @throws \InvalidArgumentException
     */
    public function setPassword($password)
    {
        // the password must a string
        if (!is_string($password) || mb_strlen($password) == 0) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid password, it cannot be empty',
                    __METHOD__
                )
            );
        }

        // the password must be at least six characters
        if (mb_strlen($password) < 6) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid password, it must be at least 6 characters',
                    __METHOD__
                )
            );
        }

        // the password must contain at least one number
        if (!preg_match('/[0-9]/', $password)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid password, it must contain at least one number',
                    __METHOD__
                )
            );
        }

        // the password must not be the same as the email address
        if (trim($password) == trim($this->getEmailAddress())) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid password, it must not be the same as the email address',
                    __METHOD__
                )
            );
        }

        // the password must not be the same as the username
        if (trim($password) == trim($this->getUsername())) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid password, it must not be the same as the username',
                    __METHOD__
                )
            );
        }

        $this->password = $password;
    }

    /**
     * This method returns the customer's password **after** it has been changed. This will never return the
     * customer's current password. If the password has not been changed, it will return
     * `PersonalData::PASSWORD_NO_CHANGE`.
     *
     * <code>
     * $password = $personalData->getPassword();
     * </code>
     *
     * @return string|null
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * This method is used to set the customer's gender. It expects one argument, which must be one of the constants
     * defined in this class, and returns nothing.
     *
     * <code>
     * $personalData->setGender(PersonalData::GENDER_MALE);
     * </code>
     *
     * @param int $gender
     * @throws \InvalidArgumentException
     */
    public function setGender($gender)
    {
        $reflectionClass = new \ReflectionClass(get_class());
        $constants       = $reflectionClass->getConstants();
        $validGenders    = array();
        foreach ($constants as $constantName => $constantValue) {
            if (substr(strtoupper($constantName), 0, 7) == 'GENDER_') {
                $validGenders[] = $constantValue;
            }
        }

        // the country must be defined
        if (!in_array($gender, $validGenders)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid gender, unknown value',
                    __METHOD__
                )
            );
        }

        $this->gender = $gender;
    }

    /**
     * This method returns the identifier for the customer's gender, or null if it is not known.
     *
     * <code>
     * $gender = $personalData->getGender();
     * </code>
     *
     * @return int|null
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * This method is used to set the customer's first name. It expects one argument, which must be a non-empty
     * string. It returns nothing.
     *
     * <code>
     * $personalData->setFirstName('Bob');
     * </code>
     *
     * @param string $firstName
     * @throws \InvalidArgumentException
     */
    public function setFirstName($firstName)
    {
        // the first name must a string
        if (!is_string($firstName) || mb_strlen($firstName) == 0) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid first name, it cannot be empty',
                    __METHOD__
                )
            );
        }

        $this->firstName = $firstName;
    }

    /**
     * This method returns the customer's first name, or null if it is not known.
     *
     * <code>
     * $firstName = $personalData->getFirstName();
     * </code>
     *
     * @return string|null
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * This method is used to set the prefix for the customer's last name. It expects one optional argument, which
     * must be a string, and returns nothing.
     *
     * <code>
     * $personalData->setMiddleName('van der');
     * </code>
     *
     * @param string $middleName (Optional) Defaults to null
     * @throws \InvalidArgumentException
     */
    public function setMiddleName($middleName = null)
    {
        // the middle name must a string or null
        if (!is_string($middleName) && $middleName !== null) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid middle name, it must be a string',
                    __METHOD__
                )
            );
        }

        // sanity check
        if (trim($middleName) == '') {
            $middleName = null;
        }

        $this->middleName = $middleName;
    }

    /**
     * This method returns the prefix to the customer's last name (e.g. 'de', 'van' or 'van der') if any, or null if
     * it is not known.
     *
     * <code>
     * $middleName = $personalData->getMiddleName();
     * </code>
     *
     * @return string|null
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * This method is used to set the customer's last name. It expects one argument, which must be a non-empty string,
     * and returns nothing.
     *
     * <code>
     * $personalData->setLastName('Smit');
     * </code>
     *
     * @param string $lastName
     * @throws \InvalidArgumentException
     */
    public function setLastName($lastName)
    {
        // the last name must a string
        if (!is_string($lastName) || mb_strlen($lastName) == 0) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid last name, it cannot be empty',
                    __METHOD__
                )
            );
        }

        $this->lastName = $lastName;
    }

    /**
     * This method returns the customer's last name, or null if it is not known.
     *
     * <code>
     * $lastName = $personalData->getLastName();
     * </code>
     *
     * @return string|null
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * This method is used to set the customer's email address. It expects one argument which, obviously, must be a
     * valid email address. It returns nothing.
     *
     * <code>
     * $personalData->setEmailAddress('email@example.com');
     * </code>
     *
     * @param string $emailAddress
     * @throws \InvalidArgumentException
     */
    public function setEmailAddress($emailAddress)
    {
        // validate that the given value is an email address
        if (!is_string($emailAddress) || !filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid email address',
                    __METHOD__
                )
            );
        }

        // the email address must not be the same as the password
        if (trim($emailAddress) == trim($this->getPassword())) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid email address, the email address must not be the same as the password',
                    __METHOD__
                )
            );
        }

        $this->emailAddress = $emailAddress;
    }

    /**
     * This method returns the customer's email address, or null if it is not known.
     *
     * <code>
     * $emailAddress = $personalData->getEmailAddress();
     * </code>
     *
     * @return string|null
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * This method is used to set the identifier of the country where this customer lives. It expects one argument,
     * which must match one of the constants defined in this class. It returns nothing.
     *
     * <code>
     * $personalData->setCountry(PersonalData::COUNTRY_NETHERLANDS);
     * </code>
     *
     * @param int $country
     * @throws \InvalidArgumentException
     */
    public function setCountry($country)
    {
        $reflectionClass = new \ReflectionClass(get_class());
        $constants       = $reflectionClass->getConstants();
        $validCountries  = array();
        foreach ($constants as $constantName => $constantValue) {
            if (substr(strtoupper($constantName), 0, 8) == 'COUNTRY_') {
                $validCountries[] = $constantValue;
            }
        }

        // the country must be defined
        if (!in_array($country, $validCountries)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid country, unknown value',
                    __METHOD__
                )
            );
        }

        $this->country = $country;
    }

    /**
     * This method returns the identifier for the country in which the customer lives, or null if it is not known.
     *
     * <code>
     * $country = $personalData->getCountry();
     * </code>
     *
     * @return int|null
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * This method returns a DateTime object containing the customer's date of birth. If this date is not known, it
     * returns today.
     *
     * <code>
     * $birthDate = $personalData->getBirthDate();
     * </code>
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        // sanity check
        if ($this->birthDate === null) {
            $this->birthDate = new \DateTime();
        }

        return $this->birthDate;
    }

    /**
     * This method is used to set the customer's date of birth. It expects one argument, which must be a DateTime
     * object, and returns nothing.
     *
     * <code>
     * $birthDate = new \DateTime('1980-01-01');
     * $personalData->setBirthDate($birthDate);
     * </code>
     *
     * @param \DateTime $birthDate
     */
    public function setBirthDate(\DateTime $birthDate)
    {
        $this->birthDate = $birthDate;
    }

    /**
     * This method is used to set only the day portion of the customer's date of birth. It expects one argument,
     * which must be an integer between 1 and 31. This method returns nothing.
     *
     * <code>
     * $personalData->setBirthDay(15);
     * </code>
     *
     * @param int $day
     */
    public function setBirthDay($day)
    {
        $day = intval($day, 10);

        $birthDate = $this->getBirthDate();

        $birthDay   = $day;
        $birthMonth = $birthDate->format('n');
        $birthYear  = $birthDate->format('Y');

        $birthDate->setDate($birthYear, $birthMonth, $birthDay);

        $this->setBirthDate($birthDate);
    }

    /**
     * This method is used to set only the month portion of the customer's date of birth. It expects one argument,
     * which must be an integer between 1 and 12. This method returns nothing.
     *
     * <code>
     * $personalData->setBirthMonth(8);
     * </code>
     *
     * @param int $month
     */
    public function setBirthMonth($month)
    {
        $month = intval($month, 10);

        $birthDate = $this->getBirthDate();

        $birthDay   = $birthDate->format('j');
        $birthMonth = $month;
        $birthYear  = $birthDate->format('Y');

        $birthDate->setDate($birthYear, $birthMonth, $birthDay);

        $this->setBirthDate($birthDate);
    }

    /**
     * This method is used to set only the year portion of the customer's date of birth. It expects one argument,
     * which must be an integer between 1899 and the current year (at the time of writing: 2014). This method returns
     * nothing.
     *
     * <code>
     * $personalData->setBirthYear(1968);
     * </code>
     *
     * @param int $year
     */
    public function setBirthYear($year)
    {
        $year = intval($year, 10);

        $birthDate = $this->getBirthDate();

        $birthDay   = $birthDate->format('j');
        $birthMonth = $birthDate->format('n');
        $birthYear  = $year;

        $birthDate->setDate($birthYear, $birthMonth, $birthDay);

        $this->setBirthDate($birthDate);
    }

    /**
     * This method is used to set the street name portion of the customer's address. It expects one argument, which
     * must be a non-empty string. It returns nothing.
     *
     * <code>
     * $personalData->setStreetName('Hoofdstraat');
     * </code>
     *
     * @param string $streetName
     * @throws \InvalidArgumentException
     */
    public function setStreetName($streetName)
    {
        // the street name must a string
        if (!is_string($streetName) || mb_strlen($streetName) == 0) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid street name, it cannot be empty',
                    __METHOD__
                )
            );
        }

        $this->streetName = $streetName;
    }

    /**
     * This method returns the street portion of the customer's address, or null if it is not known.
     *
     * <code>
     * $streetName = $personalData->getStreetName();
     * </code>
     *
     * @return string|null
     */
    public function getStreetName()
    {
        return $this->streetName;
    }

    /**
     * This method is used to set the house number portion of the customer's address. It expects one argument, which
     * must be a positive integer between 1 and 999. It returns nothing.
     *
     * <code>
     * $personalData->setHouseNumber(15);
     * </code>
     *
     * @param int $houseNumber
     * @throws \InvalidArgumentException
     */
    public function setHouseNumber($houseNumber)
    {
        $houseNumber = intval($houseNumber, 10);

        // house number must not be empty
        if (intval($houseNumber, 10) < 1 || intval($houseNumber, 10) > 999) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid house number, it must be greater than zero and less than 1000',
                    __METHOD__
                )
            );
        }

        $this->houseNumber = $houseNumber;
    }

    /**
     * This method returns the house number portion of the customer's address, or null if it is not known.
     *
     * <code>
     * $houseNumber = $personalData->getHouseNumber();
     * </code>
     *
     * @return int|null
     */
    public function getHouseNumber()
    {
        return $this->houseNumber;
    }

    /**
     * This method is used to set the suffix to the house number, if the customer has one (e.g. if the customer's
     * address is 'Hoofdstraat 12 P' the suffix is 'P'). It expects one optional argument, which must be a string,
     * and returns nothing.
     *
     * <code>
     * $personalData->setHouseNumberSuffix('P');
     * </code>
     *
     * @param string $houseNumberSuffix (Optional) Defaults to null
     * @throws \InvalidArgumentException
     */
    public function setHouseNumberSuffix($houseNumberSuffix = null)
    {
        // the suffix must a string or null
        if (!is_string($houseNumberSuffix) && $houseNumberSuffix !== null) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid house number suffix, it must be a string',
                    __METHOD__
                )
            );
        }

        if (mb_strlen($houseNumberSuffix) > 4) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid house number suffix, it must be less than 5 characters',
                    __METHOD__
                )
            );
        }

        // sanity check
        if (trim($houseNumberSuffix) == '') {
            $houseNumberSuffix = null;
        }

        $this->houseNumberSuffix = $houseNumberSuffix;
    }

    /**
     * This method returns the suffix to the customer's house number, if any, or null if it is not known.
     *
     * <code>
     * $houseNumberSuffix = $personalData->getHouseNumberSuffix();
     * </code>
     *
     * @return string|null
     */
    public function getHouseNumberSuffix()
    {
        return $this->houseNumberSuffix;
    }

    /**
     * This method is used to set the postal code portion of the customer's address. It expects one argument, which
     * must be a valid Dutch postal code, and returns nothing.
     *
     * <code>
     * $personalData->setPostalCode('1234 AB');
     * $personalData->setPostalCode('1234ab');
     * </code>
     *
     * @param string $postalCode
     * @throws \InvalidArgumentException
     */
    public function setPostalCode($postalCode)
    {
        // the suffix must a string
        if (!is_string($postalCode) || !preg_match('/^[0-9]{4}\s?[a-z]{2}$/i', $postalCode)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid postal code, it must be four numbers and two letters',
                    __METHOD__
                )
            );
        }

        // uniformity check
        $postalCode = substr($postalCode, 0, 4) . ' ' . strtoupper(substr($postalCode, -2));

        $this->postalCode = $postalCode;
    }

    /**
     * This method returns the postal code portion of the customer's address, or null if it is not known.
     *
     * <code>
     * $postalCode = $personalData->getPostalCode();
     * </code>
     *
     * @return string|null
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * This method is used to set the name of the city where this customer lives. It expects one argument, which must
     * be a non-empty string, and returns nothing.
     *
     * <code>
     * $personalData->setCity('Rotterdam');
     * </code>
     *
     * @param string $city
     * @throws \InvalidArgumentException
     */
    public function setCity($city)
    {
        // the city must a string
        if (!is_string($city) || mb_strlen($city) == 0) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid city, it cannot be empty',
                    __METHOD__
                )
            );
        }

        $this->city = $city;
    }

    /**
     * This method returns the city in which the customer lives, or null if the city is not known.
     *
     * <code>
     * $city = $personalData->getCity();
     * </code>
     *
     * @return string|null
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * This method is used to set the customer's mobile phone number. It expects one optional argument, which must be
     * a valid 10-digit Dutch mobile phone number. It returns nothing.
     *
     * <code>
     * $personalData->setMobilePhoneNumber('0612345678');
     * </code>
     *
     * @param string $mobilePhoneNumber (Optional) Defaults to null
     * @throws \InvalidArgumentException
     */
    public function setMobilePhoneNumber($mobilePhoneNumber = null)
    {
        // the phone number must a string or null
        if (!is_string($mobilePhoneNumber) && $mobilePhoneNumber !== null) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid mobile phone number, it must be a string',
                    __METHOD__
                )
            );
        }

        // sanity check
        if (trim($mobilePhoneNumber) == '') {
            $mobilePhoneNumber = null;
        }

        if ($mobilePhoneNumber !== null && !preg_match('/^06[1-9]{1}[0-9]{7}$/', $mobilePhoneNumber)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid mobile phone number, it must be exactly 10 digits',
                    __METHOD__
                )
            );
        }

        $this->mobilePhoneNumber = $mobilePhoneNumber;
    }

    /**
     * This method returns the customer's mobile phone number, or null if it is not known.
     *
     * <code>
     * $phoneNumber = $personalData->getMobilePhoneNumber();
     * </code>
     *
     * @return string|null
     */
    public function getMobilePhoneNumber()
    {
        return $this->mobilePhoneNumber;
    }

    /**
     * This method is used to set whether or not the customer wants to receive the weekly newsletter. It expects one
     * argument, which must be a boolean `true` or `false`. It returns nothing.
     *
     * <code>
     * $personalData->setNewsletter(true);
     * </code>
     *
     * @param bool $newsletter
     * @throws \InvalidArgumentException
     */
    public function setNewsletter($newsletter)
    {
        // the value must be a boolean
        if (!is_bool($newsletter)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid newsletter preference, it must be boolean true or false',
                    __METHOD__
                )
            );
        }

        $this->newsletter = $newsletter;
    }

    /**
     * This method returns a boolean indicating whether the customer wants to receive the weekly Pathé newsletter. If
     * not known, false is assumed.
     *
     * <code>
     * $newsletter = $personalData->getNewsletter();
     * </code>
     *
     * @return bool
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }

    /**
     * This method expects one argument: an HTML form filled in with the customer's personal details. It will attempt
     * to parse this form and find all of the customer's details and will return a PersonalData object.
     *
     * <code>
     * $personalData = PersonalData::parsePersonalDataFromHtmlForm($htmlResponseWithPersonalDataForm);
     * </code>
     *
     * @param string $htmlFormString
     * @return PersonalData
     */
    public static function parsePersonalDataFromHtmlForm($htmlFormString)
    {
        $retValue = new static();
        /* @type $retValue PersonalData */

        // parse the document
        libxml_use_internal_errors(true);

        $dom = new \DOMDocument();
        $dom->loadHTML($htmlFormString);

        // get the document's forms
        $forms = $dom->getElementsByTagName('form');

        // the personal data is in the first form, get it
        $personalDataForm = $forms->item(0);
        /* @type $personalDataForm \DOMElement */

        // to make life more fun, the user's gender is set via Javascript
        $scripts      = $dom->getElementsByTagName('script');
        $genderScript = $scripts->item($scripts->length - 1);

        // parse the gender
        $genderMatches = array();
        preg_match('/document\.getElementById\(\'Gender_([0-9]*)\'\);/', $genderScript->textContent, $genderMatches);
        if (isset($genderMatches[1])) {
            $retValue->setGender($genderMatches[1]);
        }

        // get all inputs and selects
        $personalDataInputs  = $personalDataForm->getElementsByTagName('input');
        $personalDataSelects = $personalDataForm->getElementsByTagName('select');

        // parse the data from the inputs
        for ($idx = 0; $idx < $personalDataInputs->length; $idx++) {
            $input = $personalDataInputs->item($idx);
            /* @type $input \DOMElement */

            $inputName  = trim($input->getAttribute('name'));
            $inputValue = trim($input->getAttribute('value'));

            switch ($inputName) {
                case self::FORM_FIRST_NAME:
                    $retValue->setFirstName($inputValue);
                    break;

                case self::FORM_MIDDLE_NAME:
                    $retValue->setMiddleName($inputValue);
                    break;

                case self::FORM_LAST_NAME:
                    $retValue->setLastName($inputValue);
                    break;

                case self::FORM_EMAIL:
                    $retValue->setEmailAddress($inputValue);
                    break;

                case self::FORM_COUNTRY:
                    $retValue->setCountry($inputValue);
                    break;

                case self::FORM_STREET_NAME:
                    $retValue->setStreetName($inputValue);
                    break;

                case self::FORM_HOUSE_NUMBER:
                    $retValue->setHouseNumber($inputValue, false);
                    break;

                case self::FORM_HOUSE_NUMBER_SUFFIX:
                    $retValue->setHouseNumberSuffix($inputValue);
                    break;

                case self::FORM_POSTAL_CODE:
                    $retValue->setPostalCode($inputValue, false);
                    break;

                case self::FORM_CITY:
                    $retValue->setCity($inputValue);
                    break;

                case self::FORM_MOBILE_PHONE_NUMBER:
                    $retValue->setMobilePhoneNumber($inputValue);
                    break;

                case self::FORM_NEWSLETTER:
                    $retValue->setNewsletter($input->getAttribute('checked') == 'checked');
                    break;
            }
        }

        // parse the data from the selects
        for ($idx = 0; $idx < $personalDataSelects->length; $idx++) {
            $select = $personalDataSelects->item($idx);
            /* @type $select \DOMElement */

            $selectName    = trim($select->getAttribute('name'));
            $selectOptions = $select->getElementsByTagName('option');

            $selectValue = '';
            if ($selectOptions->length > 0) {
                $firstOption = $selectOptions->item(0);
                /* @type $firstOption \DOMElement */
                $selectValue = $firstOption->getAttribute('value');

                for ($odx = 0; $odx < $selectOptions->length; $odx++) {
                    $option = $selectOptions->item($odx);
                    /* @type $option \DOMElement */

                    if ($option->getAttribute('selected') == 'selected') {
                        $selectValue = $option->getAttribute('value');
                    }
                }
            }

            switch ($selectName) {
                case self::FORM_BIRTH_DAY:
                    $retValue->setBirthDay($selectValue);
                    break;

                case self::FORM_BIRTH_MONTH:
                    $retValue->setBirthMonth($selectValue);
                    break;

                case self::FORM_BIRTH_YEAR:
                    $retValue->setBirthYear($selectValue);
                    break;
            }
        }

        return $retValue;
    }

    /**
     * This method checks whether all required fields are filled in, so that the contents of this instance are safe to
     * use for registering a new account (via `Client::registerAccount()`) or updating an existing account (via
     * `Client::updatePersonalData()`). It accepts one argument, which is a boolean that defaults to `false`. If it is
     * set to `true`, that means validation for a new account registration is done, meaning the username and password
     * are required and the password must not match `PersonalData::PASSWORD_NO_CHANGE`. This method returns nothing,
     * but throws a LogicException for any validation errors it comes across.
     *
     * <code>
     * $personalData->assertValidForRegistrationAndUpdate(true);
     * </code>
     *
     * @param bool $newRegistration if TRUE will make sure the username is set and the password is not set to the NO_CHANGE password
     * @throws \LogicException
     */
    public function assertValidForRegistrationAndUpdate($newRegistration = false)
    {
        if (trim($this->getFirstName()) == '') {
            throw new \LogicException(
                sprintf(
                    '%1$s: Validation error: first name is not set',
                    __METHOD__
                )
            );
        }

        if (trim($this->getLastName()) == '') {
            throw new \LogicException(
                sprintf(
                    '%1$s: Validation error: last name is not set',
                    __METHOD__
                )
            );
        }

        if (trim($this->getEmailAddress()) == '') {
            throw new \LogicException(
                sprintf(
                    '%1$s: Validation error: email address is not set',
                    __METHOD__
                )
            );
        }

        if (intval($this->getBirthDate()->setTimezone(new \DateTimeZone(date_default_timezone_get()))->format('Ymd')) >= intval(date('Ymd'))) {
            throw new \LogicException(
                sprintf(
                    '%1$s: Validation error: birth date must be in the past',
                    __METHOD__
                )
            );
        }

        if ($newRegistration && trim($this->getUsername()) == '') {
            throw new \LogicException(
                sprintf(
                    '%1$s: Validation error: username is not set',
                    __METHOD__
                )
            );
        }

        if (trim($this->getPassword()) == '' || ($newRegistration && $this->getPassword() == PersonalData::PASSWORD_NO_CHANGE)) {
            throw new \LogicException(
                sprintf(
                    '%1$s: Validation error: password is not set',
                    __METHOD__
                )
            );
        }

        if (trim($this->getStreetName()) == '') {
            throw new \LogicException(
                sprintf(
                    '%1$s: Validation error: street name is not set',
                    __METHOD__
                )
            );
        }

        if (trim($this->getHouseNumber()) == '') {
            throw new \LogicException(
                sprintf(
                    '%1$s: Validation error: house number is not set',
                    __METHOD__
                )
            );
        }

        if (trim($this->getPostalCode()) == '') {
            throw new \LogicException(
                sprintf(
                    '%1$s: Validation error: postal code is not set',
                    __METHOD__
                )
            );
        }

        if (trim($this->getCity()) == '') {
            throw new \LogicException(
                sprintf(
                    '%1$s: Validation error: city is not set',
                    __METHOD__
                )
            );
        }
    }
}