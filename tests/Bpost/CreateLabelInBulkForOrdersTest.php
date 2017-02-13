<?php
namespace Bpost;

use Bpost\BpostApiClient\Bpost\CreateLabelInBulkForOrders;
use Bpost\BpostApiClient\Common\ValidatedValue\LabelFormat;

class CreateLabelInBulkForOrdersTest extends \PHPUnit_Framework_TestCase
{
    public function testGetXml()
    {
        $self = new CreateLabelInBulkForOrders();
        $this->assertSame($this->getExpectedXml(), $self->getXml(array(
            'order_ref_1',
            'order_ref_2'
        )));
    }

    public function testGetUrl()
    {
        $self = new CreateLabelInBulkForOrders();
        $this->assertSame(
            '/labels/A4',
            $self->getUrl(new LabelFormat(LabelFormat::FORMAT_A4), false)
        );
        $this->assertSame(
            '/labels/A6/withReturnLabels',
            $self->getUrl(new LabelFormat(LabelFormat::FORMAT_A6), true)
        );
        $this->assertSame(
            '/labels/A6?forcePrinting=true',
            $self->getUrl(new LabelFormat(LabelFormat::FORMAT_A6), false, true)
        );
        $this->assertSame(
            '/labels/A4/withReturnLabels?forcePrinting=true',
            $self->getUrl(new LabelFormat(LabelFormat::FORMAT_A4), true, true)
        );
    }

    public function testHeaders()
    {
        $self = new CreateLabelInBulkForOrders();
        $this->assertSame(
            array(
                'Accept: application/vnd.bpost.shm-label-pdf-v3+XML',
                'Content-Type: application/vnd.bpost.shm-labelRequest-v3+XML',
            ),
            $self->getHeaders(true)
        );
        $this->assertSame(
            array(
                'Accept: application/vnd.bpost.shm-label-image-v3+XML',
                'Content-Type: application/vnd.bpost.shm-labelRequest-v3+XML',
            ),
            $self->getHeaders(false)
        );
    }

    private function getExpectedXml()
    {
        return <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<batchLabels xmlns="http://schema.post.be/shm/deepintegration/v3/">
  <order>order_ref_1</order>
  <order>order_ref_2</order>
</batchLabels>

XML;
    }
}
