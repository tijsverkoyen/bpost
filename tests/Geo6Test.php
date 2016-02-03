<?php

namespace TijsVerkoyen\Bpost\Geo6\test;

use TijsVerkoyen\Bpost\Exception\ApiResponseException\BpostTaxipostLocatorException;
use TijsVerkoyen\Bpost\Geo6;

class Geo6Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Geo6
     */
    private $geo6;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->geo6 = new Geo6(GEO6_PARTNER, GEO6_APP_ID);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->geo6 = null;
        parent::tearDown();
    }

    /**
     * Tests Geo6->getTimeOut()
     */
    public function testGetTimeOut()
    {
        $this->geo6->setTimeOut(5);
        $this->assertSame(5, $this->geo6->getTimeOut());
    }

    /**
     * Tests Geo6->getUserAgent()
     */
    public function testGetUserAgent()
    {
        $this->geo6->setUserAgent('testing/1.0.0');
        $this->assertSame('PHP Bpost Geo6/' . Geo6::VERSION . ' testing/1.0.0', $this->geo6->getUserAgent());
    }

    /**
     * Tests Geo6->getNearestServicePoint()
     */
    public function testGetNearestServicePoint()
    {
        $response = $this->geo6->getNearestServicePoint('Afrikalaan', '289', '9000');

        $this->assertInternalType('array', $response);

        foreach ($response as $item) {
            $this->assertArrayHasKey('poi', $item);
            $this->assertArrayHasKey('distance', $item);
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Geo6\Poi', $item['poi']);
        }
    }

    /**
     * Tests Geo6->getServicePointDetails()
     */
    public function testGetServicePointDetails()
    {
        $id = '220000';
        $type = '1';
        $response = $this->geo6->getServicePointDetails($id, 'nl', $type);

        $this->assertInstanceOf('TijsVerkoyen\Bpost\Geo6\Poi', $response);
        $this->assertSame($response->getId(), $id);
        $this->assertSame($response->getType(), $type);

        try {
            $this->geo6->getServicePointDetails('-1');
            $this->fail('BpostTaxipostLocatorException not launched');
        } catch (BpostTaxipostLocatorException $e) {
            $this->assertSame('No match for id : -1 and type : 3', $e->getMessage());
        } catch (\Exception $e) {
            $this->fail('BpostTaxipostLocatorException not caught');
        }

        try {
            $this->geo6->getServicePointDetails('0');
            $this->fail('BpostTaxipostLocatorException not launched');
        } catch (BpostTaxipostLocatorException $e) {
            $this->assertSame('Id is mandatory', $e->getMessage());
        } catch (\Exception $e) {
            $this->fail('BpostTaxipostLocatorException not caught');
        }

    }

    /**
     * Tests Geo6->getServicePointPage()
     */
    public function testGetServicePointPage()
    {
        $id = '220000';
        $type = '1';
        $response = $this->geo6->getServicePointPage($id, 'nl', $type);

        $this->assertSame(
            'http://taxipost.geo6.be/Locator?Id=' . $id . '&Language=nl&Type=' . $type . '&Function=page&Partner=' . GEO6_PARTNER . '&AppId=' . GEO6_APP_ID . '&Format=xml',
            $response
        );
    }
}
