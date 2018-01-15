<?php

namespace Curve\DB;

/**
 * Generic DB exception
 */
class Exception extends \Exception
{
    const CODE_DUPLICATE_ENTRY     = 1062;
    const CODE_INVALID_FOREIGN_KEY = 1452;
}