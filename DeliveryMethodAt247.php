<?php
namespace TijsVerkoyen\Bpost;

/**
 * bPost Delivery At 24/7 Method class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class DeliveryMethodAt247 extends DeliveryMethod
{
    /**
     * Generic info
     *
     * @var mixed
     */
    private $infoParcelsDepot, $signature, $memberId;

    /**
     * Create an at24-7 object
     *
     * @param string $parcelsDepotId
     */
    public function __construct($parcelsDepotId)
    {
        $this->setInfoParcelsDepot($parcelsDepotId);
    }

    /**
     * Get info parcel depot
     *
     * @return string
     */
    public function getInfoParcelsDepot()
    {
        return $this->infoParcelsDepot;
    }

    /**
     * Get member id
     *
     * @return mixed
     */
    public function getMemberId()
    {
        return $this->memberId;
    }

    /**
     * Get signature
     *
     * @return mixed
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * Set info parcels depot
     *
     * @param string $infoParcelsDepot
     */
    public function setInfoParcelsDepot($infoParcelsDepot)
    {
        $this->infoParcelsDepot = (string) $infoParcelsDepot;
    }

    /**
     * Set member id
     *
     * @param string $memberId
     */
    public function setMemberId($memberId)
    {
        $this->memberId = $memberId;
    }

    /**
     * Set signature
     *
     * @param bool[optional] $isPlus
     */
    public function setSignature($isPlus = false)
    {
        $this->signature = (bool) $isPlus;
    }

    /**
     * Return the object as an array for usage in the XML
     *
     * @return array
     */
    public function toXMLArray()
    {
        $data = array();
        $data['at24-7']['infoParcelsDepot']['parcelsDepotId'] = $this->infoParcelsDepot;
        $data['at24-7']['memberId'] = $this->memberId;
        if ($this->signature !== null) {
            if($this->signature) $data['at24-7']['signaturePlus'] = null;
            else $data['at24-7']['signature'] = null;
        }
        if ($this->insurance !== null) {
            if($this->insurance == 0) $data['at24-7']['insurance']['basicInsurance'] = '';
            else $data['at24-7']['insurance']['additionalInsurance']['@attributes']['value'] = $this->insurance;
        }

        return $data;
    }
}
