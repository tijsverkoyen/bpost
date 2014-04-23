<?php
namespace Bpost;

require_once __DIR__ . '/../../../../../../../autoload.php';

use TijsVerkoyen\Bpost\Bpost\Order\Box\Option\CashOnDelivery;

class CashOnDeliveryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests CashOnDelivery->toXMLArray
     */
    public function testToXMLArray()
    {
        $data = array(
            'cod' => array(
                'codAmount' => 1251,
                'iban' => 'BE19210023508812',
                'bic' => 'GEBABEBB',
            ),
        );
        $cashOnDelivery = new CashOnDelivery(
            $data['cod']['codAmount'],
            $data['cod']['iban'],
            $data['cod']['bic']
        );
        $xmlArray = $cashOnDelivery->toXMLArray();
        $this->assertEquals($data, $xmlArray);
    }
}
