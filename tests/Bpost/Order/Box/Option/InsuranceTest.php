<?php
namespace Bpost;

require_once __DIR__ . '/../../../../../../../autoload.php';

use TijsVerkoyen\Bpost\Bpost\Order\Box\Option\Insurance;

class InsuranceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Insurance->toXMLArray
     */
    public function testToXMLArray()
    {
        $data = array(
            'insured' => array(
                'basicInsurance' => array()
            ),
        );

        $insurance = new Insurance('basicInsurance');
        $xmlArray = $insurance->toXMLArray();
        $this->assertEquals($data, $xmlArray);

        $data = array(
            'insured' => array(
                'additionalInsurance' => array(
                    '@attributes' => array(
                        'value' => 3
                    )
                )
            ),
        );

        $insurance = new Insurance(
            'additionalInsurance',
            $data['insured']['additionalInsurance']['@attributes']['value']
        );
        $xmlArray = $insurance->toXMLArray();
        $this->assertEquals($data, $xmlArray);
    }

    /**
     * Test validation in the setters
     */
    public function testFaultyProperties()
    {
        try {
            new Insurance(
                str_repeat('a', 10)
            );
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertEquals(
                sprintf(
                    'Invalid value, possible values are: %1$s.',
                    implode(', ', Insurance::getPossibleTypeValues())
                ),
                $e->getMessage()
            );
        }

        try {
            new Insurance(
                'additionalInsurance',
                str_repeat('1', 10)
            );
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertEquals(
                sprintf(
                    'Invalid value, possible values are: %1$s.',
                    implode(', ', Insurance::getPossibleValueValues())
                ),
                $e->getMessage()
            );
        }
    }
}
