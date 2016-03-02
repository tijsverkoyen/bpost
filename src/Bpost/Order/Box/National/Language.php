<?php
namespace TijsVerkoyen\Bpost\Bpost\Order\Box\National;

use TijsVerkoyen\Bpost\BasicAttribute;

class Language extends BasicAttribute
{

    const LANGUAGE_EN = 'EN';
    const LANGUAGE_FR = 'FR';
    const LANGUAGE_NL = 'NL';

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
