<?php

namespace TijsVerkoyen\Bpost\Bpost;

use TijsVerkoyen\Bpost\Exception;
use TijsVerkoyen\Bpost\Bpost\Order\Box;
use TijsVerkoyen\Bpost\Bpost\Order\Line;

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
     * @return Box[]
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
     * @return Line[]
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
     * @param  \DOMDocument $document
     * @param  string       $accountId
     * @return \DOMElement
     */
    public function toXML(\DOMDocument $document, $accountId)
    {
        $order = $document->createElement(
            'tns:order'
        );
        $order->setAttribute(
            'xmlns:common',
            'http://schema.post.be/shm/deepintegration/v3/common'
        );
        $order->setAttribute(
            'xmlns:tns',
            'http://schema.post.be/shm/deepintegration/v3/'
        );
        $order->setAttribute(
            'xmlns',
            'http://schema.post.be/shm/deepintegration/v3/national'
        );
        $order->setAttribute(
            'xmlns:international',
            'http://schema.post.be/shm/deepintegration/v3/international'
        );
        $order->setAttribute(
            'xmlns:xsi',
            'http://www.w3.org/2001/XMLSchema-instance'
        );
        $order->setAttribute(
            'xsi:schemaLocation',
            'http://schema.post.be/shm/deepintegration/v3/'
        );

        $document->appendChild($order);

        $order->appendChild(
            $document->createElement(
                'tns:accountId',
                (string) $accountId
            )
        );

        if ($this->getReference() !== null) {
            $order->appendChild(
                $document->createElement(
                    'tns:reference',
                    $this->getReference()
                )
            );
        }
        if ($this->getCostCenter() !== null) {
            $order->appendChild(
                $document->createElement(
                    'tns:costCenter',
                    $this->getCostCenter()
                )
            );
        }

        $lines = $this->getLines();
        if (!empty($lines)) {
            foreach ($lines as $line) {
                /** @var $line \TijsVerkoyen\Bpost\Bpost\Order\Line */
                $order->appendChild(
                    $line->toXML($document, 'tns')
                );
            }
        }

        $boxes = $this->getBoxes();
        if (!empty($boxes)) {
            foreach ($boxes as $box) {
                /** @var $box \TijsVerkoyen\Bpost\Bpost\Order\Box */
                $order->appendChild(
                    $box->toXML($document, 'tns')
                );
            }
        }

        return $order;
    }

    /**
     * @param  \SimpleXMLElement $xml
     * @return Order
     */
    public static function createFromXML(\SimpleXMLElement $xml)
    {
        // @todo work with classmaps ...
        if (!isset($xml->reference)) {
            throw new Exception('No reference found.');
        }

        $order = new Order((string) $xml->reference);

        if (isset($xml->costCenter) && $xml->costCenter != '') {
            $order->setCostCenter((string) $xml->costCenter);
        }
        if (isset($xml->orderLine)) {
            foreach ($xml->orderLine as $orderLine) {
                $order->addLine(
                    Line::createFromXML($orderLine)
                );
            }
        }
        if (isset($xml->box)) {
            foreach ($xml->box as $box) {
                $order->addBox(Box::createFromXML($box));
            }
        }

        return $order;
    }
}
