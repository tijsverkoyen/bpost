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
    public function setInternationalBox($internationalBox)
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
     * @param \TijsVerkoyen\Bpost\Bpost\Order\Box\AtHome $nationalBox
     */
    public function setNationalBox($nationalBox)
    {
        $this->nationalBox = $nationalBox;
    }

    /**
     * @return \TijsVerkoyen\Bpost\Bpost\Order\Box\AtHome
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
    public function setSender($sender)
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
     * @return array
     */
    public function toXMLArray()
    {
        $data = array();
        if ($this->getSender() !== null) {
            $data['sender'] = $this->getSender()->toXMLArray();
        }
        if ($this->getNationalBox() !== null) {
            $data['nationalBox'] = $this->getNationalBox()->toXMLArray();
        }
        if ($this->getInternationalBox() !== null) {
            $data['internationalBox'] = $this->getInternationalBox()->toXMLArray();
        }
        if ($this->getRemark() !== null) {
            $data['remark'] = $this->getRemark();
        }

        return $data;
    }
}
