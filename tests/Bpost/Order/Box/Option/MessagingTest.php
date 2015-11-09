<?php
namespace Bpost;

use TijsVerkoyen\Bpost\Bpost\Order\Box\Option\Messaging;

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
            new Messaging(
                str_repeat('a', 10),
                'NL'
            );
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertEquals(
                sprintf(
                    'Invalid value, possible values are: %1$s.',
                    implode(', ', Messaging::getPossibleTypeValues())
                ),
                $e->getMessage()
            );
        }

        try {
            new Messaging(
                'infoDistributed',
                str_repeat('a', 10)
            );
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertEquals(
                sprintf(
                    'Invalid value, possible values are: %1$s.',
                    implode(', ', Messaging::getPossibleLanguageValues())
                ),
                $e->getMessage()
            );
        }

        try {
            new Messaging(
                'infoDistributed',
                'NL',
                str_repeat('a', 51)
            );
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertEquals('Invalid length, maximum is 50.', $e->getMessage());
        }

        try {
            new Messaging(
                'infoDistributed',
                'NL',
                null,
                str_repeat('a', 21)
            );
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertEquals('Invalid length, maximum is 20.', $e->getMessage());
        }
    }
}
