<?php
namespace TijsVerkoyen\Bpost;

/**
 * bPost Delivery At Shop Method class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class DeliveryMethodAtShop extends DeliveryMethod
{
    /**
     * Generic Info
     *
     * @var mixed
     */
    private $infoPugo, $infoDistributed;

    /**
     * Get the options
     *
     * @return array
     */
    public function getInfoDistributed()
    {
        return $this->infoDistributed;
    }

    /**
     * Get the info pigu
     *
     * @return mixed
     */
    public function getInfoPugo()
    {
        return $this->infoPugo;
    }

    /**
     * Set the options
     *
     * @param Notification $notification
     */
    public function setInfoDistributed(Notification $notification)
    {
        $this->infoDistributed = $notification;
    }

    /**
     * Set the Pick Up & Go information
     *
     * @param string            $id           Id of the Pick Up & Go
     * @param string            $name         Name of the Pick Up & Go
     * @param Notification $notification One of the notification tags.
     */
    public function setInfoPugo($id, $name, Notification $notification)
    {
        $this->infoPugo['pugoId'] = (string) $id;
        $this->infoPugo['pugoName'] = (string) $name;
        $this->infoPugo['notification'] = $notification;
    }

    /**
     * Return the object as an array for usage in the XML
     *
     * @return array
     */
    public function toXMLArray()
    {
        $data = array();
        if(isset($this->infoPugo['notification'])) $data['atShop']['infoPugo'] = $this->infoPugo['notification']->toXMLArray();
        $data['atShop']['infoPugo']['pugoId'] = $this->infoPugo['pugoId'];
        $data['atShop']['infoPugo']['pugoName'] = $this->infoPugo['pugoName'];

        if ($this->insurance !== null) {
            if($this->insurance == 0) $data['atShop']['insurance']['basicInsurance'] = '';
            else $data['atShop']['insurance']['additionalInsurance']['@attributes']['value'] = $this->insurance;
        }
        if ($this->infoDistributed !== null) {
            $data['atShop']['infoDistributed'] = $this->infoDistributed->toXMLArray();
        }

        return $data;
    }
}
