<?php
namespace TijsVerkoyen\Bpost;

/**
 * bPost Delivery International Express Method class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class DeliveryMethodIntExpress extends DeliveryMethod
{
    /**
     * The options
     *
     * @var array
     */
    private $insured;

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
     * Set the options
     *
     * @param array $options
     */
    public function setInsured(array $options = null)
    {
        if ($options !== null) {
            foreach($options as $key => $value) $this->insured[$key] = $value;
        }
    }

    /**
     * Return the object as an array for usage in the XML
     *
     * @return array
     */
    public function toXMLArray()
    {
        $data = array();
        $data['intExpress'] = null;
        if ($this->insurance !== null) {
            if($this->insurance == 0) $data['intExpress']['insured']['basicInsurance'] = '';
            else $data['intExpress']['insured']['additionalInsurance']['@attributes']['value'] = $this->insurance;
        }
        if ($this->insured !== null) {
            foreach ($this->insured as $key => $value) {
                if($key == 'automaticSecondPresentation') $data['intExpress']['insured']['options']['automaticSecondPresentation'] = $value;
                else $data['intExpress']['insured']['options'][$key] = $value->toXMLArray();
            }
        }

        return $data;
    }

}
