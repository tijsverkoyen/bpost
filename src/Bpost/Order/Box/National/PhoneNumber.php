<?php
namespace TijsVerkoyen\Bpost\Bpost\Order\Box\National;

use TijsVerkoyen\Bpost\BasicAttribute;

class PhoneNumber extends BasicAttribute
{

    public function validate()
    {
        $this->validateLength(20);
    }

    /**
     * @return string
     */
    protected function getDefaultKey()
    {
        return 'phoneNumber';
    }
}
