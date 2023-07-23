<?php

namespace App\Services\Archie\RM\DataValues;

class DvURI extends DataValue implements SingleValuedDataValue
{
    protected string $value;

    public function __construct($value = null)
    {
        if ($value != null && is_string($value)) {
            $this->value = filter_var($value, FILTER_VALIDATE_URL) ? $value : null;
        } elseif ($value != null) {
            $this->value = $value;
        }
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value): void
    {
        if (is_string($value)) {
            $this->value = filter_var($value, FILTER_VALIDATE_URL) ? $value : null;
        } else {
            $this->value = $value;
        }
    }

    public function equals($object): bool
    {
        if ($this === $object) {
            return true;
        }
        if ($object === null || get_class($this) !== get_class($object)) {
            return false;
        }

        $dvURI = $object;

        return $this->value === $dvURI->getValue();
    }

    public function hashCode(): string
    {
        return hash('sha256', $this->value);
    }

    public function valueValid(): bool
    {
        return ! empty($this->value);
    }
}
