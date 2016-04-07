<?php

namespace Geo6;

use Bpost\BpostApiClient\Geo6\Poi;

class PoiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Poi::createFromXml()
     */
    public function testCreateFromXml()
    {
        $xml = simplexml_load_string($this->getXml());

        $poi = Poi::createFromXML($xml);

        $this->assertSame('220000', $poi->getId());
        $this->assertSame('1', $poi->getType());
        $this->assertSame('GENT CENTRUM', $poi->getOffice());
        $this->assertSame('Lange Kruisstraat', $poi->getStreet());
        $this->assertSame('55', $poi->getNr());
        $this->assertSame('9000', $poi->getZip());
        $this->assertSame('Gent', $poi->getCity());
        $this->assertSame(104918, $poi->getX());
        $this->assertSame(193708, $poi->getY());
        $this->assertSame(3.72581, $poi->getLongitude());
        $this->assertSame(51.05178, $poi->getLatitude());
        $this->assertNotNull($poi->getHours());

        $hours = $poi->getHours();
        $this->assertCount(7, $hours);
        $this->assertArrayHasKey(1, $hours); // Monday

        $this->assertSame('9:00', $hours[1]->getAmOpen());
        $this->assertNull($hours[1]->getAmClose());
        $this->assertNull($hours[1]->getPmOpen());
        $this->assertSame('18:00', $hours[1]->getPmClose());

        $this->assertNull($poi->getClosedFrom());
        $this->assertNull($poi->getClosedTo());
        $this->assertNull($poi->getNote());

    }

    /**
     * @return string
     */
    private function getXml()
    {
        return <<< XML
<Record>
  <ID>220000</ID>
  <Type>1</Type>
  <OFFICE>GENT CENTRUM</OFFICE>
  <STREET>Lange Kruisstraat</STREET>
  <NR>55</NR>
  <ZIP>9000</ZIP>
  <CITY>Gent</CITY>
  <X>104918</X>
  <Y>193708</Y>
  <Longitude>3.72581</Longitude>
  <Latitude>51.05178</Latitude>
  <Services>
    <Service category="2" flag="10">Loket met Bancontact/Mistercash</Service>
  </Services>
  <Hours>
    <Monday>
      <AMOpen>9:00</AMOpen>
      <AMClose></AMClose>
      <PMOpen></PMOpen>
      <PMClose>18:00</PMClose>
    </Monday>
    <Tuesday>
      <AMOpen>9:00</AMOpen>
      <AMClose></AMClose>
      <PMOpen></PMOpen>
      <PMClose>18:00</PMClose>
    </Tuesday>
    <Wednesday>
      <AMOpen>9:00</AMOpen>
      <AMClose></AMClose>
      <PMOpen></PMOpen>
      <PMClose>18:00</PMClose>
    </Wednesday>
    <Thursday>
      <AMOpen>9:00</AMOpen>
      <AMClose></AMClose>
      <PMOpen></PMOpen>
      <PMClose>18:00</PMClose>
    </Thursday>
    <Friday>
      <AMOpen>9:00</AMOpen>
      <AMClose></AMClose>
      <PMOpen></PMOpen>
      <PMClose>18:00</PMClose>
    </Friday>
    <Saturday>
      <AMOpen>9:00</AMOpen>
      <AMClose></AMClose>
      <PMOpen></PMOpen>
      <PMClose>15:00</PMClose>
    </Saturday>
    <Sunday>
      <AMOpen></AMOpen>
      <AMClose></AMClose>
      <PMOpen></PMOpen>
      <PMClose></PMClose>
    </Sunday>
  </Hours>
  <ClosedFrom></ClosedFrom>
  <ClosedTo></ClosedTo>
  <NOTE></NOTE>
</Record>
XML;

    }
}
