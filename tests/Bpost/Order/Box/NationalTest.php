<?php
namespace Bpost;

use Bpost\BpostApiClient\Bpost\Order\Box\National;
use Bpost\BpostApiClient\Bpost\Order\Box\Openinghour\Day;
use Bpost\BpostApiClient\Bpost\Order\Box\Option\Messaging;
use Bpost\BpostApiClient\Bpost\Order\Box\Option\SaturdayDelivery;
use Bpost\BpostApiClient\Bpost\ProductConfiguration\Option;

class NationalFake extends National
{


    /**
     * Return the object as an array for usage in the XML
     *
     * @param  \DomDocument $document
     * @param  string       $prefix
     * @param  string       $type
     * @return \DomElement
     */
    public function toXML(\DOMDocument $document, $prefix = null, $type = null)
    {
        $nationalElement = $document->createElement($this->getPrefixedTagName('nationalBox', $prefix));
        $boxElement = parent::toXML($document, null, 'nationalFake');
        $nationalElement->appendChild($boxElement);
        return $nationalElement;
    }

    /**
     * @param  \SimpleXMLElement $xml
     * @return self
     */
    public static function createFromXML(\SimpleXMLElement $xml)
    {
        return parent::createFromXML($xml->nationalFake, new self());
    }
}

class NationalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the methods that are implemented by the children
     * But we would like to have full coverage... Stats-porn!
     */
    public function testMethodsThatAreImplementedByChildren()
    {
        $possibleProductValues = National::getPossibleProductValues();
        $this->assertInternalType('array', $possibleProductValues);
        $this->assertEmpty($possibleProductValues);
    }

    public function testToXml()
    {
        $self = new NationalFake();
        $self->setProduct('bpack 24h Pro');

        $self->setOptions(array(
            new Messaging('infoDistributed', 'EN', null, '0476123456'),
            new Messaging('infoNextDay', 'EN', 'receiver@mail.be')
        ));
        $self->addOption(new Messaging('infoReminder', 'EN', null, '0032475123456'));
        $self->addOption(new SaturdayDelivery());
        $self->setWeight(500);

        $self->setOpeningHours(array(
            new Day('Monday', '07:00-15:00'),
            new Day('Tuesday', '07:00-15:00'),
            new Day('Wednesday', '-/-'),
            new Day('Thursday', '07:00-15:00'),
            new Day('Friday', '10:00-12:00/13:00-17:30'),
        ));
        $self->addOpeningHour(new Day('Saturday', '10:00-12:00/13:00-17:30'));

        $self->setDesiredDeliveryPlace('Place your delivery instructions here');

        // Normal
        $rootDom = $this->createDomDocument();
        $document = $this->generateDomDocument($rootDom, $self->toXML($rootDom, 'tns'));

        $this->assertSame($this->getXml(), $document->saveXML());
    }

    public function testCreateFromXml()
    {
        $self = NationalFake::createFromXML(new \SimpleXMLElement($this->getXml()));

        $this->assertSame('bpack 24h Pro', $self->getProduct());

        /** @var Option[] $options */
        $options = $self->getOptions();
        $this->assertNotNull($options);
        // @todo Fix options feeding and test it

        $this->assertSame(500, $self->getWeight());

        $openingHours = $self->getOpeningHours();
        $this->assertCount(6, $openingHours);
        $this->assertSame('-/-', $openingHours[2]->getValue());

        $this->assertSame('Place your delivery instructions here', $self->getDesiredDeliveryPlace());
    }

    private function getXml()
    {
        return <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<tns:nationalBox xmlns="http://schema.post.be/shm/deepintegration/v3/national" xmlns:common="http://schema.post.be/shm/deepintegration/v3/common" xmlns:tns="http://schema.post.be/shm/deepintegration/v3/" xmlns:international="http://schema.post.be/shm/deepintegration/v3/international" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://schema.post.be/shm/deepintegration/v3/">
  <nationalFake>
    <product>bpack 24h Pro</product>
    <options>
      <common:infoDistributed language="EN">
        <common:mobilePhone>0476123456</common:mobilePhone>
      </common:infoDistributed>
      <common:infoNextDay language="EN">
        <common:emailAddress>receiver@mail.be</common:emailAddress>
      </common:infoNextDay>
      <common:infoReminder language="EN">
        <common:mobilePhone>0032475123456</common:mobilePhone>
      </common:infoReminder>
      <common:saturdayDelivery/>
    </options>
    <weight>500</weight>
    <openingHours>
      <Monday>07:00-15:00</Monday>
      <Tuesday>07:00-15:00</Tuesday>
      <Wednesday>-/-</Wednesday>
      <Thursday>07:00-15:00</Thursday>
      <Friday>10:00-12:00/13:00-17:30</Friday>
      <Saturday>10:00-12:00/13:00-17:30</Saturday>
    </openingHours>
    <desiredDeliveryPlace>Place your delivery instructions here</desiredDeliveryPlace>
  </nationalFake>
</tns:nationalBox>

EOF;
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
