<?php
namespace Bpost\BpostApiClient;

/**
 * bPost Exception class
 *
 * Class used for the retro-compatibility (Before, we had Bpost\BpostApiClient\Exception which extended \Exception)
 *
 * @deprecated Use BpostException
 * @see        BpostException
 */
class Exception extends BpostException
{
}
