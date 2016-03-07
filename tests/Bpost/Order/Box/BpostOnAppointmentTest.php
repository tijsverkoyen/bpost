<?php
namespace Bpost;

use TijsVerkoyen\Bpost\Bpost\Order\Address;
use TijsVerkoyen\Bpost\Bpost\Order\Box\BpostOnAppointment;
use TijsVerkoyen\Bpost\Bpost\Order\Receiver;
use TijsVerkoyen\Bpost\Bpost\ProductConfiguration\Product;

class BpostOnAppointmentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Create a generic DOM Document
     *
     * @return \DOMDocument
     */
    private function createDomDocument()
    {
        $document = new \DOMDocument('1.0', 'utf-8');
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;

        return $document;
    }

    /**
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

    /**
     * Tests Address->toXML
     */
    public function testToXML()
    {
        $address = new Address();
        $address->setCountryCode('BE');
        $address->setPostalCode('1040');
        $address->setLocality('Brussels');
        $address->setStreetName('Rue du Grand Duc');
        $address->setNumber('13');

        $receiver = new Receiver();
        $receiver->setName('La Pomme');
        $receiver->setEmailAddress('pomme@antidot.com');
        $receiver->setCompany('Antidot');
        $receiver->setAddress($address);
        $receiver->setPhoneNumber('026411390');

        $self = new BpostOnAppointment();
        $self->setProduct('bpack 24h Pro');
        $self->setInNetworkCutOff('2016-03-16');
        $self->setReceiver($receiver);

        // Normal
        $rootDom = $this->createDomDocument();
        $document = $this->generateDomDocument($rootDom, $self->toXml($rootDom));

        $this->assertEquals($this->getNormalXml(), $document->saveXML());

        return;
    }

    public function testCreateFromXml()
    {
        $self = BpostOnAppointment::createFromXml(new \SimpleXMLElement($this->getNormalXml()));

        $this->assertSame('2016-03-16', $self->getInNetworkCutOff());

        $this->assertNotNull($self->getReceiver());
        $this->assertSame('Antidot', $self->getReceiver()->getCompany());
    }

    public function testCreateFromNotBpostOnAppointmentXml()
    {
        $this->setExpectedException('TijsVerkoyen\Bpost\Exception\XmlException\BpostXmlInvalidItemException');
        BpostOnAppointment::createFromXml(new \SimpleXMLElement($this->getNotBpostOnAppointmentXml()));
    }

    private function getNormalXml()
    {
        return <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<nationalBox xmlns="http://schema.post.be/shm/deepintegration/v3/national" xmlns:common="http://schema.post.be/shm/deepintegration/v3/common" xmlns:tns="http://schema.post.be/shm/deepintegration/v3/" xmlns:international="http://schema.post.be/shm/deepintegration/v3/international" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://schema.post.be/shm/deepintegration/v3/">
  <bpostOnAppointment>
    <product>bpack 24h Pro</product>
    <receiver>
      <common:name>La Pomme</common:name>
      <common:company>Antidot</common:company>
      <common:address>
        <common:streetName>Rue du Grand Duc</common:streetName>
        <common:number>13</common:number>
        <common:postalCode>1040</common:postalCode>
        <common:locality>Brussels</common:locality>
        <common:countryCode>BE</common:countryCode>
      </common:address>
      <common:emailAddress>pomme@antidot.com</common:emailAddress>
      <common:phoneNumber>026411390</common:phoneNumber>
    </receiver>
    <inNetworkCutOff>2016-03-16</inNetworkCutOff>
  </bpostOnAppointment>
</nationalBox>

EOF;
    }

    private function getNotBpostOnAppointmentXml()
    {
        return <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<nationalBox xmlns="http://schema.post.be/shm/deepintegration/v3/national" xmlns:common="http://schema.post.be/shm/deepintegration/v3/common" xmlns:tns="http://schema.post.be/shm/deepintegration/v3/" xmlns:international="http://schema.post.be/shm/deepintegration/v3/international" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://schema.post.be/shm/deepintegration/v3/">
  <bpostAtHome>
    <product>bpack 24h Pro</product>
    <receiver>
      <common:name>La Pomme</common:name>
      <common:company>Antidot</common:company>
      <common:address>
        <common:streetName>Rue du Grand Duc</common:streetName>
        <common:number>13</common:number>
        <common:postalCode>1040</common:postalCode>
        <common:locality>Brussels</common:locality>
        <common:countryCode>BE</common:countryCode>
      </common:address>
      <common:emailAddress>pomme@antidot.com</common:emailAddress>
      <common:phoneNumber>026411390</common:phoneNumber>
    </receiver>
    <inNetworkCutOff>2016-03-16</inNetworkCutOff>
  </bpostAtHome>
</nationalBox>

EOF;
    }
}
