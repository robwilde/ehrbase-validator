<?php

namespace App\Services\Archie\RM\DataValues;

interface SingleValuedDataValue
{
    public function getValue();

    public function setValue($type);
}
