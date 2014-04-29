<?php
namespace TijsVerkoyen\Bpost\Bpost\Order;

/**
 * bPost Box class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class Box
{
    /**
     * @var \TijsVerkoyen\Bpost\Bpost\Order\Sender
     */
    private $sender;

    /**
     * @var \TijsVerkoyen\Bpost\Bpost\Order\Box\AtHome
     */
    private $nationalBox;

    /**
     * @var \TijsVerkoyen\Bpost\Bpost\Order\Box\International
     */
    private $internationalBox;

    /**
     * @var string
     */
    private $remark;

    /**
     * @param \TijsVerkoyen\Bpost\Bpost\Order\Box\International $internationalBox
     */
    public function setInternationalBox(Box\International $internationalBox)
    {
        $this->internationalBox = $internationalBox;
    }

    /**
     * @return \TijsVerkoyen\Bpost\Bpost\Order\Box\International
     */
    public function getInternationalBox()
    {
        return $this->internationalBox;
    }

    /**
     * @param \TijsVerkoyen\Bpost\Bpost\Order\Box\National $nationalBox
     */
    public function setNationalBox(Box\National $nationalBox)
    {
        $this->nationalBox = $nationalBox;
    }

    /**
     * @return \TijsVerkoyen\Bpost\Bpost\Order\Box\National
     */
    public function getNationalBox()
    {
        return $this->nationalBox;
    }

    /**
     * @param string $remark
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;
    }

    /**
     * @return string
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * @param \TijsVerkoyen\Bpost\Bpost\Order\Sender $sender
     */
    public function setSender(Sender $sender)
    {
        $this->sender = $sender;
    }

    /**
     * @return \TijsVerkoyen\Bpost\Bpost\Order\Sender
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Return the object as an array for usage in the XML
     *
     * @param  \DomDocument $document
     * @param  string       $prefix
     * @return \DomElement
     */
    public function toXML(\DOMDocument $document, $prefix = null)
    {
        $tagName = 'box';
        if ($prefix !== null) {
            $tagName = $prefix . ':' . $tagName;
        }

        $box = $document->createElement($tagName);

        if ($this->getSender() !== null) {
            $box->appendChild(
                $this->getSender()->toXML($document, $prefix)
            );
        }
        if ($this->getNationalBox() !== null) {
            $box->appendChild(
                $this->getNationalBox()->toXML($document, $prefix)
            );
        }
        if ($this->getInternationalBox() !== null) {
            $box->appendChild(
                $this->getInternationalBox()->toXML($document, $prefix)
            );
        }
        if ($this->getRemark() !== null) {
            $tagName = 'remark';
            if ($prefix !== null) {
                $tagName = $prefix . ':' . $tagName;
            }
            $box->appendChild(
                $document->createElement(
                    $tagName,
                    $this->getRemark()
                )
            );
        }

        return $box;
    }
}
