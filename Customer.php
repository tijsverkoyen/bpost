<?php
namespace TijsVerkoyen\Bpost;

/**
 * bPost Customer class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class Customer
{
    /**
     * Generic info
     *
     * @var string
     */
    private $firstName, $lastName, $email, $phoneNumber;

    /**
     * The address
     *
     * @var Address
     */
    private $deliveryAddress;

    /**
     * Create a customer
     *
     * @param string $firstName
     * @param string $lastName
     */
    public function __construct($firstName, $lastName)
    {
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
    }

    /**
     * Get the delivery address
     *
     * @return Address
     */
    public function getDeliveryAddress()
    {
        return $this->deliveryAddress;
    }

    /**
     * Get the email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get the first name
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Get the last name
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Get the phone number
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set the delivery address
     *
     * @param Address $deliveryAddress
     */
    public function setDeliveryAddress($deliveryAddress)
    {
        $this->deliveryAddress = $deliveryAddress;
    }

    /**
     * Set the email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        if(mb_strlen($email) > 50) throw new Exception('Invalid length for email, maximum is 50.');
        $this->email = $email;
    }

    /**
     * Set the first name
     *
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        if(mb_strlen($firstName) > 40) throw new Exception('Invalid length for firstName, maximum is 40.');
        $this->firstName = $firstName;
    }

    /**
     * Set the last name
     *
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        if(mb_strlen($lastName) > 40) throw new Exception('Invalid length for lastName, maximum is 40.');
        $this->lastName = $lastName;
    }

    /**
     * Set the phone number
     *
     * @param string $phoneNumber
     */
    public function setPhoneNumber($phoneNumber)
    {
        if(mb_strlen($phoneNumber) > 20) throw new Exception('Invalid length for phone number, maximum is 20.');
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * Return the object as an array for usage in the XML
     *
     * @return array
     */
    public function toXMLArray()
    {
        $data = array();
        if($this->firstName !== null) $data['firstName'] = $this->firstName;
        if($this->lastName !== null) $data['lastName'] = $this->lastName;
        if($this->deliveryAddress !== null) $data['deliveryAddress'] = $this->deliveryAddress->toXMLArray();
        if($this->email !== null) $data['email'] = $this->email;
        if($this->phoneNumber !== null) $data['phoneNumber'] = $this->phoneNumber;

        return $data;
    }
}
