<?php

namespace TijsVerkoyen\Bpost\tests;

require_once __DIR__ . '/../../../autoload.php';
require_once 'config.php';

use \TijsVerkoyen\Bpost\Bpost;
use \TijsVerkoyen\Bpost\DeliveryMethodAtHome;
use \TijsVerkoyen\Bpost\DeliveryMethodIntBusiness;
use \TijsVerkoyen\Bpost\DeliveryMethodIntExpress;
use \TijsVerkoyen\Bpost\Address;
use \TijsVerkoyen\Bpost\Customer;
use \TijsVerkoyen\Bpost\Order;
use \TijsVerkoyen\Bpost\InternationalLabelInfo;

/**
 * test case.
 */
class BpostTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Bpost
     */
    private $bpost;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->bpost = new Bpost(ACCOUNT_ID, PASSPHRASE);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->bpost = null;
        parent::tearDown();
    }

    /**
     * Tests Bpost->getTimeOut()
     */
    public function testGetTimeOut()
    {
        $this->bpost->setTimeOut(5);
        $this->assertEquals(5, $this->bpost->getTimeOut());
    }

    /**
     * Tests Bpost->getUserAgent()
     */
    public function testGetUserAgent()
    {
        $this->bpost->setUserAgent('testing/1.0.0');
        $this->assertEquals('PHP Bpost/' . Bpost::VERSION . ' testing/1.0.0', $this->bpost->getUserAgent());
    }

    /**
     * Tests bpost->createOrReplaceOrder
     */
    public function testCreateOrReplaceOrder()
    {
        $orderId = time();

        $deliveryMethod = new DeliveryMethodAtHome();
        $deliveryMethod->setNormal();

        $customer = new Customer('Tijs', 'Verkoyen');
        $customer->setDeliveryAddress(new Address('Kerkstraat', '108', '9050', 'Gentbrugge'));

        $order = new Order($orderId);
        $order->setStatus('OPEN');
        $order->setCostCenter('CostCenter1');
        $order->addOrderLine('Item 1', 10);
        $order->addOrderLine('Item 2', 20);
        $order->setCustomer($customer);
        $order->setDeliveryMethod($deliveryMethod);
        $order->setTotal(100);

        $var = $this->bpost->createOrReplaceOrder($order);

        $this->assertTrue($var);

        $this->bpost->modifyOrderStatus($orderId, 'CANCELLED');
    }

    /**
     * Tests bpost->fetchOrder
     */
    public function testFetchOrder()
    {
        $orderId = time();

        $deliveryMethod = new DeliveryMethodAtHome();
        $deliveryMethod->setNormal();

        $customer = new Customer('Tijs', 'Verkoyen');
        $customer->setDeliveryAddress(new Address('Kerkstraat', '108', '9050', 'Gentbrugge'));

        $order = new Order($orderId);
        $order->setStatus('OPEN');
        $order->setCostCenter('CostCenter1');
        $order->addOrderLine('Item 1', 10);
        $order->addOrderLine('Item 2', 20);
        $order->setCustomer($customer);
        $order->setDeliveryMethod($deliveryMethod);
        $order->setTotal(100);

        $this->bpost->createOrReplaceOrder($order);

        $var = $this->bpost->fetchOrder($orderId);

        $this->assertInstanceOf('Order', $var);
        $this->assertEquals($orderId, $var->getReference());

        $this->bpost->modifyOrderStatus($orderId, 'CANCELLED');
    }

    /**
     * Tests bpost->modifyOrderStatus
     */
    public function testModifyOrderStatus()
    {
        $orderId = time();

        $deliveryMethod = new DeliveryMethodAtHome();
        $deliveryMethod->setNormal();

        $customer = new Customer('Tijs', 'Verkoyen');
        $customer->setDeliveryAddress(new Address('Kerkstraat', '108', '9050', 'Gentbrugge'));

        $order = new Order($orderId);

        $order->setStatus('OPEN');
        $order->setCostCenter('CostCenter1');
        $order->addOrderLine('Item 1', 10);
        $order->addOrderLine('Item 2', 20);
        $order->setCustomer($customer);
        $order->setDeliveryMethod($deliveryMethod);
        $order->setTotal(100);

        $this->bpost->createOrReplaceOrder($order);

        $var = $this->bpost->modifyOrderStatus($orderId, 'CANCELLED');

        $this->assertTrue($var);
    }

    /**
     * Tests bpost->createNationalLabel
     */
    public function testCreateNationalLabel()
    {
        $orderId = time();

        $deliveryMethod = new DeliveryMethodAtHome();
        $deliveryMethod->setNormal();

        $customer = new Customer('Tijs', 'Verkoyen');
        $customer->setDeliveryAddress(new Address('Kerkstraat', '108', '9050', 'Gentbrugge'));

        $order = new Order($orderId);
        $order->setStatus('OPEN');
        $order->setCostCenter('CostCenter1');
        $order->addOrderLine('Item 1', 10);
        $order->addOrderLine('Item 2', 20);
        $order->setCustomer($customer);
        $order->setDeliveryMethod($deliveryMethod);
        $order->setTotal(100);

        $this->bpost->createOrReplaceOrder($order);

        $var = $this->bpost->createNationalLabel($orderId, 1, null, true);

        $this->assertArrayHasKey('orderReference', $var);
        $this->assertArrayHasKey('barcode', $var);
        $this->assertArrayHasKey('pdf', $var);

        $this->bpost->modifyOrderStatus($orderId, 'CANCELLED');
    }

    /**
     * Tests bpost->createInternationalLabel
     */
    public function testCreateInternationalLabel()
    {
        $orderId = time();

        $customer = new Customer('Tijs', 'Verkoyen');
        $customer->setDeliveryAddress(new Address('Dieselstr.', '24', '85748', 'Garching', 'DE'));

        $deliveryMethod = new DeliveryMethodIntBusiness();
        $deliveryMethod->setInsurance(10);

        $order = new Order($orderId);

        $order->setStatus('OPEN');
        $order->setCostCenter('CostCenter1');
        $order->addOrderLine('Item 1', 10);
        $order->addOrderLine('Item 2', 20);
        $order->setCustomer($customer);
        $order->setDeliveryMethod($deliveryMethod);
        $order->setTotal(100);

        $this->bpost->createOrReplaceOrder($order);

        $labelInfo1 = new InternationalLabelInfo(100, 300, $orderId, 'OTHER', 'RTA', true);

        $var = $this->bpost->createInternationalLabel($orderId, array($labelInfo1), true);

        $this->assertArrayHasKey('orderReference', $var);
        $this->assertArrayHasKey('barcode', $var);
        $this->assertArrayHasKey('pdf', $var);

        $this->bpost->modifyOrderStatus($orderId, 'CANCELLED');
    }

    /**
     * Tests bpost->createOrderAndNationalLabel
     */
    public function testCreateOrderAndNationalLabel()
    {
        $orderId = time();

        $deliveryMethod = new DeliveryMethodAtHome();
        $deliveryMethod->setNormal();

        $customer = new Customer('Tijs', 'Verkoyen');
        $customer->setDeliveryAddress(new Address('Kerkstraat', '108', '9050', 'Gentbrugge'));

        $order = new Order($orderId);
        $order->setStatus('OPEN');
        $order->setCostCenter('CostCenter1');
        $order->addOrderLine('Item 1', 10);
        $order->addOrderLine('Item 2', 20);
        $order->setCustomer($customer);
        $order->setDeliveryMethod($deliveryMethod);
        $order->setTotal(100);

        $var = $this->bpost->createOrderAndNationalLabel($order, 1);

        $this->assertArrayHasKey('orderReference', $var);
        $this->assertArrayHasKey('barcode', $var);

        $this->bpost->modifyOrderStatus($orderId, 'CANCELLED');
    }

    /**
     * Tests bpost->createOrderAndInternationalLabel
     */
    public function testCreateOrderAndInternationalLabel()
    {
        $orderId = time();

        $customer = new Customer('Tijs', 'Verkoyen');
        $customer->setDeliveryAddress(new Address('Dieselstr.', '24', '85748', 'Garching', 'DE'));

        $deliveryMethod = new DeliveryMethodIntExpress();

        $order = new Order($orderId);

        $order->setStatus('OPEN');
        $order->setCostCenter('CostCenter1');
        $order->addOrderLine('Item 1', 10);
        $order->addOrderLine('Item 2', 20);
        $order->setCustomer($customer);
        $order->setDeliveryMethod($deliveryMethod);
        $order->setTotal(100);

        $labelInfo1 = new InternationalLabelInfo(100, 300, $orderId, 'OTHER', 'RTA', true);

        $var = $this->bpost->createOrderAndInternationalLabel(array($labelInfo1), $order);

        $this->assertArrayHasKey('orderReference', $var);
        $this->assertArrayHasKey('barcode', $var);

        $this->bpost->modifyOrderStatus($orderId, 'CANCELLED');
    }

    /**
     * Tests bpost->retrievePDFLabelsForBox
     */
    public function testRetrievePDFLabelsForBox()
    {
        $orderId = time();

        $deliveryMethod = new DeliveryMethodAtHome();
        $deliveryMethod->setNormal();

        $customer = new Customer('Tijs', 'Verkoyen');
        $customer->setDeliveryAddress(new Address('Kerkstraat', '108', '9050', 'Gentbrugge'));

        $order = new Order($orderId);
        $order->setStatus('OPEN');
        $order->setCostCenter('CostCenter1');
        $order->addOrderLine('Item 1', 10);
        $order->addOrderLine('Item 2', 20);
        $order->setCustomer($customer);
        $order->setDeliveryMethod($deliveryMethod);
        $order->setTotal(100);

        $this->bpost->createOrReplaceOrder($order);
        $var = $this->bpost->createNationalLabel($orderId, 1);

        $var = $this->bpost->retrievePDFLabelsForBox($var['barcode'][0]);

        $this->assertTrue((strpos(base64_decode($var), 'PDF-1.4') !== false));

        $this->bpost->modifyOrderStatus($orderId, 'CANCELLED');
    }

    /**
     * Tests bpost->retrievePDFLabelsForOrder
     */
    public function testRetrievePDFLabelsForOrder()
    {
        $orderId = time();

        $deliveryMethod = new DeliveryMethodAtHome();
        $deliveryMethod->setNormal();

        $customer = new Customer('Tijs', 'Verkoyen');
        $customer->setDeliveryAddress(new Address('Kerkstraat', '108', '9050', 'Gentbrugge'));

        $order = new Order($orderId);
        $order->setStatus('OPEN');
        $order->setCostCenter('CostCenter1');
        $order->addOrderLine('Item 1', 10);
        $order->addOrderLine('Item 2', 20);
        $order->setCustomer($customer);
        $order->setDeliveryMethod($deliveryMethod);
        $order->setTotal(100);

        $this->bpost->createOrReplaceOrder($order);
        $var = $this->bpost->createNationalLabel($orderId, 1);

        $var = $this->bpost->retrievePDFLabelsForOrder($orderId);

        $this->assertTrue((strpos(base64_decode($var), 'PDF-1.4') !== false));

        $this->bpost->modifyOrderStatus($orderId, 'CANCELLED');
    }
}
