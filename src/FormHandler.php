<?php
namespace Bpost\BpostApiClient;

use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;

/**
 * bPost Form handler class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class FormHandler
{
    /**
     * bPost instance
     *
     * @var Bpost
     */
    private $bpost;

    /**
     * The parameters
     *
     * @var array
     */
    private $parameters = array();

    /**
     * Create bPostFormHandler instance
     *
     * @param string $accountId
     * @param string $passPhrase
     * @param string $apiUrl
     */
    public function __construct($accountId, $passPhrase, $apiUrl = Bpost::API_URL)
    {
        $this->bpost = new Bpost($accountId, $passPhrase, $apiUrl);
    }

    /**
     * Calculate the hash
     *
     * @return string
     */
    private function getChecksum()
    {
        $keysToHash = array(
            'accountId',
            'action',
            'costCenter',
            'customerCountry',
            'deliveryMethodOverrides',
            'extraSecure',
            'orderReference',
            'orderWeight',
        );
        $base = 'accountId=' . $this->bpost->getAccountId() . '&';

        foreach ($keysToHash as $key) {
            if (isset($this->parameters[$key])) {
                 if (! is_array($this->parameters[$key])) {
                    $base .= $key.'='.$this->parameters[$key].'&';
                } else {
                    foreach ($this->parameters[$key] as $entry) {
                        $base .= $key.'='.$entry.'&';
                    }
                }
            }
        }

        // add passphrase
        $base .= $this->bpost->getPassPhrase();

        // return the hash
        return hash('sha256', $base);
    }

    /**
     * Get the parameters
     *
     * @param  bool  $form
     * @param  bool  $includeChecksum
     * @return array
     */
    public function getParameters($form = false, $includeChecksum = true)
    {
        $return = $this->parameters;

        if ($form && isset($return['orderLine'])) {
            foreach ($return['orderLine'] as $key => $value) {
                $return['orderLine[' . $key . ']'] = $value;
            }

            unset($return['orderLine']);
        }

        if ($includeChecksum) {
            $return['accountId'] = $this->bpost->getAccountId();
            $return['checksum'] = $this->getChecksum();
        }

        return $return;
    }

    /**
     * Set a parameter
     *
     * @param string $key
     * @param mixed  $value
     * @throws BpostInvalidValueException
     * @throws BpostInvalidLengthException
     */
    public function setParameter($key, $value)
    {
        switch ((string) $key) {
            // limited values
            case 'action':
            case 'lang':
                $allowedValues['action'] = array('START', 'CONFIRM');
                $allowedValues['lang'] = array('NL', 'FR', 'EN', 'DE', 'Default');

                if (!in_array($value, $allowedValues[$key])) {
                    throw new BpostInvalidValueException($key, $value, $allowedValues[$key]);
                }
                $this->parameters[$key] = $value;
                break;

            // maximum 2 chars
            case 'customerCountry':
                if (mb_strlen($value) > 2) {
                    throw new BpostInvalidLengthException($key, mb_strlen($value), 2);
                }
                $this->parameters[$key] = (string) $value;
                break;

            // maximum 8 chars
            case 'customerStreetNumber':
            case 'customerBox':
                if (mb_strlen($value) > 8) {
                    throw new BpostInvalidLengthException($key, mb_strlen($value), 8);
                }
                $this->parameters[$key] = (string) $value;
                break;

            // maximum 20 chars
            case 'customerPhoneNumber':
                if (mb_strlen($value) > 20) {
                    throw new BpostInvalidLengthException($key, mb_strlen($value), 20);
                }
                $this->parameters[$key] = (string) $value;
                break;

            // maximum 32 chars
            case 'customerPostalCode':
                if (mb_strlen($value) > 32) {
                    throw new BpostInvalidLengthException($key, mb_strlen($value), 32);
                }
                $this->parameters[$key] = (string) $value;
                break;

            // maximum 40 chars
            case 'customerFirstName':
            case 'customerLastName':
            case 'customerCompany':
            case 'customerStreet':
            case 'customerCity':
                if (mb_strlen($value) > 40) {
                    throw new BpostInvalidLengthException($key, mb_strlen($value), 40);
                }
                $this->parameters[$key] = (string) $value;
                break;

            // maximum 50 chars
            case 'orderReference':
            case 'costCenter':
            case 'customerEmail':
                if (mb_strlen($value) > 50) {
                    throw new BpostInvalidLengthException($key, mb_strlen($value), 50);
                }
                $this->parameters[$key] = (string) $value;
                break;

            // integers
            case 'orderTotalPrice':
            case 'orderWeight':
                $this->parameters[$key] = (int) $value;
                break;

            // array
            case 'orderLine':
                if (!isset($this->parameters[$key])) {
                    $this->parameters[$key] = array();
                }
                $this->parameters[$key][] = $value;
                break;

            // unknown
            case 'deliveryMethodOverrides':
            case 'extra':
            case 'extraSecure':
            case 'confirmUrl':
            case 'cancelUrl':
            case 'errorUrl':
            default:
                if (is_array($value)) {
                    sort($value);
                }
                $this->parameters[$key] = $value;
        }
    }
}
