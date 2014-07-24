<?php
require_once(dirname(__FILE__) . '/../../init.php');

use Capirussa\Pathe\PersonalData;

/**
 * Tests Capirussa\Pathe\PersonalData
 *
 */
class PersonalDataTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $personalData = new PersonalData();

        $this->assertNull($this->getObjectAttribute($personalData, 'username'));
        $this->assertEquals(PersonalData::PASSWORD_NO_CHANGE, $this->getObjectAttribute($personalData, 'password'));
        $this->assertNull($this->getObjectAttribute($personalData, 'gender'));
        $this->assertNull($this->getObjectAttribute($personalData, 'firstName'));
        $this->assertNull($this->getObjectAttribute($personalData, 'middleName'));
        $this->assertNull($this->getObjectAttribute($personalData, 'lastName'));
        $this->assertNull($this->getObjectAttribute($personalData, 'emailAddress'));
        $this->assertNull($this->getObjectAttribute($personalData, 'country'));
        $this->assertNull($this->getObjectAttribute($personalData, 'birthDate'));
        $this->assertNull($this->getObjectAttribute($personalData, 'streetName'));
        $this->assertNull($this->getObjectAttribute($personalData, 'houseNumber'));
        $this->assertNull($this->getObjectAttribute($personalData, 'houseNumberSuffix'));
        $this->assertNull($this->getObjectAttribute($personalData, 'postalCode'));
        $this->assertNull($this->getObjectAttribute($personalData, 'city'));
        $this->assertNull($this->getObjectAttribute($personalData, 'mobilePhoneNumber'));
        $this->assertFalse($this->getObjectAttribute($personalData, 'newsletter'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetUsernameWithoutParameters()
    {
        $personalData = new PersonalData();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $personalData->setUsername();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must be a valid email address
     */
    public function testSetUsernameWithInvalidUsername()
    {
        $personalData = new PersonalData();

        $personalData->setUsername('invalidUsername');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must not be the same as the password
     */
    public function testSetUsernameWithUsernameSameAsPassword()
    {
        $personalData = new PersonalData();

        $personalData->setPassword('password1@example.com');
        $personalData->setUsername('password1@example.com');
    }

    public function testSetUsernameWithValidUsername()
    {
        $personalData = new PersonalData();

        $this->assertNull($this->getObjectAttribute($personalData, 'username'));

        $personalData->setUsername('test@example.com');

        $this->assertNotNull($this->getObjectAttribute($personalData, 'username'));
        $this->assertEquals('test@example.com', $this->getObjectAttribute($personalData, 'username'));

        $this->assertNotNull($personalData->getUsername());
        $this->assertEquals('test@example.com', $personalData->getUsername());
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetPasswordWithoutParameters()
    {
        $personalData = new PersonalData();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $personalData->setPassword();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage cannot be empty
     */
    public function testSetPasswordWithEmptyPassword()
    {
        $personalData = new PersonalData();

        $personalData->setPassword(null);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must be at least 6 characters
     */
    public function testSetPasswordWithTooShortPassword()
    {
        $personalData = new PersonalData();

        $personalData->setPassword('test');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must contain at least one number
     */
    public function testSetPasswordWithoutNumbers()
    {
        $personalData = new PersonalData();

        $personalData->setPassword('testPassword');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must not be the same as the email address
     */
    public function testSetPasswordSameAsEmailAddress()
    {
        $personalData = new PersonalData();

        $personalData->setEmailAddress('test1@example.com');
        $personalData->setPassword('test1@example.com');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must not be the same as the username
     */
    public function testSetPasswordSameAsUsername()
    {
        $personalData = new PersonalData();

        $personalData->setUsername('test2@example.com');
        $personalData->setPassword('test2@example.com');
    }

    public function testSetPasswordWithValidPassword()
    {
        $personalData = new PersonalData();

        $this->assertEquals(PersonalData::PASSWORD_NO_CHANGE, $this->getObjectAttribute($personalData, 'password'));

        $personalData->setPassword('testPassword123');

        $this->assertEquals('testPassword123', $this->getObjectAttribute($personalData, 'password'));
        $this->assertEquals('testPassword123', $personalData->getPassword());
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetGenderWithoutParameters()
    {
        $personalData = new PersonalData();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $personalData->setGender();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage unknown value
     */
    public function testSetGenderWithInvalidGender()
    {
        $personalData = new PersonalData();

        $personalData->setGender('invalidGender');
    }

    public function testSetGenderWithValidGender()
    {
        $personalData = new PersonalData();

        $this->assertNull($this->getObjectAttribute($personalData, 'gender'));

        $personalData->setGender(PersonalData::GENDER_FEMALE);

        $this->assertNotNull($this->getObjectAttribute($personalData, 'gender'));
        $this->assertEquals(PersonalData::GENDER_FEMALE, $this->getObjectAttribute($personalData, 'gender'));

        $this->assertNotNull($personalData->getGender());
        $this->assertEquals(PersonalData::GENDER_FEMALE, $personalData->getGender());
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetFirstNameWithoutParameters()
    {
        $personalData = new PersonalData();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $personalData->setFirstName();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage cannot be empty
     */
    public function testSetFirstNameWithEmptyName()
    {
        $personalData = new PersonalData();

        $personalData->setFirstName(null);
    }

    public function testSetFirstNameWithValidFirstName()
    {
        $personalData = new PersonalData();

        $this->assertNull($this->getObjectAttribute($personalData, 'firstName'));

        $personalData->setFirstName('testName');

        $this->assertNotNull($this->getObjectAttribute($personalData, 'firstName'));
        $this->assertEquals('testName', $this->getObjectAttribute($personalData, 'firstName'));

        $this->assertNotNull($personalData->getFirstName());
        $this->assertEquals('testName', $personalData->getFirstName());
    }

    public function testSetMiddleNameWithoutParameters()
    {
        $personalData = new PersonalData();

        $this->assertNull($this->getObjectAttribute($personalData, 'middleName'));

        $personalData->setMiddleName();

        $this->assertNull($this->getObjectAttribute($personalData, 'middleName'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must be a string
     */
    public function testSetMiddleNameWithInvalidName()
    {
        $personalData = new PersonalData();

        $personalData->setMiddleName(array('foo'));
    }

    public function testSetMiddleNameWithValidMiddleName()
    {
        $personalData = new PersonalData();

        $this->assertNull($this->getObjectAttribute($personalData, 'middleName'));

        $personalData->setMiddleName('testName');

        $this->assertNotNull($this->getObjectAttribute($personalData, 'middleName'));
        $this->assertEquals('testName', $this->getObjectAttribute($personalData, 'middleName'));

        $this->assertNotNull($personalData->getMiddleName());
        $this->assertEquals('testName', $personalData->getMiddleName());

        $personalData->setMiddleName('');

        $this->assertNull($this->getObjectAttribute($personalData, 'middleName'));
        $this->assertNull($personalData->getMiddleName());

        $personalData->setMiddleName('testName');

        $this->assertNotNull($this->getObjectAttribute($personalData, 'middleName'));
        $this->assertEquals('testName', $this->getObjectAttribute($personalData, 'middleName'));

        $this->assertNotNull($personalData->getMiddleName());
        $this->assertEquals('testName', $personalData->getMiddleName());

        $personalData->setMiddleName(null);

        $this->assertNull($this->getObjectAttribute($personalData, 'middleName'));
        $this->assertNull($personalData->getMiddleName());

        $personalData->setMiddleName('testName');

        $this->assertNotNull($this->getObjectAttribute($personalData, 'middleName'));
        $this->assertEquals('testName', $this->getObjectAttribute($personalData, 'middleName'));

        $this->assertNotNull($personalData->getMiddleName());
        $this->assertEquals('testName', $personalData->getMiddleName());

        $personalData->setMiddleName();

        $this->assertNull($this->getObjectAttribute($personalData, 'middleName'));
        $this->assertNull($personalData->getMiddleName());
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetLastNameWithoutParameters()
    {
        $personalData = new PersonalData();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $personalData->setLastName();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage cannot be empty
     */
    public function testSetLastNameWithEmptyName()
    {
        $personalData = new PersonalData();

        $personalData->setLastName(null);
    }

    public function testSetLastNameWithValidLastName()
    {
        $personalData = new PersonalData();

        $this->assertNull($this->getObjectAttribute($personalData, 'lastName'));

        $personalData->setLastName('testName');

        $this->assertNotNull($this->getObjectAttribute($personalData, 'lastName'));
        $this->assertEquals('testName', $this->getObjectAttribute($personalData, 'lastName'));

        $this->assertNotNull($personalData->getLastName());
        $this->assertEquals('testName', $personalData->getLastName());
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetEmailAddressWithoutParameters()
    {
        $personalData = new PersonalData();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $personalData->setEmailAddress();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid email address
     */
    public function testSetEmailAddressWithInvalidEmailAddress()
    {
        $personalData = new PersonalData();

        $personalData->setEmailAddress('invalidEmailAddress');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must not be the same as the password
     */
    public function testSetEmailAddressWithEmailAddressSameAsPassword()
    {
        $personalData = new PersonalData();

        $personalData->setPassword('password1@example.com');
        $personalData->setEmailAddress('password1@example.com');
    }

    public function testSetEmailAddressWithValidEmailAddress()
    {
        $personalData = new PersonalData();

        $this->assertNull($this->getObjectAttribute($personalData, 'emailAddress'));

        $personalData->setEmailAddress('test@example.com');

        $this->assertNotNull($this->getObjectAttribute($personalData, 'emailAddress'));
        $this->assertEquals('test@example.com', $this->getObjectAttribute($personalData, 'emailAddress'));

        $this->assertNotNull($personalData->getEmailAddress());
        $this->assertEquals('test@example.com', $personalData->getEmailAddress());
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetCountryWithoutParameters()
    {
        $personalData = new PersonalData();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $personalData->setCountry();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage unknown value
     */
    public function testSetCountryWithInvalidCountry()
    {
        $personalData = new PersonalData();

        $personalData->setCountry('invalidCountry');
    }

    public function testSetCountryWithValidCountry()
    {
        $personalData = new PersonalData();

        $this->assertNull($this->getObjectAttribute($personalData, 'country'));

        $personalData->setCountry(PersonalData::COUNTRY_NETHERLANDS);

        $this->assertNotNull($this->getObjectAttribute($personalData, 'country'));
        $this->assertEquals(PersonalData::COUNTRY_NETHERLANDS, $this->getObjectAttribute($personalData, 'country'));

        $this->assertNotNull($personalData->getCountry());
        $this->assertEquals(PersonalData::COUNTRY_NETHERLANDS, $personalData->getCountry());
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testSetBirthDateWithoutParameters()
    {
        $personalData = new PersonalData();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $personalData->setBirthDate();
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testSetBirthDateWithInvalidBirthDate()
    {
        $personalData = new PersonalData();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $personalData->setBirthDate('invalidBirthDate');
    }

    public function testSetBirthDateWithValidBirthDate()
    {
        $personalData = new PersonalData();

        $this->assertNull($this->getObjectAttribute($personalData, 'birthDate'));

        $birthDate = new \DateTime('1995-05-05');

        $personalData->setBirthDate($birthDate);

        $this->assertNotNull($this->getObjectAttribute($personalData, 'birthDate'));
        $this->assertInstanceOf('DateTime', $this->getObjectAttribute($personalData, 'birthDate'));
        $this->assertEquals('1995-05-05', $this->getObjectAttribute($personalData, 'birthDate')->format('Y-m-d'));

        $this->assertNotNull($personalData->getBirthDate());
        $this->assertInstanceOf('DateTime', $personalData->getBirthDate());
        $this->assertEquals('1995-05-05', $personalData->getBirthDate()->format('Y-m-d'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetBirthDayWithoutParameters()
    {
        $personalData = new PersonalData();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $personalData->setBirthDay();
    }

    public function testSetBirthDay()
    {
        $personalData = new PersonalData();

        $this->assertNull($this->getObjectAttribute($personalData, 'birthDate'));

        $birthDate = new \DateTime('1995-05-05');

        $personalData->setBirthDate($birthDate);

        $this->assertNotNull($this->getObjectAttribute($personalData, 'birthDate'));
        $this->assertInstanceOf('DateTime', $this->getObjectAttribute($personalData, 'birthDate'));
        $this->assertEquals('1995-05-05', $this->getObjectAttribute($personalData, 'birthDate')->format('Y-m-d'));

        $this->assertNotNull($personalData->getBirthDate());
        $this->assertInstanceOf('DateTime', $personalData->getBirthDate());
        $this->assertEquals('1995-05-05', $personalData->getBirthDate()->format('Y-m-d'));

        $personalData->setBirthDay(10);

        $this->assertEquals('1995-05-10', $this->getObjectAttribute($personalData, 'birthDate')->format('Y-m-d'));
        $this->assertEquals('1995-05-10', $personalData->getBirthDate()->format('Y-m-d'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetBirthMonthWithoutParameters()
    {
        $personalData = new PersonalData();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $personalData->setBirthMonth();
    }

    public function testSetBirthMonth()
    {
        $personalData = new PersonalData();

        $this->assertNull($this->getObjectAttribute($personalData, 'birthDate'));

        $birthDate = new \DateTime('1995-05-05');

        $personalData->setBirthDate($birthDate);

        $this->assertNotNull($this->getObjectAttribute($personalData, 'birthDate'));
        $this->assertInstanceOf('DateTime', $this->getObjectAttribute($personalData, 'birthDate'));
        $this->assertEquals('1995-05-05', $this->getObjectAttribute($personalData, 'birthDate')->format('Y-m-d'));

        $this->assertNotNull($personalData->getBirthDate());
        $this->assertInstanceOf('DateTime', $personalData->getBirthDate());
        $this->assertEquals('1995-05-05', $personalData->getBirthDate()->format('Y-m-d'));

        $personalData->setBirthMonth(10);

        $this->assertEquals('1995-10-05', $this->getObjectAttribute($personalData, 'birthDate')->format('Y-m-d'));
        $this->assertEquals('1995-10-05', $personalData->getBirthDate()->format('Y-m-d'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetBirthYearWithoutParameters()
    {
        $personalData = new PersonalData();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $personalData->setBirthYear();
    }

    public function testSetBirthYear()
    {
        $personalData = new PersonalData();

        $this->assertNull($this->getObjectAttribute($personalData, 'birthDate'));

        $birthDate = new \DateTime('1995-05-05');

        $personalData->setBirthDate($birthDate);

        $this->assertNotNull($this->getObjectAttribute($personalData, 'birthDate'));
        $this->assertInstanceOf('DateTime', $this->getObjectAttribute($personalData, 'birthDate'));
        $this->assertEquals('1995-05-05', $this->getObjectAttribute($personalData, 'birthDate')->format('Y-m-d'));

        $this->assertNotNull($personalData->getBirthDate());
        $this->assertInstanceOf('DateTime', $personalData->getBirthDate());
        $this->assertEquals('1995-05-05', $personalData->getBirthDate()->format('Y-m-d'));

        $personalData->setBirthYear(2005);

        $this->assertEquals('2005-05-05', $this->getObjectAttribute($personalData, 'birthDate')->format('Y-m-d'));
        $this->assertEquals('2005-05-05', $personalData->getBirthDate()->format('Y-m-d'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetStreetNameWithoutParameters()
    {
        $personalData = new PersonalData();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $personalData->setStreetName();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage cannot be empty
     */
    public function testSetStreetNameWithEmptyName()
    {
        $personalData = new PersonalData();

        $personalData->setStreetName(null);
    }

    public function testSetStreetNameWithValidStreetName()
    {
        $personalData = new PersonalData();

        $this->assertNull($this->getObjectAttribute($personalData, 'streetName'));

        $personalData->setStreetName('testStreet');

        $this->assertNotNull($this->getObjectAttribute($personalData, 'streetName'));
        $this->assertEquals('testStreet', $this->getObjectAttribute($personalData, 'streetName'));

        $this->assertNotNull($personalData->getStreetName());
        $this->assertEquals('testStreet', $personalData->getStreetName());
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetHouseNumberWithoutParameters()
    {
        $personalData = new PersonalData();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $personalData->setHouseNumber();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must be greater than zero
     */
    public function testSetHouseNumberWithEmptyHouseNumber()
    {
        $personalData = new PersonalData();

        $personalData->setHouseNumber(null);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must be greater than zero
     */
    public function testSetHouseNumberWithTooSmallHouseNumber()
    {
        $personalData = new PersonalData();

        $personalData->setHouseNumber(-15);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage and less than 1000
     */
    public function testSetHouseNumberWithTooBigHouseNumber()
    {
        $personalData = new PersonalData();

        $personalData->setHouseNumber(1015);
    }

    public function testSetHouseNumberWithValidHouseNumber()
    {
        $personalData = new PersonalData();

        $this->assertNull($this->getObjectAttribute($personalData, 'houseNumber'));

        $personalData->setHouseNumber(50);

        $this->assertNotNull($this->getObjectAttribute($personalData, 'houseNumber'));
        $this->assertEquals(50, $this->getObjectAttribute($personalData, 'houseNumber'));

        $this->assertNotNull($personalData->getHouseNumber());
        $this->assertEquals(50, $personalData->getHouseNumber());
    }

    public function testSetHouseNumberSuffixWithoutParameters()
    {
        $personalData = new PersonalData();

        $this->assertNull($this->getObjectAttribute($personalData, 'houseNumberSuffix'));

        $personalData->setHouseNumberSuffix();

        $this->assertNull($this->getObjectAttribute($personalData, 'houseNumberSuffix'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must be a string
     */
    public function testSetHouseNumberSuffixWithInvalidSuffix()
    {
        $personalData = new PersonalData();

        $personalData->setHouseNumberSuffix(array('foo'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must be less than 5 characters
     */
    public function testSetHouseNumberSuffixWithTooLongSuffix()
    {
        $personalData = new PersonalData();

        $personalData->setHouseNumberSuffix('testSuffix');
    }

    public function testSetHouseNumberSuffixWithValidSuffix()
    {
        $personalData = new PersonalData();

        $this->assertNull($this->getObjectAttribute($personalData, 'houseNumberSuffix'));

        $personalData->setHouseNumberSuffix('abc');

        $this->assertNotNull($this->getObjectAttribute($personalData, 'houseNumberSuffix'));
        $this->assertEquals('abc', $this->getObjectAttribute($personalData, 'houseNumberSuffix'));

        $this->assertNotNull($personalData->getHouseNumberSuffix());
        $this->assertEquals('abc', $personalData->getHouseNumberSuffix());

        $personalData->setHouseNumberSuffix('');

        $this->assertNull($this->getObjectAttribute($personalData, 'houseNumberSuffix'));
        $this->assertNull($personalData->getHouseNumberSuffix());

        $personalData->setHouseNumberSuffix('def');

        $this->assertNotNull($this->getObjectAttribute($personalData, 'houseNumberSuffix'));
        $this->assertEquals('def', $this->getObjectAttribute($personalData, 'houseNumberSuffix'));

        $this->assertNotNull($personalData->getHouseNumberSuffix());
        $this->assertEquals('def', $personalData->getHouseNumberSuffix());

        $personalData->setHouseNumberSuffix(null);

        $this->assertNull($this->getObjectAttribute($personalData, 'houseNumberSuffix'));
        $this->assertNull($personalData->getHouseNumberSuffix());

        $personalData->setHouseNumberSuffix('ghi');

        $this->assertNotNull($this->getObjectAttribute($personalData, 'houseNumberSuffix'));
        $this->assertEquals('ghi', $this->getObjectAttribute($personalData, 'houseNumberSuffix'));

        $this->assertNotNull($personalData->getHouseNumberSuffix());
        $this->assertEquals('ghi', $personalData->getHouseNumberSuffix());

        $personalData->setHouseNumberSuffix();

        $this->assertNull($this->getObjectAttribute($personalData, 'houseNumberSuffix'));
        $this->assertNull($personalData->getHouseNumberSuffix());
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetPostalCodeWithoutParameters()
    {
        $personalData = new PersonalData();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $personalData->setPostalCode();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid postal code
     */
    public function testSetPostalCodeWithInvalidPostalCode()
    {
        $personalData = new PersonalData();

        $personalData->setPostalCode('invalidPostalCode');
    }

    public function testSetPostalCodeWithValidPostalCode()
    {
        $personalData = new PersonalData();

        $this->assertNull($this->getObjectAttribute($personalData, 'postalCode'));

        $personalData->setPostalCode('1234ab');

        $this->assertNotNull($this->getObjectAttribute($personalData, 'postalCode'));
        $this->assertEquals('1234 AB', $this->getObjectAttribute($personalData, 'postalCode'));

        $this->assertNotNull($personalData->getPostalCode());
        $this->assertEquals('1234 AB', $personalData->getPostalCode());

        $personalData->setPostalCode('2345 CD');

        $this->assertNotNull($this->getObjectAttribute($personalData, 'postalCode'));
        $this->assertEquals('2345 CD', $this->getObjectAttribute($personalData, 'postalCode'));

        $this->assertNotNull($personalData->getPostalCode());
        $this->assertEquals('2345 CD', $personalData->getPostalCode());
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetCityWithoutParameters()
    {
        $personalData = new PersonalData();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $personalData->setCity();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage cannot be empty
     */
    public function testSetCityWithEmptyCity()
    {
        $personalData = new PersonalData();

        $personalData->setCity(null);
    }

    public function testSetCityWithValidCity()
    {
        $personalData = new PersonalData();

        $this->assertNull($this->getObjectAttribute($personalData, 'city'));

        $personalData->setCity('testCity');

        $this->assertNotNull($this->getObjectAttribute($personalData, 'city'));
        $this->assertEquals('testCity', $this->getObjectAttribute($personalData, 'city'));

        $this->assertNotNull($personalData->getCity());
        $this->assertEquals('testCity', $personalData->getCity());
    }

    public function testSetMobilePhoneNumberWithoutParameters()
    {
        $personalData = new PersonalData();

        $this->assertNull($this->getObjectAttribute($personalData, 'mobilePhoneNumber'));

        $personalData->setMobilePhoneNumber();

        $this->assertNull($this->getObjectAttribute($personalData, 'mobilePhoneNumber'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must be a string
     */
    public function testSetMobilePhoneNumberWithInvalidType()
    {
        $personalData = new PersonalData();

        $personalData->setMobilePhoneNumber(array('foo'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must be exactly 10 digits
     */
    public function testSetMobilePhoneNumberWithInvalidNumber()
    {
        $personalData = new PersonalData();

        $personalData->setMobilePhoneNumber('invalidNumber');
    }

    public function testSetMobilePhoneNumberWithValidNumber()
    {
        $personalData = new PersonalData();

        $this->assertNull($this->getObjectAttribute($personalData, 'mobilePhoneNumber'));

        $personalData->setMobilePhoneNumber('0612345678');

        $this->assertNotNull($this->getObjectAttribute($personalData, 'mobilePhoneNumber'));
        $this->assertEquals('0612345678', $this->getObjectAttribute($personalData, 'mobilePhoneNumber'));

        $this->assertNotNull($personalData->getMobilePhoneNumber());
        $this->assertEquals('0612345678', $personalData->getMobilePhoneNumber());

        $personalData->setMobilePhoneNumber('');

        $this->assertNull($this->getObjectAttribute($personalData, 'mobilePhoneNumber'));
        $this->assertNull($personalData->getMobilePhoneNumber());

        $personalData->setMobilePhoneNumber('0612345678');

        $this->assertNotNull($this->getObjectAttribute($personalData, 'mobilePhoneNumber'));
        $this->assertEquals('0612345678', $this->getObjectAttribute($personalData, 'mobilePhoneNumber'));

        $this->assertNotNull($personalData->getMobilePhoneNumber());
        $this->assertEquals('0612345678', $personalData->getMobilePhoneNumber());

        $personalData->setMobilePhoneNumber(null);

        $this->assertNull($this->getObjectAttribute($personalData, 'mobilePhoneNumber'));
        $this->assertNull($personalData->getMobilePhoneNumber());

        $personalData->setMobilePhoneNumber('0612345678');

        $this->assertNotNull($this->getObjectAttribute($personalData, 'mobilePhoneNumber'));
        $this->assertEquals('0612345678', $this->getObjectAttribute($personalData, 'mobilePhoneNumber'));

        $this->assertNotNull($personalData->getMobilePhoneNumber());
        $this->assertEquals('0612345678', $personalData->getMobilePhoneNumber());

        $personalData->setMobilePhoneNumber();

        $this->assertNull($this->getObjectAttribute($personalData, 'mobilePhoneNumber'));
        $this->assertNull($personalData->getMobilePhoneNumber());
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetNewsletterWithoutParameters()
    {
        $personalData = new PersonalData();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $personalData->setNewsletter();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must be boolean true or false
     */
    public function testSetNewsletterWithInvalidType()
    {
        $personalData = new PersonalData();

        $personalData->setNewsletter(null);
    }

    public function testSetNewsletterWithValidNewsletter()
    {
        $personalData = new PersonalData();

        $this->assertFalse($this->getObjectAttribute($personalData, 'newsletter'));

        $personalData->setNewsletter(true);

        $this->assertTrue($this->getObjectAttribute($personalData, 'newsletter'));
        $this->assertTrue($personalData->getNewsletter());
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testParsePersonalDataFromHtmlFormWithoutData()
    {
        /** @noinspection PhpParamsInspection (this is intentional) */
        PersonalData::parsePersonalDataFromHtmlForm();
    }

    public function testParsePersonalDataFromHtmlFormWithData()
    {
        /** @noinspection PhpParamsInspection (this is intentional) */
        $personalData = PersonalData::parsePersonalDataFromHtmlForm(file_get_contents(dirname(__FILE__) . '/mock/mock_30_logged_in.txt'));

        $this->assertInstanceOf('Capirussa\\Pathe\\PersonalData', $personalData);

        $this->assertEquals(PersonalData::PASSWORD_NO_CHANGE, $personalData->getPassword());
        $this->assertEquals(PersonalData::GENDER_MALE, $personalData->getGender());
        $this->assertEquals('testFirstName', $personalData->getFirstName());
        $this->assertEquals('testMiddleName', $personalData->getMiddleName());
        $this->assertEquals('testLastName', $personalData->getLastName());
        $this->assertEquals('test@example.com', $personalData->getEmailAddress());
        $this->assertEquals(PersonalData::COUNTRY_NETHERLANDS, $personalData->getCountry());

        $this->assertInstanceOf('DateTime', $personalData->getBirthDate());
        $this->assertEquals('1980-01-01', $personalData->getBirthDate()->format('Y-m-d'));

        $this->assertEquals('testStreet', $personalData->getStreetName());
        $this->assertEquals('1', $personalData->getHouseNumber());
        $this->assertEquals('a', $personalData->getHouseNumberSuffix());
        $this->assertEquals('1234 AB', $personalData->getPostalCode());
        $this->assertEquals('testCity', $personalData->getCity());
        $this->assertEquals('0612345678', $personalData->getMobilePhoneNumber());
        $this->assertTrue($personalData->getNewsletter());
    }
}