<?php

require_once 'config.php';
require_once '../Bpost.php';

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * test case.
 */
class bPostTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var bPost
     */
    private $bpost;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->bpost = new bPost(ACCOUNT_ID, PASSPHRASE);
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
     * Tests bPost->getTimeOut()
     */
    public function testGetTimeOut()
    {
        $this->bpost->setTimeOut(5);
        $this->assertEquals(5, $this->bpost->getTimeOut());
    }

    /**
     * Tests bPost->getUserAgent()
     */
    public function testGetUserAgent()
    {
        $this->bpost->setUserAgent('testing/1.0.0');
        $this->assertEquals('PHP bPost/' . bPost::VERSION . ' testing/1.0.0', $this->bpost->getUserAgent());
    }

    /**
     * Tests bpost->createOrReplaceOrder
     */
    public function testCreateOrReplaceOrder()
    {
        $orderId = time();

        $deliveryMethod = new bPostDeliveryMethodAtHome();
        $deliveryMethod->setNormal();

        $customer = new bPostCustomer('Tijs', 'Verkoyen');
        $customer->setDeliveryAddress(new bPostAddress('Kerkstraat', '108', '9050', 'Gentbrugge'));

        $order = new bPostOrder($orderId);
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

        $deliveryMethod = new bPostDeliveryMethodAtHome();
        $deliveryMethod->setNormal();

        $customer = new bPostCustomer('Tijs', 'Verkoyen');
        $customer->setDeliveryAddress(new bPostAddress('Kerkstraat', '108', '9050', 'Gentbrugge'));

        $order = new bPostOrder($orderId);
        $order->setStatus('OPEN');
        $order->setCostCenter('CostCenter1');
        $order->addOrderLine('Item 1', 10);
        $order->addOrderLine('Item 2', 20);
        $order->setCustomer($customer);
        $order->setDeliveryMethod($deliveryMethod);
        $order->setTotal(100);

        $this->bpost->createOrReplaceOrder($order);

        $var = $this->bpost->fetchOrder($orderId);

        $this->assertInstanceOf('bPostOrder', $var);
        $this->assertEquals($orderId, $var->getReference());

        $this->bpost->modifyOrderStatus($orderId, 'CANCELLED');
    }

    /**
     * Tests bpost->modifyOrderStatus
     */
    public function testModifyOrderStatus()
    {
        $orderId = time();

        $deliveryMethod = new bPostDeliveryMethodAtHome();
        $deliveryMethod->setNormal();

        $customer = new bPostCustomer('Tijs', 'Verkoyen');
        $customer->setDeliveryAddress(new bPostAddress('Kerkstraat', '108', '9050', 'Gentbrugge'));

        $order = new bPostOrder($orderId);

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

        $deliveryMethod = new bPostDeliveryMethodAtHome();
        $deliveryMethod->setNormal();

        $customer = new bPostCustomer('Tijs', 'Verkoyen');
        $customer->setDeliveryAddress(new bPostAddress('Kerkstraat', '108', '9050', 'Gentbrugge'));

        $order = new bPostOrder($orderId);
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

        $customer = new bPostCustomer('Tijs', 'Verkoyen');
        $customer->setDeliveryAddress(new bPostAddress('Dieselstr.', '24', '85748', 'Garching', 'DE'));

        $deliveryMethod = new bPostDeliveryMethodIntBusiness();
        $deliveryMethod->setInsurance(10);

        $order = new bPostOrder($orderId);

        $order->setStatus('OPEN');
        $order->setCostCenter('CostCenter1');
        $order->addOrderLine('Item 1', 10);
        $order->addOrderLine('Item 2', 20);
        $order->setCustomer($customer);
        $order->setDeliveryMethod($deliveryMethod);
        $order->setTotal(100);

        $this->bpost->createOrReplaceOrder($order);

        $labelInfo1 = new bPostInternationalLabelInfo(100, 300, $orderId, 'OTHER', 'RTA', true);

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

        $deliveryMethod = new bPostDeliveryMethodAtHome();
        $deliveryMethod->setNormal();

        $customer = new bPostCustomer('Tijs', 'Verkoyen');
        $customer->setDeliveryAddress(new bPostAddress('Kerkstraat', '108', '9050', 'Gentbrugge'));

        $order = new bPostOrder($orderId);
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

        $customer = new bPostCustomer('Tijs', 'Verkoyen');
        $customer->setDeliveryAddress(new bPostAddress('Dieselstr.', '24', '85748', 'Garching', 'DE'));

        $deliveryMethod = new bPostDeliveryMethodIntExpress();

        $order = new bPostOrder($orderId);

        $order->setStatus('OPEN');
        $order->setCostCenter('CostCenter1');
        $order->addOrderLine('Item 1', 10);
        $order->addOrderLine('Item 2', 20);
        $order->setCustomer($customer);
        $order->setDeliveryMethod($deliveryMethod);
        $order->setTotal(100);

        $labelInfo1 = new bPostInternationalLabelInfo(100, 300, $orderId, 'OTHER', 'RTA', true);

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

        $deliveryMethod = new bPostDeliveryMethodAtHome();
        $deliveryMethod->setNormal();

        $customer = new bPostCustomer('Tijs', 'Verkoyen');
        $customer->setDeliveryAddress(new bPostAddress('Kerkstraat', '108', '9050', 'Gentbrugge'));

        $order = new bPostOrder($orderId);
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

        $deliveryMethod = new bPostDeliveryMethodAtHome();
        $deliveryMethod->setNormal();

        $customer = new bPostCustomer('Tijs', 'Verkoyen');
        $customer->setDeliveryAddress(new bPostAddress('Kerkstraat', '108', '9050', 'Gentbrugge'));

        $order = new bPostOrder($orderId);
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
