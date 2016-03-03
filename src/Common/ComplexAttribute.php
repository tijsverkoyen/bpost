<?php

namespace TijsVerkoyen\Bpost\Common;

abstract class ComplexAttribute
{
    /**
     * Prefix $tagName with the $prefix, if needed
     * @param string $prefix
     * @param string $tagName
     * @return string
     */
    public function getPrefixedTagName($tagName, $prefix = null)
    {
        if (empty($prefix)) {
            return $tagName;
        }
        return $prefix . ':' . $tagName;
    }
}
