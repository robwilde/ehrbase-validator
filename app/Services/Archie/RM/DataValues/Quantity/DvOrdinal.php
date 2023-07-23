<?php

namespace App\Services\Archie\RM\DataValues\Quantity;

use App\Services\Archie\RM\DataValues\SingleValuedDataValue;

class DvOrdinal extends DvOrdered implements SingleValuedDataValue, \Comparable
{
    private $symbol;

    private $value;

    public function __construct($value = null, $symbol = null)
    {
        $this->symbol = $symbol;
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function compareTo($o)
    {
        return $this->value <=> $o->value;
    }

    public function getSymbol()
    {
        return $this->symbol;
    }

    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;
    }

    public function equals($o)
    {
        if ($this == $o) {
            return true;
        }
        if ($o == null || get_class($this) != get_class($o)) {
            return false;
        }
        if (! parent::equals($o)) {
            return false;
        }
        $dvOrdinal = $o;

        return $this->symbol == $dvOrdinal->symbol &&
            $this->value == $dvOrdinal->value;
    }

    public function hashCode()
    {
        return hash('ripemd160', parent::hashCode().$this->symbol.$this->value);
    }
}
