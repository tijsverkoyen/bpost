<?php
namespace TijsVerkoyen\Bpost;

/**
 * bPost Delivery At Home Method class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class DeliveryMethodAtHome extends DeliveryMethod
{
    private $normal, $insured;

    /**
     * @var bool
     */
    private $dropAtTheDoor;

    /**
     * Get the options
     *
     * @return array
     */
    public function getInsured()
    {
        return $this->insured;
    }

    /**
     * Get the options
     *
     * @return mixed
     */
    public function getNormal()
    {
        return $this->normal;
    }

    /**
     * Set drop at the door
     *
     * @param bool $dropAtTheDoor
     */
    public function setDropAtTheDoor($dropAtTheDoor = true)
    {
        $this->dropAtTheDoor = (bool) $dropAtTheDoor;
    }

    /**
     * Set normal
     *
     * @param array $options
     */
    public function setNormal(array $options = null)
    {
        if ($options !== null) {
            foreach($options as $key => $value) $this->normal[$key] = $value;
        } else $this->normal = array();
    }

    /**
     * Return the object as an array for usage in the XML
     *
     * @return array
     */
    public function toXMLArray()
    {
        $data = array();
        if ($this->normal !== null) {
            $data['atHome']['normal'] = null;

            foreach ($this->normal as $key => $value) {
                if($key == 'automaticSecondPresentation') $data['atHome']['normal']['options']['automaticSecondPresentation'] = $value;
                else $data['atHome']['normal']['options'][$key] = $value->toXMLArray();
            }
        }
        if ($this->insurance !== null) {
            if($this->insurance == 0) $data['atHome-7']['insurance']['basicInsurance'] = '';
            else $data['atHome']['insurance']['additionalInsurance']['@attributes']['value'] = $this->insurance;
        }
        if($this->dropAtTheDoor) $data['atHome']['dropAtTheDoor'] = null;

        return $data;
    }
}
