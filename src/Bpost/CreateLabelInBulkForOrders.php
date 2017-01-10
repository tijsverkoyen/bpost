<?php

namespace Bpost\BpostApiClient\Bpost;

use Bpost\BpostApiClient\Common\ValidatedValue\LabelFormat;

/**
 * Class CreateLabelInBulkForOrders
 */
class CreateLabelInBulkForOrders
{
    /**
     * @param string[] $references
     * @return string
     */
    public function getXml(array $references)
    {
        $document = new \DOMDocument('1.0', 'UTF-8');
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;

        $batchLabels = $document->createElement('batchLabels');
        $batchLabels->setAttribute('xmlns', 'http://schema.post.be/shm/deepintegration/v3/');
        foreach ($references as $reference) {
            $batchLabels->appendChild(
                $document->createElement('order', $reference)
            );
        }
        $document->appendChild($batchLabels);

        return $document->saveXML();
    }

    /**
     * @param bool $asPdf
     * @return string[]
     */
    public function getHeaders($asPdf)
    {
        return array(
            'Accept: application/vnd.bpost.shm-label-' . ($asPdf ? 'pdf' : 'image') . '-v3+XML',
            'Content-Type: application/vnd.bpost.shm-labelRequest-v3+XML',
        );
    }

    /**
     * @param LabelFormat $format
     * @param bool        $withReturnLabels
     * @return string
     */
    public function getUrl(LabelFormat $format, $withReturnLabels, $forcePrinting = false)
    {
        return '/labels/' . $format->getValue()
               . ($withReturnLabels ? '/withReturnLabels' : '')
               . ($forcePrinting ? '?forcePrinting=true' : '');
    }
}
