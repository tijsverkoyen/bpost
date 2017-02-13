<?php
namespace Bpost;

use Bpost\BpostApiClient\Bpost\Order\Address;
use Bpost\BpostApiClient\Bpost\Order\Box\AtHome;
use Bpost\BpostApiClient\Bpost\Order\Receiver;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;

class AtHomeTest extends \PHPUnit_Framework_TestCase
{
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
        $receiver->setEmailAddress('dev.null@antidot.com');
        $receiver->setCompany('Antidot');
        $receiver->setAddress($address);
        $receiver->setPhoneNumber('0032475123456');

        $self = new AtHome();
        $self->setProduct('bpack 24h Pro');
        $self->setRequestedDeliveryDate('2016-03-16');
        $self->setReceiver($receiver);

        // Normal
        $rootDom = $this->createDomDocument();
        $document = $this->generateDomDocument($rootDom, $self->toXML($rootDom, 'tns'));

        $this->assertSame($this->getXml(), $document->saveXML());
    }

    public function testCreateFromNormalXml()
    {
        $self = AtHome::createFromXML(new \SimpleXMLElement($this->getXml()));

        $this->assertSame('2016-03-16', $self->getRequestedDeliveryDate());

        $this->assertNotNull($self->getReceiver());
        $this->assertSame('Antidot', $self->getReceiver()->getCompany());
    }

    public function testCreateFromBadXml()
    {
        $this->setExpectedException('Bpost\BpostApiClient\Exception\XmlException\BpostXmlInvalidItemException');
        AtHome::createFromXML(new \SimpleXMLElement($this->getNotAtHomeXml()));
    }

    /**
     * Test validation in the setters
     */
    public function testFaultyProperties()
    {
        $atHome = new AtHome();

        try {
            $atHome->setProduct(str_repeat('a', 10));
            $this->fail('BpostInvalidValueException not launched');
        } catch (BpostInvalidValueException $e) {
            // Nothing, the exception is good
        } catch (\Exception $e) {
            $this->fail('BpostInvalidValueException not caught');
        }

        // Exceptions were caught,
        $this->assertTrue(true);
    }

    private function getXml()
    {
        return <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<tns:nationalBox xmlns="http://schema.post.be/shm/deepintegration/v3/national" xmlns:common="http://schema.post.be/shm/deepintegration/v3/common" xmlns:tns="http://schema.post.be/shm/deepintegration/v3/" xmlns:international="http://schema.post.be/shm/deepintegration/v3/international" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://schema.post.be/shm/deepintegration/v3/">
  <atHome>
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
      <common:emailAddress>dev.null@antidot.com</common:emailAddress>
      <common:phoneNumber>0032475123456</common:phoneNumber>
    </receiver>
    <requestedDeliveryDate>2016-03-16</requestedDeliveryDate>
  </atHome>
</tns:nationalBox>

EOF;
    }

    private function getNotAtHomeXml()
    {
        return <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<tns:nationalBox xmlns="http://schema.post.be/shm/deepintegration/v3/national" xmlns:common="http://schema.post.be/shm/deepintegration/v3/common" xmlns:tns="http://schema.post.be/shm/deepintegration/v3/" xmlns:international="http://schema.post.be/shm/deepintegration/v3/international" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://schema.post.be/shm/deepintegration/v3/">
  <notAtHome>
    <product>bpack 24h Pro</product>
  </notAtHome>
</tns:nationalBox>

EOF;
    }
}
