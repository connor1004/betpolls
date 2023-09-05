<?php

namespace App\Helpers;

class FormatHelper
{
    public function formatSignedWeight($value)
    {
        if ($value == ceil($value)) {
            return sprintf("%+d", $value);
        } else {
            return sprintf("%+.1f", $value);
        }
    }
}
