<?php
namespace TijsVerkoyen\Bpost\Bpost\Order\Box;

use TijsVerkoyen\Bpost\Bpost\Order\Box\Openinghour\Day;
use TijsVerkoyen\Bpost\Exception;

/**
 * bPost AtHome class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 * @version   3.0.0
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class AtHome extends National
{
    /**
     * @var array
     */
    private $openingHours;

    /**
     * @var string
     */
    private $desiredDeliveryPlace;

    /**
     * @var \TijsVerkoyen\Bpost\Bpost\Order\Receiver
     */
    private $receiver;

    /**
     * @param string $desiredDeliveryPlace
     */
    public function setDesiredDeliveryPlace($desiredDeliveryPlace)
    {
        $this->desiredDeliveryPlace = $desiredDeliveryPlace;
    }

    /**
     * @return string
     */
    public function getDesiredDeliveryPlace()
    {
        return $this->desiredDeliveryPlace;
    }

    /**
     * @param array $openingHours
     */
    public function setOpeningHours($openingHours)
    {
        $this->openingHours = $openingHours;
    }

    /**
     * @param \TijsVerkoyen\Bpost\Bpost\Order\Box\Openinghour\Day $day
     */
    public function addOpeningHour(\TijsVerkoyen\Bpost\Bpost\Order\Box\Openinghour\Day $day)
    {
        $this->openingHours[] = $day;
    }

    /**
     * @return array
     */
    public function getOpeningHours()
    {
        return $this->openingHours;
    }

    /**
     * @param string $product Possible values are:
     *                          * bpack 24h Pro,
     *                          * bpack 24h business
     *                          * bpack Bus
     *                          * bpack Pallet
     *                          * bpack Easy Retour
     */
    public function setProduct($product)
    {
        if (!in_array($product, self::getPossibleProductValues())) {
            throw new Exception(
                sprintf(
                    'Invalid value, possible values are: %1$s.',
                    implode(', ', self::getPossibleProductValues())
                )
            );
        }

        parent::setProduct($product);
    }

    /**
     * @return array
     */
    public static function getPossibleProductValues()
    {
        return array(
            'bpack 24h Pro',
            'bpack 24h business',
            'bpack Bus',
            'bpack Pallet',
            'bpack Easy Retour',
        );
    }

    /**
     * @param \TijsVerkoyen\Bpost\Bpost\Order\Receiver $receiver
     */
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;
    }

    /**
     * @return \TijsVerkoyen\Bpost\Bpost\Order\Receiver
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * Return the object as an array for usage in the XML
     *
     * @return array
     */
    public function toXMLArray()
    {
        $data = parent::toXMLArray();

        $openingHours = $this->getOpeningHours();
        if (!empty($openingHours)) {
            foreach ($openingHours as $day) {
                /**
                 * @var $day Day
                 */
                $data['openingHours'][] = $day->toXMLArray();
            }
        }

        if ($this->getDesiredDeliveryPlace() !== null) {
            $data['desiredDeliveryPlace'] = $this->getDesiredDeliveryPlace();
        }

        if ($this->getReceiver() !== null) {
            $data['receiver'] = $this->getReceiver()->toXMLArray();
        }

        return array('atHome' => $data);
    }
}
