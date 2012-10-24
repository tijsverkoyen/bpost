<?php
namespace TijsVerkoyen\Bpost;

/**
 * bPost Order class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class Order
{
    /**
     * Generic info
     *
     * @var string
     */
    private $costCenter, $status, $reference;

    /**
     * The order lines
     * @var array
     */
    private $lines, $barcodes;

    /**
     * The customer
     *
     * @var bPostCustomer
     */
    private $customer;

    /**
     * The delivery method
     *
     * @var bPostDeliveryMethod
     */
    private $deliveryMethod;

    /**
     * The order total
     *
     * @var int
     */
    private $total;

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
     * Add an order line
     *
     * @param string $text          Text describing the ordered item.
     * @param int    $numberOfItems Number of items.
     */
    public function addOrderLine($text, $numberOfItems)
    {
        $this->lines[] = array(
            'text' => (string) $text,
            'nbOfItems' => (int) $numberOfItems
        );
    }

    /**
     * Get the barcodes
     *
     * @return array
     */
    public function getBarcodes()
    {
        return $this->barcodes;
    }

    /**
     * Get the cost center
     * @return string
     */
    public function getCostCenter()
    {
        return $this->costCenter;
    }

    /**
     * Get the customer
     *
     * @return bPostCustomer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Get the delivery method
     *
     * @return bPostDeliveryMethod
     */
    public function getDeliveryMethod()
    {
        return $this->deliveryMethod;
    }

    /**
     * Get the order lines
     *
     * @return array
     */
    public function getOrderLines()
    {
        return $this->lines;
    }

    /**
     * Get the reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Get the status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get the total price of the order.
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set the barcodes
     *
     * @param array $barcodes
     */
    public function setBarcodes(array $barcodes)
    {
        $this->barcodes = $barcodes;
    }

    /**
     * Set teh cost center, will be used on your invoice and allows you to attribute different cost centers
     *
     * @param string $costCenter
     */
    public function setCostCenter($costCenter)
    {
        $this->costCenter = (string) $costCenter;
    }

    /**
     * Set the customer
     *
     * @param Customer $customer
     */
    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * Set the delivery method
     *
     * @param DeliveryMethod $deliveryMethod
     */
    public function setDeliveryMethod(DeliveryMethod $deliveryMethod)
    {
        $this->deliveryMethod = $deliveryMethod;
    }

    /**
     * Set the order reference, a unique id used in your web-shop.
     * If the value already exists it will overwrite the current info.
     *
     * @param string $reference
     */
    public function setReference($reference)
    {
        $this->reference = (string) $reference;
    }

    /**
     * Set the order status
     *
     * @param string $status Possible values are OPEN, PENDING, CANCELLED, COMPLETED, ON-HOLD.
     */
    public function setStatus($status)
    {
        $allowedStatuses = array('OPEN', 'PENDING', 'CANCELLED', 'COMPLETED', 'ON-HOLD');

        // validate
        if (!in_array($status, $allowedStatuses)) {
            throw new Exception(
                'Invalid status (' . $status . '), possible values are: ' . implode(', ', $allowedStatuses) . '.'
            );
        }

        $this->status = $status;
    }

    /**
     * The total price of the order in euro-cents (excluding shipping)
     *
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = (int) $total;
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
        $data['@attributes']['xmlns'] = 'http://schema.post.be/shm/deepintegration/v2/';
        $data['accountId'] = (string) $accountId;
        if($this->reference !== null) $data['orderReference'] = $this->reference;
        if($this->status !== null) $data['status'] = $this->status;
        if($this->costCenter !== null) $data['costCenter'] = $this->costCenter;

        if (!empty($this->lines)) {
            foreach ($this->lines as $line) {
                $data['orderLine'][] = $line;
            }
        }

        if($this->customer !== null) $data['customer'] = $this->customer->toXMLArray();
        if($this->deliveryMethod !== null) $data['deliveryMethod'] = $this->deliveryMethod->toXMLArray();
        if($this->total !== null) $data['totalPrice'] = $this->total;

        return $data;
    }
}

