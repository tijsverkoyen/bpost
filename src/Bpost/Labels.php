<?php

namespace Bpost\BpostApiClient\Bpost;

/**
 * Class Labels
 */
class Labels
{
    /**
     * @param \SimpleXMLElement $xml
     *
     * @return Label[]
     */
    public static function createFromXML(\SimpleXMLElement $xml)
    {
        $labels = array();

        if (isset($xml->label)) {
            foreach ($xml->label as $label) {
                $labels[] = Label::createFromXML($label);
            }
        }

        return $labels;
    }
}
