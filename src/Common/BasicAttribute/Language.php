<?php
namespace Bpost\BpostApiClient\Common\BasicAttribute;

use Bpost\BpostApiClient\Common\BasicAttribute;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;

class Language extends BasicAttribute
{
    const LANGUAGE_EN = 'EN';
    const LANGUAGE_FR = 'FR';
    const LANGUAGE_NL = 'NL';

    /**
     * @throws BpostInvalidValueException
     */
    public function validate()
    {
        $this->validateChoice(array(
            self::LANGUAGE_EN,
            self::LANGUAGE_FR,
            self::LANGUAGE_NL,
        ));
    }

    /**
     * @return string
     */
    protected function getDefaultKey()
    {
        return 'language';
    }
}
