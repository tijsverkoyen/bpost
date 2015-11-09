<?php

namespace TijsVerkoyen\Bpost\tests;

use \TijsVerkoyen\Bpost\Bpost;
use \TijsVerkoyen\Bpost\Bpost\Order;
use \TijsVerkoyen\Bpost\Bpost\Order\Address;
use \TijsVerkoyen\Bpost\Bpost\Order\Box;
use \TijsVerkoyen\Bpost\Bpost\Order\Box\AtHome;
use \TijsVerkoyen\Bpost\Bpost\Order\Box\Option\Messaging;
use \TijsVerkoyen\Bpost\Bpost\Order\Line as OrderLine;
use \TijsVerkoyen\Bpost\Bpost\Order\Receiver;
use \TijsVerkoyen\Bpost\Bpost\Order\Sender;

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
        $this->assertSame(5, $this->bpost->getTimeOut());
    }

    /**
     * Tests Bpost->getUserAgent()
     */
    public function testGetUserAgent()
    {
        $this->bpost->setUserAgent('testing/1.0.0');
        $this->assertSame('PHP Bpost/' . Bpost::VERSION . ' testing/1.0.0', $this->bpost->getUserAgent());
    }

    /**
     * @return Order
     */
    protected function createAtHomeOrderObject()
    {
        // create order
        $orderId = time();
        $order = new Order($orderId);
        $order->setCostCenter('Cost Center');

        // add lines
        $line1 = new OrderLine('Beer', 1);
        $order->addLine($line1);
        $line2 = new OrderLine('Whisky', 100);
        $order->addLine($line2);

        // add box
        $address = new Address();
        $address->setStreetName('Afrikalaan');
        $address->setNumber('289');
        $address->setPostalCode('9000');
        $address->setLocality('Gent');
        $address->setCountryCode('BE');

        $sender = new Sender();
        $sender->setAddress($address);
        $sender->setName('Tijs Verkoyen');
        $sender->setCompany('Sumo Coders');
        $sender->setPhoneNumber('+32 9 395 02 51');
        $sender->setEmailAddress('bpost.sender@verkoyen.eu');

        $box = new Box();
        $box->setSender($sender);
        $box->setRemark('Remark');

        // add label
        $address = new Address();
        $address->setStreetName('Kerkstraat');
        $address->setNumber('108');
        $address->setPostalCode('9050');
        $address->setLocality('Gentbrugge');
        $address->setCountryCode('BE');

        $receiver = new Receiver();
        $receiver->setAddress($address);
        $receiver->setName('Tijs Verkoyen');
        $receiver->setCompany('Sumo Coders');
        $receiver->setPhoneNumber('+32 9 395 02 51');
        $receiver->setEmailAddress('bpost.receiver@verkoyen.eu');

        // options
        $option = new Messaging('infoDistributed', 'NL', 'bpost@verkoyen.eu');

        // @Home
        $atHome = new AtHome();
        $atHome->setProduct('bpack 24h Pro');
        $atHome->setWeight(2000);
        $atHome->setReceiver($receiver);
        $atHome->addOption($option);
        $box->setNationalBox($atHome);

        $order->addBox($box);

        return $order;
    }

    /**
     * Tests Bpost->createOrReplaceOrder
     */
    public function testCreateOrReplaceOrder()
    {
        $order = $this->createAtHomeOrderObject();
        $response = $this->bpost->createOrReplaceOrder($order);
        $this->assertTrue($response);

        $this->bpost->modifyOrderStatus($order->getReference(), 'CANCELLED');
    }

    /**
     * Tests Bpost->modifyOrderStatus
     */
    public function testModifyOrderStatus()
    {
        $order = $this->createAtHomeOrderObject();
        $this->bpost->createOrReplaceOrder($order);
        $response = $this->bpost->modifyOrderStatus($order->getReference(), 'OPEN');
        $this->assertTrue($response);

        $this->bpost->modifyOrderStatus($order->getReference(), 'CANCELLED');
    }

    /**
     * Tests Bpost->fetchOrder
     */
    public function testFetchOrder()
    {
        $order = $this->createAtHomeOrderObject();
        $this->bpost->createOrReplaceOrder($order);
        $response = $this->bpost->fetchOrder($order->getReference());
        $this->assertInstanceOf('\\TijsVerkoyen\Bpost\\Bpost\\Order', $response);
        $this->assertSame($order->getReference(), $response->getReference());

        $this->bpost->modifyOrderStatus($order->getReference(), 'CANCELLED');
    }

    /**
     * Tests Bpost->createLabelForOrder
     */
    public function testCreateLabelForOrder()
    {
        $order = $this->createAtHomeOrderObject();
        $this->bpost->createOrReplaceOrder($order);
        $response = $this->bpost->createLabelForOrder($order->getReference());
        $this->assertInternalType('array', $response);
        foreach ($response as $label) {
            $this->assertInstanceOf('\\TijsVerkoyen\\Bpost\BPost\Label', $label);
        }

        $this->bpost->modifyOrderStatus($order->getReference(), 'CANCELLED');
    }

    /**
     * Tests Bpost->createLabelForBox
     */
    public function testCreateLabelForBox()
    {
        $this->markTestSkipped(
            'As our account isn\'t one that has been marked for production
            we can\'t test this.'
        );

        $order = $this->createAtHomeOrderObject();
        $this->bpost->createOrReplaceOrder($order);
        $response = $this->bpost->createLabelForOrder($order->getReference());
        $response = $this->bpost->createLabelForBox($response[0]->getBarcode());
        $this->assertInternalType('array', $response);
        foreach ($response as $label) {
            $this->assertInstanceOf('\\TijsVerkoyen\\Bpost\BPost\Label', $label);
        }

        $this->bpost->modifyOrderStatus($order->getReference(), 'CANCELLED');
    }

    /**
     * Tests Bpost->createLabelInBulkForOrders
     */
    public function testCreateLabelInBulkForOrders()
    {
        $order1 = $this->createAtHomeOrderObject();
        $this->bpost->createOrReplaceOrder($order1);

        $order2 = $this->createAtHomeOrderObject();
        $this->bpost->createOrReplaceOrder($order2);

        $this->bpost->setTimeOut(60);
        $response = $this->bpost->createLabelInBulkForOrders(
            array(
                $order1->getReference(),
                $order2->getReference(),
            )
        );

        $this->assertInternalType('array', $response);
        foreach ($response as $label) {
            $this->assertInstanceOf('\\TijsVerkoyen\\Bpost\BPost\Label', $label);
        }

        $this->bpost->modifyOrderStatus($order1->getReference(), 'CANCELLED');
        $this->bpost->modifyOrderStatus($order2->getReference(), 'CANCELLED');
    }
}
