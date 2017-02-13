<?php
namespace Bpost;

use Bpost\BpostApiClient\Bpost\Order;
use Bpost\BpostApiClient\Bpost\Order\Address;
use Bpost\BpostApiClient\Bpost\Order\Box;
use Bpost\BpostApiClient\Bpost\Order\Box\AtBpost;
use Bpost\BpostApiClient\Bpost\Order\Box\Option\CashOnDelivery;
use Bpost\BpostApiClient\Bpost\Order\Box\Option\Messaging;
use Bpost\BpostApiClient\Bpost\Order\Box\Option\SaturdayDelivery;
use Bpost\BpostApiClient\Bpost\Order\Line;
use Bpost\BpostApiClient\Bpost\Order\PugoAddress;
use Bpost\BpostApiClient\Bpost\Order\Sender;

class OrderTest extends \PHPUnit_Framework_TestCase
{
    public function testToXml()
    {
        $self = new Order('bpack@bpost VAS 038 - COD+SAT+iD');
        $self->setCostCenter('Cost Center');

        $self->setLines(array(new Line('Product 1', 1)));
        $self->addLine(new Line('Product 1', 5));

        $senderAddress = new Address();
        $senderAddress->setStreetName('MUNT');
        $senderAddress->setNumber(1);
        $senderAddress->setBox(1);
        $senderAddress->setPostalCode(1000);
        $senderAddress->setLocality('Brussel');
        $senderAddress->setCountryCode('BE');
        $senderAddress->setBox(1);

        $pugoAddress = new PugoAddress();
        $pugoAddress->setStreetName('Turnhoutsebaan');
        $pugoAddress->setNumber(468);
        $pugoAddress->setBox('A');
        $pugoAddress->setPostalCode(2110);
        $pugoAddress->setLocality('Wijnegem');
        $pugoAddress->setCountryCode('BE');

        $sender = new Sender();
        $sender->setName('SENDER NAME');
        $sender->setCompany('SENDER COMPANY');
        $sender->setAddress($senderAddress);
        $sender->setEmailAddress('sender@mail.be');
        $sender->setPhoneNumber('022011111');

        $atBpost = new AtBpost();

        $atBpost->setOptions(array(
            new Messaging('infoDistributed', 'EN', null, '0476123456'),
            new Messaging('keepMeInformed', 'EN', null, '0032475123456'),
        ));
        $atBpost->addOption(new SaturdayDelivery());
        $atBpost->addOption(new CashOnDelivery(1251, 'BE19210023508812', 'GEBABEBB'));

        $atBpost->setWeight(2000);

        $atBpost->setPugoId(207500);
        $atBpost->setPugoName('WIJNEGEM');
        $atBpost->setPugoAddress($pugoAddress);
        $atBpost->setReceiverName('RECEIVER NAME');
        $atBpost->setReceiverCompany('RECEIVER COMPANY');
        $atBpost->setRequestedDeliveryDate('2020-10-22');

        $box = new Box();
        $box->setSender($sender);
        $box->setNationalBox($atBpost);
        $box->setRemark('bpack@bpost VAS 038 - COD+SAT+iD');
        $box->setAdditionalCustomerReference('Reference that can be used for cross-referencing');

        $self->setBoxes(array());
        $this->assertCount(0, $self->getBoxes());
        $self->addBox($box);

        // Normal
        $rootDom = $this->createDomDocument();
        $document = $this->generateDomDocument($rootDom, $self->toXML($rootDom, '107423'));

        $this->assertSame($this->getCreateOrderXml(), $document->saveXML());
    }

    /**
     * @expectedException \Bpost\BpostApiClient\Exception\XmlException\BpostXmlNoReferenceFoundException
     */
    public function testCreateFromXmlWithException()
    {
        Order::createFromXML(new \SimpleXMLElement($this->getFetchOrderWithReferenceXml()));
    }

    public function testCreateFromXml()
    {
        $self = Order::createFromXML(new \SimpleXMLElement($this->getFetchOrderXml()));

        $this->assertSame('bpost_ref_56e02a5047119', $self->getReference());
        $this->assertNotNull($self->getLines());
        $this->assertCount(2, $self->getLines());

        $this->assertNotNull($self->getBoxes());
        $this->assertCount(1, $self->getBoxes());
        $this->assertSame('Cost Center', $self->getCostCenter());

        /** @var Box $box */
        $box = current($self->getBoxes());

        $this->assertSame('Sender name', $box->getSender()->getName());
        $this->assertSame('Sender company', $box->getSender()->getCompany());
        $this->assertSame('pomme@antidot.com', $box->getSender()->getEmailAddress());
        $this->assertSame('0434343434', $box->getSender()->getPhoneNumber());
        $this->assertSame('Sender street', $box->getSender()->getAddress()->getStreetName());

        $this->assertSame('WORDPRESS 4.4.2 / WOOCOMMERCE 2.5.2', $box->getAdditionalCustomerReference());
        $this->assertSame(Box::BOX_STATUS_PENDING, $box->getStatus());
        $this->assertSame('Plouf a la playa', $box->getRemark());
        $this->assertNull($box->getInternationalBox());
        $this->assertNotNull($box->getNationalBox());

        /** @var AtBpost $nationalBox */
        $nationalBox = $box->getNationalBox();
        $this->assertInstanceOf('Bpost\BpostApiClient\Bpost\Order\Box\AtBpost', $nationalBox);
        $this->assertSame('bpack@bpost', $nationalBox->getProduct());
        $this->assertSame(1234, $nationalBox->getWeight());
        $this->assertNull($nationalBox->getOpeningHours());
        $this->assertSame('33100', $nationalBox->getPugoId());
        $this->assertSame('ANDERLECHT AUTONOMIE', $nationalBox->getPugoName());
        $this->assertSame('Receiver name', $nationalBox->getReceiverName());
        $this->assertSame('Receiver company', $nationalBox->getReceiverCompany());
        $this->assertSame('2016-03-19+01:00', $nationalBox->getRequestedDeliveryDate());
        $this->assertSame('Rue de l\'Autonomie', $nationalBox->getPugoAddress()->getStreetName());

        $this->assertNotNull($nationalBox->getOptions());
        //$this->assertCount(6, $nationalBox->getOptions());
    }

    private function getFetchOrderWithReferenceXml()
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<orderInfo xmlns="http://schema.post.be/shm/deepintegration/v3/" xmlns:ns2="http://schema.post.be/shm/deepintegration/v3/common" xmlns:ns3="http://schema.post.be/shm/deepintegration/v3/national" xmlns:ns4="http://schema.post.be/shm/deepintegration/v3/international">
  <accountId>107423</accountId>
  <costCenter>Cost Center</costCenter>
</orderInfo>

XML;
    }

    private function getFetchOrderXml()
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<orderInfo xmlns="http://schema.post.be/shm/deepintegration/v3/" xmlns:ns2="http://schema.post.be/shm/deepintegration/v3/common" xmlns:ns3="http://schema.post.be/shm/deepintegration/v3/national" xmlns:ns4="http://schema.post.be/shm/deepintegration/v3/international">
  <accountId>107423</accountId>
  <reference>bpost_ref_56e02a5047119</reference>
  <costCenter>Cost Center</costCenter>
  <orderLine>
    <text>Product 1</text>
    <nbOfItems>1</nbOfItems>
  </orderLine>
  <orderLine>
    <text>Product 1</text>
    <nbOfItems>5</nbOfItems>
  </orderLine>
  <box>
    <sender>
      <ns2:name>Sender name</ns2:name>
      <ns2:company>Sender company</ns2:company>
      <ns2:address>
        <ns2:streetName>Sender street</ns2:streetName>
        <ns2:number>1</ns2:number>
        <ns2:box>A</ns2:box>
        <ns2:postalCode>1000</ns2:postalCode>
        <ns2:locality>Bruxelles</ns2:locality>
        <ns2:countryCode>BE</ns2:countryCode>
      </ns2:address>
      <ns2:emailAddress>pomme@antidot.com</ns2:emailAddress>
      <ns2:phoneNumber>0434343434</ns2:phoneNumber>
    </sender>
    <nationalBox>
      <ns3:atBpost>
        <ns3:product>bpack@bpost</ns3:product>
        <ns3:options>
          <ns2:infoDistributed language="FR">
            <ns2:emailAddress>pomme@antidot.com</ns2:emailAddress>
          </ns2:infoDistributed>
          <ns2:keepMeInformed language="EN">
            <ns2:emailAddress>pomme@antidot.com</ns2:emailAddress>
          </ns2:keepMeInformed>
          <ns2:insured>
            <ns2:additionalInsurance value="2"/>
          </ns2:insured>
          <ns2:signed/>
          <ns2:saturdayDelivery/>
          <ns2:cod>
            <ns2:codAmount>1234</ns2:codAmount>
            <ns2:iban>BE19 2100 2350 8812</ns2:iban>
            <ns2:bic>GEBABEBB</ns2:bic>
          </ns2:cod>
        </ns3:options>
        <ns3:weight>1234</ns3:weight>
        <ns3:openingHours/>
        <ns3:pugoId>33100</ns3:pugoId>
        <ns3:pugoName>ANDERLECHT AUTONOMIE</ns3:pugoName>
        <ns3:pugoAddress>
          <ns2:streetName>Rue de l'Autonomie</ns2:streetName>
          <ns2:number>6A</ns2:number>
          <ns2:postalCode>1070</ns2:postalCode>
          <ns2:locality>Anderlecht</ns2:locality>
          <ns2:countryCode>BE</ns2:countryCode>
        </ns3:pugoAddress>
        <ns3:receiverName>Receiver name</ns3:receiverName>
        <ns3:receiverCompany>Receiver company</ns3:receiverCompany>
        <ns3:requestedDeliveryDate>2016-03-19+01:00</ns3:requestedDeliveryDate>
      </ns3:atBpost>
    </nationalBox>
    <remark>Plouf a la playa</remark>
    <additionalCustomerReference>WORDPRESS 4.4.2 / WOOCOMMERCE 2.5.2</additionalCustomerReference>
    <status>PENDING</status>
  </box>
</orderInfo>

XML;
    }

    private function getCreateOrderXml()
    {
        return <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<tns:order xmlns="http://schema.post.be/shm/deepintegration/v3/national" xmlns:common="http://schema.post.be/shm/deepintegration/v3/common" xmlns:tns="http://schema.post.be/shm/deepintegration/v3/" xmlns:international="http://schema.post.be/shm/deepintegration/v3/international" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://schema.post.be/shm/deepintegration/v3/">
  <tns:accountId>107423</tns:accountId>
  <tns:reference>bpack@bpost VAS 038 - COD+SAT+iD</tns:reference>
  <tns:costCenter>Cost Center</tns:costCenter>
  <tns:orderLine>
    <tns:text>Product 1</tns:text>
    <tns:nbOfItems>1</tns:nbOfItems>
  </tns:orderLine>
  <tns:orderLine>
    <tns:text>Product 1</tns:text>
    <tns:nbOfItems>5</tns:nbOfItems>
  </tns:orderLine>
  <tns:box>
    <tns:sender>
      <common:name>SENDER NAME</common:name>
      <common:company>SENDER COMPANY</common:company>
      <common:address>
        <common:streetName>MUNT</common:streetName>
        <common:number>1</common:number>
        <common:box>1</common:box>
        <common:postalCode>1000</common:postalCode>
        <common:locality>Brussel</common:locality>
        <common:countryCode>BE</common:countryCode>
      </common:address>
      <common:emailAddress>sender@mail.be</common:emailAddress>
      <common:phoneNumber>022011111</common:phoneNumber>
    </tns:sender>
    <tns:nationalBox>
      <atBpost>
        <product>bpack@bpost</product>
        <options>
          <common:infoDistributed language="EN">
            <common:mobilePhone>0476123456</common:mobilePhone>
          </common:infoDistributed>
          <common:keepMeInformed language="EN">
            <common:mobilePhone>0032475123456</common:mobilePhone>
          </common:keepMeInformed>
          <common:saturdayDelivery/>
          <common:cod>
            <common:codAmount>1251</common:codAmount>
            <common:iban>BE19210023508812</common:iban>
            <common:bic>GEBABEBB</common:bic>
          </common:cod>
        </options>
        <weight>2000</weight>
        <pugoId>207500</pugoId>
        <pugoName>WIJNEGEM</pugoName>
        <pugoAddress>
          <common:streetName>Turnhoutsebaan</common:streetName>
          <common:number>468</common:number>
          <common:box>A</common:box>
          <common:postalCode>2110</common:postalCode>
          <common:locality>Wijnegem</common:locality>
          <common:countryCode>BE</common:countryCode>
        </pugoAddress>
        <receiverName>RECEIVER NAME</receiverName>
        <receiverCompany>RECEIVER COMPANY</receiverCompany>
        <requestedDeliveryDate>2020-10-22</requestedDeliveryDate>
      </atBpost>
    </tns:nationalBox>
    <tns:remark>bpack@bpost VAS 038 - COD+SAT+iD</tns:remark>
    <tns:additionalCustomerReference>Reference that can be used for cross-referencing</tns:additionalCustomerReference>
  </tns:box>
</tns:order>

XML;
    }

    /**
     * Create a generic DOM Document
     *
     * @return \DOMDocument
     */
    private function createDomDocument()
    {
        $document = new \DOMDocument('1.0', 'UTF-8');
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;

        return $document;
    }

    /**
     * Generate the document, by adding the namespace declarations
     * @param \DOMDocument $document
     * @param \DOMElement  $element
     * @return \DOMDocument
     */
    private function generateDomDocument(\DOMDocument $document, \DOMElement $element)
    {
        $element->setAttribute(
            'xmlns:common',
            'http://schema.post.be/shm/deepintegration/v3/common'
        );
        $element->setAttribute(
            'xmlns:tns',
            'http://schema.post.be/shm/deepintegration/v3/'
        );
        $element->setAttribute(
            'xmlns',
            'http://schema.post.be/shm/deepintegration/v3/national'
        );
        $element->setAttribute(
            'xmlns:international',
            'http://schema.post.be/shm/deepintegration/v3/international'
        );
        $element->setAttribute(
            'xmlns:xsi',
            'http://www.w3.org/2001/XMLSchema-instance'
        );
        $element->setAttribute(
            'xsi:schemaLocation',
            'http://schema.post.be/shm/deepintegration/v3/'
        );

        $document->appendChild($element);

        return $document;
    }
}
