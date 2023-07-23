<?php

namespace App\Services\Archie\RM;

abstract class RMObject
{
    public function __clone()
    {
        return unserialize(serialize($this));
    }
}
