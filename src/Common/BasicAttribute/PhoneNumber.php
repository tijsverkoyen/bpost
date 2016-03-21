<?php
namespace Bpost\BpostApiClient\Common\BasicAttribute;

use Bpost\BpostApiClient\Common\BasicAttribute;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException;

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
