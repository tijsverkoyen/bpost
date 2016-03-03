<?php
namespace TijsVerkoyen\Bpost\Bpost\Order\Box\National;

use TijsVerkoyen\Bpost\Common\BasicAttribute;

class ShopHandlingInstruction extends BasicAttribute
{

    public function validate()
    {
        $this->validateLength(50);
    }

    /**
     * @return string
     */
    protected function getDefaultKey()
    {
        return 'shopHandlingInstruction';
    }
}
