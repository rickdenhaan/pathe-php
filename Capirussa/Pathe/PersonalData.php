<?php
namespace Capirussa\Pathe;

class PersonalData
{
    /**
     * Form field names
     */
    const FORM_GENDER              = 'Gender';
    const FORM_FIRST_NAME          = 'FirstName';
    const FORM_MIDDLE_NAME         = 'MiddleName';
    const FORM_LAST_NAME           = 'LastName';
    const FORM_EMAIL               = 'Email';
    const FORM_COUNTRY             = 'CountryID';
    const FORM_BIRTH_DAY           = 'BirthDay';
    const FORM_BIRTH_MONTH         = 'BirthMonth';
    const FORM_BIRTH_YEAR          = 'BirthYear';
    const FORM_STREET_NAME         = 'Address1';
    const FORM_HOUSE_NUMBER        = 'HouseNbr';
    const FORM_HOUSE_NUMBER_SUFFIX = 'HouseNbrPostFix';
    const FORM_POSTAL_CODE         = 'ZIP';
    const FORM_CITY                = 'City';
    const FORM_MOBILE_PHONE_NUMBER = 'Mobile';
    const FORM_PASSWORD            = 'LoginPassword';
    const FORM_NEWSLETTER          = 'InfoWanted';

    /**
     * Gender values
     */
    const GENDER_FEMALE = 10273;
    const GENDER_MALE   = 10274;

    /**
     * Country values
     */
    const COUNTRY_NETHERLANDS = 11456;

    /**
     * Password default value indicating no change
     */
    const PASSWORD_NO_CHANGE = 'ticket.international';

    /**
     * The user's username
     *
     * @type string
     */
    protected $username;

    /**
     * The user's password
     *
     * @type string
     */
    protected $password = self::PASSWORD_NO_CHANGE;

    /**
     * The user's gender
     *
     * @type int
     */
    protected $gender = self::GENDER_MALE;

    /**
     * The user's first name
     *
     * @type string
     */
    protected $firstName;

    /**
     * The user's middle name
     *
     * @type string
     */
    protected $middleName;

    /**
     * The user's last name
     *
     * @type string
     */
    protected $lastName;

    /**
     * The user's email address
     *
     * @type string
     */
    protected $emailAddress;

    /**
     * The user's country
     *
     * @type int
     */
    protected $country = self::COUNTRY_NETHERLANDS;

    /**
     * The user's birth date
     *
     * @type \DateTime
     */
    protected $birthDate;

    /**
     * The user's street name
     *
     * @type string
     */
    protected $streetName;

    /**
     * The user's house number
     *
     * @type int
     */
    protected $houseNumber;

    /**
     * The user's house number suffix
     *
     * @type string
     */
    protected $houseNumberSuffix;

    /**
     * The user's postal code
     *
     * @type string
     */
    protected $postalCode;

    /**
     * The user's city
     *
     * @type string
     */
    protected $city;

    /**
     * The user's mobile phone number
     *
     * @type string
     */
    protected $mobilePhoneNumber;

    /**
     * Whether the user wants to receive the weekly newsletter
     *
     * @var bool
     */
    protected $newsletter = false;

    /**
     * Sets the username
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
     * Returns the username
     *
     * @return string|null
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Sets the user's password
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
     * Returns the password
     *
     * @return string|null
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Sets the user's gender
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
     * Returns the gender
     *
     * @return int|null
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Sets the user's first name
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
     * Returns the first name
     *
     * @return string|null
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Sets the user's middle name
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
     * Returns the middle name
     *
     * @return string|null
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * Sets the user's last name
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
     * Returns the last name
     *
     * @return string|null
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Sets the email address
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
     * Returns the email address
     *
     * @return string|null
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * Sets the user's country
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
     * Returns the country
     *
     * @return int|null
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Returns the birth date
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
     * Sets the full birth date
     *
     * @param \DateTime $birthDate
     */
    public function setBirthDate(\DateTime $birthDate)
    {
        $this->birthDate = $birthDate;
    }

    /**
     * Sets only the day of the birth date
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
     * Sets only the month of the birth date
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
     * Sets only the year of the birth date
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
     * Sets the user's street name
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
     * Returns the street name
     *
     * @return string|null
     */
    public function getStreetName()
    {
        return $this->streetName;
    }

    /**
     * Sets the user's house number
     *
     * @param int  $houseNumber
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
     * Returns the house number
     *
     * @return int|null
     */
    public function getHouseNumber()
    {
        return $this->houseNumber;
    }

    /**
     * Sets the user's house number suffix
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
     * Returns the house number suffix
     *
     * @return string|null
     */
    public function getHouseNumberSuffix()
    {
        return $this->houseNumberSuffix;
    }

    /**
     * Sets the user's postal code
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
     * Returns the postal code
     *
     * @return string|null
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Sets the user's city
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
     * Returns the city
     *
     * @return string|null
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Sets the user's mobile phone number
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
     * Returns the mobile phone number
     *
     * @return string|null
     */
    public function getMobilePhoneNumber()
    {
        return $this->mobilePhoneNumber;
    }

    /**
     * Sets the user's weekly newsletter preference
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
     * Returns the newsletter preference
     *
     * @return bool
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }

    /**
     * Creates a new PersonalData entity from the given HTML file containing an HTML form
     *
     * @param string $htmlFormString
     * @return PersonalData
     */
    public static function parsePersonalDataFromHtmlForm($htmlFormString)
    {
        $retValue = new static();

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
     * Makes sure all values that are required for registration and/or a personal information update are filled
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