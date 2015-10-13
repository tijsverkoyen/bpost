<?php

namespace TijsVerkoyen\Bpost\Bpost;

use SimpleXMLElement;
use TijsVerkoyen\Bpost\Bpost\ProductConfiguration\DeliveryMethod;

class ProductConfiguration
{

    /** @var array DeliveryMethod[] */
    private $deliveryMethods = array();

    /**
     * @return DeliveryMethod[]
     */
    public function getDeliveryMethods()
    {
        return $this->deliveryMethods;
    }

    /**
     * @param DeliveryMethod $deliveryMethod
     */
    public function addDeliveryMethod(DeliveryMethod $deliveryMethod)
    {
        $this->deliveryMethods[] = $deliveryMethod;
    }

    /**
     * @param SimpleXMLElement $xml
     *
     * @return ProductConfiguration
     */
    public static function createFromXML(SimpleXMLElement $xml)
    {
        $productConfiguration = new self();
        $children = $xml->children();

        if (isset($children->deliveryMethod)) {
            foreach ($children->deliveryMethod as $deliveryMethodXml) {
                $productConfiguration->addDeliveryMethod(DeliveryMethod::createFromXML($deliveryMethodXml));
            }
        }

        return $productConfiguration;
    }
}
