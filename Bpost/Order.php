<?php

namespace TijsVerkoyen\Bpost\Bpost;

/**
 * bPost Order class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class Order
{
    /**
     * Order reference: unique ID used in your web shop to assign to an order.
     * The value of this parameter is not managed by bpost. If the value
     * already exists, it will update current order info. Existing boxes will
     * not be changed, new boxes will be added.
     *
     * @var string
     */
    private $reference;

    /**
     * This information is used on your invoice and allows you to attribute
     * different cost centers
     *
     * @var string
     */
    private $costCenter;

    /**
     * The items that are included in the order.
     * Order lines are shown in the back end of the Shipping Manager and
     * facilitate the use of the tool.
     *
     * @var array
     */
    private $lines;

    /**
     * Box tags
     *
     * @var array
     */
    private $boxes;

    /**
     * Create an order
     *
     * @param string $reference
     */
    public function __construct($reference)
    {
        $this->setReference($reference);
    }

    /**
     * @param array $boxes
     */
    public function setBoxes($boxes)
    {
        $this->boxes = $boxes;
    }

    /**
     * @return array
     */
    public function getBoxes()
    {
        return $this->boxes;
    }

    /**
     * Add a box
     *
     * @param \TijsVerkoyen\Bpost\Bpost\Order\Box $box
     */
    public function addBox(\TijsVerkoyen\Bpost\Bpost\Order\Box $box)
    {
        $this->boxes[] = $box;
    }

    /**
     * @param string $costCenter
     */
    public function setCostCenter($costCenter)
    {
        $this->costCenter = $costCenter;
    }

    /**
     * @return string
     */
    public function getCostCenter()
    {
        return $this->costCenter;
    }

    /**
     * @param array $lines
     */
    public function setLines($lines)
    {
        $this->lines = $lines;
    }

    /**
     * @return array
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * Add an order line
     *
     * @param \TijsVerkoyen\Bpost\Bpost\Order\Line $line
     */
    public function addLine(\TijsVerkoyen\Bpost\Bpost\Order\Line $line)
    {
        $this->lines[] = $line;
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
     * Return the object as an array for usage in the XML
     *
     * @param  string $accountId
     * @return array
     */
    public function toXMLArray($accountId)
    {
        $data = array();
        $data['@attributes']['xmlns'] = 'http://schema.post.be/shm/deepintegration/v3/';
        $data['accountId'] = (string) $accountId;
        if ($this->getReference() !== null) {
            $data['reference'] = $this->getReference();
        }
        if ($this->getCostCenter() !== null) {
            $data['costCenter'] = $this->getCostCenter();
        }

        $lines = $this->getLines();
        if (!empty($lines)) {
            foreach ($lines as $line) {
                /** @var $line \TijsVerkoyen\Bpost\Bpost\Order\Line */
                $data['orderLine'][] = $line->toXMLArray();
            }
        }

        $boxes = $this->getBoxes();
        if (!empty($boxes)) {
            foreach ($boxes as $box) {
                /** @var $box \TijsVerkoyen\Bpost\Bpost\Order\Box */
                $data['box'][] = $box->toXMLArray();
            }
        }

        return $data;
    }
}
