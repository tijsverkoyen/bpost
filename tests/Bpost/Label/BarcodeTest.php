<?php
namespace Bpost\BpostApiClient\Bpost\Label;

class BarcodeTest extends \PHPUnit_Framework_TestCase
{
    private function getBarcodeXml()
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<barcodeWithReference xmlns="http://schema.post.be/shm/deepintegration/v3/" xmlns:ns2="http://schema.post.be/shm/deepintegration/v3/common" xmlns:ns3="http://schema.post.be/shm/deepintegration/v3/national" xmlns:ns4="http://schema.post.be/shm/deepintegration/v3/international">
  <barcode>323299901059912015292030</barcode>
  <reference>test_barcode_with_reference</reference>
</barcodeWithReference>
XML;
    }

    public function testCreateFromXML()
    {
        $self = Barcode::createFromXML(new \SimpleXMLElement($this->getBarcodeXml()));

        $this->assertSame('323299901059912015292030', $self->getBarcode());
        $this->assertSame('test_barcode_with_reference', $self->getReference());
    }
}
