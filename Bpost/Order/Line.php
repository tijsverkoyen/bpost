<?php
namespace TijsVerkoyen\Bpost\Bpost\Order;

/**
 * bPost Line class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class Line
{
    /**
     * @var string
     */
    private $text;

    /**
     * @var int
     */
    private $numberOfItems;

    /**
     * @param int $nbOfItems
     */
    public function setNumberOfItems($nbOfItems)
    {
        $this->numberOfItems = $nbOfItems;
    }

    /**
     * @return int
     */
    public function getNumberOfItems()
    {
        return $this->numberOfItems;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @param int    $numberOfItems
     */
    public function __construct($text = null, $numberOfItems = null)
    {
        if ($text != null) {
            $this->setText($text);
        }
        if ($numberOfItems != null) {
            $this->setNumberOfItems($numberOfItems);
        }
    }

    /**
     * Return the object as an array for usage in the XML
     *
     * @return array
     */
    public function toXMLArray()
    {
        $data = array();
        if ($this->getText() !== null) {
            $data['text'] = $this->getText();
        }
        if ($this->getNumberOfItems() !== null) {
            $data['nbOfItems'] = $this->getNumberOfItems();
        }

        return $data;
    }
}
