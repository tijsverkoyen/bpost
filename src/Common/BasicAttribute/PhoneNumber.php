<?php
namespace TijsVerkoyen\Bpost\Common\BasicAttribute;

use TijsVerkoyen\Bpost\Common\BasicAttribute;
use TijsVerkoyen\Bpost\Exception\BpostLogicException\BpostInvalidLengthException;

class PhoneNumber extends BasicAttribute
{

    /**
     * @throws BpostInvalidLengthException
     */
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
