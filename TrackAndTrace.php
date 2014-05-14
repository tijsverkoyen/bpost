<?php
namespace TijsVerkoyen\Bpost;

use TijsVerkoyen\Bpost\Exception;

/**
 * Track&Trace class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 * @version   3.0.1
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class TrackAndTrace
{
    /**
     * Get a direct link to the track & trace system based on the barcode(s)
     *
     * @param array       $barcodes   The barcode(s)
     * @param string      $language   The language for the landing page, possible
     *                                values are: nl, fr, en, de.
     * @param string|null $passphrase If provided the detailpages will include
     *                                more in depth information, should only be
     *                                used internally and not exposed to the
     *                                public.
     * @return string
     */
    public function getDeepLink(array $barcodes, $language = 'nl', $passphrase = null)
    {
        $baseUrl = 'http://track.bpost.be/etr/light/performSearch.do';

        $parameters = array();
        $parameters['searchByItemCode'] = 'true';
        $parameters['itemCodes'] = implode(',', $barcodes);
        $parameters['oss_language'] = (string) $language;

        if ($passphrase != '') {
            $base = implode(',', $barcodes);
            $base .= $passphrase;
            $parameters['checksum'] = hash('sha256', $base);
        }

        return $baseUrl . '?' . http_build_query($parameters);
    }
}
