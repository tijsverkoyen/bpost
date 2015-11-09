<?php
namespace Bpost;

use TijsVerkoyen\Bpost\Bpost\Order\Box\Option\CashOnDelivery;

class CashOnDeliveryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Create a generic DOM Document
     *
     * @return \DOMDocument
     */
    private static function createDomDocument()
    {
        $document = new \DOMDocument('1.0', 'utf-8');
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;

        return $document;
    }

    /**
     * Tests CashOnDelivery->toXML
     */
    public function testToXML()
    {
        $data = array(
            'cod' => array(
                'codAmount' => 1251,
                'iban' => 'BE19210023508812',
                'bic' => 'GEBABEBB',
            ),
        );

        $expectedDocument = self::createDomDocument();
        $cod = $expectedDocument->createElement('cod');
        foreach ($data['cod'] as $key => $value) {
            $cod->appendChild(
                $expectedDocument->createElement($key, $value)
            );
        }
        $expectedDocument->appendChild($cod);

        $actualDocument = self::createDomDocument();
        $cashOnDelivery = new CashOnDelivery(
            $data['cod']['codAmount'],
            $data['cod']['iban'],
            $data['cod']['bic']
        );
        $actualDocument->appendChild(
            $cashOnDelivery->toXML($actualDocument)
        );

        $this->assertEquals($expectedDocument, $actualDocument);

        $data = array(
            'cod' => array(
                'codAmount' => 1251,
                'iban' => 'BE19210023508812',
                'bic' => 'GEBABEBB',
            ),
        );

        $expectedDocument = self::createDomDocument();
        $cod = $expectedDocument->createElement('foo:cod');
        foreach ($data['cod'] as $key => $value) {
            $cod->appendChild(
                $expectedDocument->createElement('foo:'. $key, $value)
            );
        }
        $expectedDocument->appendChild($cod);

        $actualDocument = self::createDomDocument();
        $cashOnDelivery = new CashOnDelivery(
            $data['cod']['codAmount'],
            $data['cod']['iban'],
            $data['cod']['bic']
        );
        $actualDocument->appendChild(
            $cashOnDelivery->toXML($actualDocument, 'foo')
        );

        $this->assertEquals($expectedDocument->saveXML(), $actualDocument->saveXML());
    }
}
