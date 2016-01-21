<?php
namespace TijsVerkoyen\Bpost;

/**
 * bPost Exception class
 *
 * Class used for the retro-compatibility (Before, we had TijsVerkoyen\Bpost\Exception which extended \Exception)
 *
 * @deprecated Use BpostException
 * @see        BpostException
 */
class Exception extends BpostException
{
}
