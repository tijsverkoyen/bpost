<?php
namespace Bpost;

use TijsVerkoyen\Bpost\Bpost\Order\Box\Option\Messaging;
use TijsVerkoyen\Bpost\Exception\LogicException\BpostInvalidLengthException;
use TijsVerkoyen\Bpost\Exception\LogicException\BpostInvalidValueException;

class MessagingTest extends \PHPUnit_Framework_TestCase
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
     * Tests Messaging->ToXML
     */
    public function testToXML()
    {
        $data = array(
            'infoDistributed' => array(
                '@attributes' => array(
                    'language' => 'EN',
                ),
                'mobilePhone' => '0495151689',
            ),
        );

        $expectedDocument = self::createDomDocument();
        $infoDistributed = $expectedDocument->createElement('infoDistributed');
        $infoDistributed->setAttribute('language', $data['infoDistributed']['@attributes']['language']);
        $infoDistributed->appendChild(
            $expectedDocument->createElement(
                'mobilePhone',
                $data['infoDistributed']['mobilePhone']
            )
        );
        $expectedDocument->appendChild($infoDistributed);

        $actualDocument = self::createDomDocument();
        $messaging = new Messaging(
            'infoDistributed',
            $data['infoDistributed']['@attributes']['language'],
            null,
            $data['infoDistributed']['mobilePhone']
        );
        $actualDocument->appendChild(
            $messaging->toXML($actualDocument, null)
        );
        $this->assertEquals($expectedDocument, $actualDocument);

        $data = array(
            'infoNextDay' => array(
                '@attributes' => array(
                    'language' => 'EN',
                ),
                'emailAddress' => 'someone@test.com',
            ),
        );

        $expectedDocument = self::createDomDocument();
        $infoNextDay = $expectedDocument->createElement('infoNextDay');
        $infoNextDay->setAttribute('language', $data['infoNextDay']['@attributes']['language']);
        $infoNextDay->appendChild(
            $expectedDocument->createElement(
                'emailAddress',
                $data['infoNextDay']['emailAddress']
            )
        );
        $expectedDocument->appendChild($infoNextDay);

        $actualDocument = self::createDomDocument();
        $messaging = new Messaging(
            'infoNextDay',
            $data['infoNextDay']['@attributes']['language'],
            $data['infoNextDay']['emailAddress']
        );
        $actualDocument->appendChild(
            $messaging->toXML($actualDocument, null)
        );
        $this->assertEquals($expectedDocument, $actualDocument);
    }

    /**
     * Test validation in the setters
     */
    public function testFaultyProperties()
    {
        try {
            new Messaging(str_repeat('a', 10), 'NL');
            $this->fail('BpostInvalidValueException not launched');
        } catch (BpostInvalidValueException $e) {
            // Nothing, the exception is good
        } catch (\Exception $e) {
            $this->fail('BpostInvalidValueException not caught');
        }

        try {
            new Messaging('infoDistributed', str_repeat('a', 10));
            $this->fail('BpostInvalidValueException not launched');
        } catch (BpostInvalidValueException $e) {
            // Nothing, the exception is good
        } catch (\Exception $e) {
            $this->fail('BpostInvalidValueException not caught');
        }

        try {
            new Messaging('infoDistributed', 'NL', str_repeat('a', 51));
            $this->fail('BpostInvalidLengthException not launched');
        } catch (BpostInvalidLengthException $e) {
            // Nothing, the exception is good
        } catch (\Exception $e) {
            $this->fail('BpostInvalidLengthException not caught');
        }

        try {
            new Messaging('infoDistributed', 'NL', null, str_repeat('a', 21));
            $this->fail('BpostInvalidLengthException not launched');
        } catch (BpostInvalidLengthException $e) {
            // Nothing, the exception is good
        } catch (\Exception $e) {
            $this->fail('BpostInvalidLengthException not caught');
        }

        // Exceptions were caught,
        $this->assertTrue(true);
    }
}
