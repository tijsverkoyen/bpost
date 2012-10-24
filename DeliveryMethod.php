<?php
namespace TijsVerkoyen\Bpost;

/**
 * bPost Delivery Method class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class DeliveryMethod
{
    protected $insurance;

    /**
     * Set the insurance level
     *
     * @param int $level Level from 0 to 11.
     */
    public function setInsurance($level = 0)
    {
        if((int) $level > 11) throw new Exception('Invalid value () for level.');
        $this->insurance = $level;
    }

    /**
     * Return the object as an array for usage in the XML
     *
     * @return array
     */
    public function toXMLArray()
    {
        // build data
        $data = array();
        if ($this->insurance !== null) {
            if($this->insurance == 0) $data['insurance']['basicInsurance'] = '';
            else $data['insurance']['additionalInsurance']['@attributes']['value'] = $this->insurance;
        }

        return $data;
    }
}

