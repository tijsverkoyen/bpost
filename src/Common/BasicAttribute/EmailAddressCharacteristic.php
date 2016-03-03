<?php
namespace TijsVerkoyen\Bpost\Common\BasicAttribute;

use TijsVerkoyen\Bpost\Common\BasicAttribute;
use TijsVerkoyen\Bpost\Exception\BpostLogicException\BpostInvalidLengthException;
use TijsVerkoyen\Bpost\Exception\BpostLogicException\BpostInvalidPatternException;

class EmailAddressCharacteristic extends BasicAttribute
{

    /**
     * @throws BpostInvalidLengthException
     * @throws BpostInvalidPatternException
     */
    public function validate()
    {
        $this->validateLength(40);
        $this->validatePattern('([a-zA-Z0-9_\.\-+])+@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+');
    }

    /**
     * @return string
     */
    protected function getDefaultKey()
    {
        return 'emailAddress';
    }
}
