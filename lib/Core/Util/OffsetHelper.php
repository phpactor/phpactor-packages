<?php

namespace Phpactor\Completion\Core\Util;

class OffsetHelper
{
    public static function lastNonWhitespaceOffset(string $source): int
    {
        return mb_strlen(preg_replace('/[ \t\x0d\n\r\f]+$/u', '', $source));
    }
}
