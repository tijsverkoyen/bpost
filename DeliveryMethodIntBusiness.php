<?php
namespace TijsVerkoyen\Bpost;

/**
 * bPost Delivery International Business Method class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class DeliveryMethodIntBusiness extends DeliveryMethod
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
        $data['intBusiness'] = null;
        if ($this->insurance !== null) {
            if($this->insurance == 0) $data['intBusiness']['insured']['basicInsurance'] = '';
            else $data['intBusiness']['insured']['additionalInsurance']['@attributes']['value'] = $this->insurance;
        }
        if ($this->insured !== null) {
            foreach ($this->insured as $key => $value) {
                if($key == 'automaticSecondPresentation') $data['intBusiness']['insured']['options']['automaticSecondPresentation'] = $value;
                else $data['intBusiness']['insured']['options'][$key] = $value->toXMLArray();
            }
        }

        return $data;
    }
}
