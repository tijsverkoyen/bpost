<?php
namespace Bpost\BpostApiClient\Bpost\Order\Box;

use Bpost\BpostApiClient\Bpost\Order\Receiver;
use Bpost\BpostApiClient\Exception\XmlException\BpostXmlInvalidItemException;

/**
 * Class BpostOnAppointment
 * @package Bpost\BpostApiClient\Bpost\Order\Box
 */
class BpostOnAppointment extends National
{
    /** @var Receiver */
    private $receiver;

    /** @var string */
    protected $inNetworkCutOff;

    /**
     * @param Receiver $receiver
     */
    public function setReceiver(Receiver $receiver)
    {
        $this->receiver = $receiver;
    }

    /**
     * @return Receiver
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * @return string
     */
    public function getInNetworkCutOff()
    {
        return $this->inNetworkCutOff;
    }

    /**
     * @param string $inNetworkCutOff
     */
    public function setInNetworkCutOff($inNetworkCutOff)
    {
        $this->inNetworkCutOff = (string)$inNetworkCutOff;
    }

    /**
     * Return the object as an array for usage in the XML
     *
     * @param  \DomDocument $document
     * @param  string       $prefix
     * @param  string       $type
     * @return \DomElement
     */
    public function toXML(\DOMDocument $document, $prefix = null, $type = null)
    {
        $nationalElement = $document->createElement($this->getPrefixedTagName('nationalBox', $prefix));
        $boxElement = parent::toXML($document, null, 'bpostOnAppointment');
        $nationalElement->appendChild($boxElement);

        $this->addToXmlReceiver($document, $boxElement);

        $this->addToXmlRequestedDeliveryDate($document, $boxElement, $prefix);

        return $nationalElement;
    }

    /**
     * @param \DOMDocument $document
     * @param \DOMElement  $typeElement
     */
    protected function addToXmlReceiver(\DOMDocument $document, \DOMElement $typeElement)
    {
        if ($this->getReceiver() !== null) {
            $typeElement->appendChild(
                $this->getReceiver()->toXML($document)
            );
        }
    }

    /**
     * @param \DOMDocument $document
     * @param \DOMElement  $typeElement
     * @param string       $prefix
     */
    protected function addToXmlRequestedDeliveryDate(\DOMDocument $document, \DOMElement $typeElement, $prefix)
    {
        if ($this->getInNetworkCutOff() !== null) {
            $typeElement->appendChild(
                $document->createElement(
                    $this->getPrefixedTagName('inNetworkCutOff', $prefix),
                    $this->getInNetworkCutOff()
                )
            );
        }
    }

    /**
     * @param  \SimpleXMLElement $xml
     * @return BpostOnAppointment
     * @throws BpostXmlInvalidItemException
     */
    public static function createFromXML(\SimpleXMLElement $xml)
    {
        $self = new self();

        if (!isset($xml->bpostOnAppointment)) {
            throw new BpostXmlInvalidItemException();
        }

        $bpostOnAppointmentXml = $xml->bpostOnAppointment;

        if (isset($bpostOnAppointmentXml->receiver)) {
            $self->setReceiver(
                Receiver::createFromXML(
                    $bpostOnAppointmentXml->receiver->children('http://schema.post.be/shm/deepintegration/v3/common')
                )
            );
        }

        if (isset($bpostOnAppointmentXml->inNetworkCutOff) && $bpostOnAppointmentXml->inNetworkCutOff != '') {
            $self->setInNetworkCutOff(
                (string)$bpostOnAppointmentXml->inNetworkCutOff
            );
        }

        return $self;
    }
}
