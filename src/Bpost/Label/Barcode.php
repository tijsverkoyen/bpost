<?php

namespace Bpost\BpostApiClient\Bpost\Label;

use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;

/**
 * Class Barcode
 * @package Bpost\BpostApiClient\Bpost\Label
 */
class Barcode
{

    /**
     * @var string
     */
    private $barcode;

    /**
     * @var string
     */
    private $reference;

    /**
     * @param string $barcode
     */
    public function setBarcode($barcode)
    {
        $this->barcode = (string)$barcode;
    }

    /**
     * @return string
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * @param string $reference
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param \SimpleXMLElement $xml
     *
     * @return self
     */
    public static function createFromXML(\SimpleXMLElement $xml)
    {
        $self = new self();
        if (isset($xml->barcode) && $xml->barcode != '') {
            $self->setBarcode((string)$xml->barcode);
        }
        if (isset($xml->reference) && $xml->reference != '') {
            $self->setReference((string)$xml->reference);
        }

        return $self;
    }
}
