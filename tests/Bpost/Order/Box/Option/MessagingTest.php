<?php
namespace Bpost;

require_once __DIR__ . '/../../../../../../../autoload.php';

use TijsVerkoyen\Bpost\Bpost\Order\Box\Option\Messaging;

class MessagingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Messaging->toXMLArray
     */
    public function testToXMLArray()
    {
        $data = array(
            'infoDistributed' => array(
                '@attributes' => array(
                    'language' => 'EN',
                ),
                'mobilePhone' => '0495151689',
            ),
        );
        $messaging = new Messaging(
            'infoDistributed',
            $data['infoDistributed']['@attributes']['language'],
            null,
            $data['infoDistributed']['mobilePhone']
        );

        $xmlArray = $messaging->toXMLArray();
        $this->assertEquals($data, $xmlArray);

        $data = array(
            'infoNextDay' => array(
                '@attributes' => array(
                    'language' => 'EN',
                ),
                'emailAddress' => 'someone@test.com',
            ),
        );
        $messaging = new Messaging(
            'infoNextDay',
            $data['infoNextDay']['@attributes']['language'],
            $data['infoNextDay']['emailAddress']
        );

        $xmlArray = $messaging->toXMLArray();
        $this->assertEquals($data, $xmlArray);
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
