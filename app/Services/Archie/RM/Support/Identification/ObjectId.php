<?php

namespace App\Services\Archie\RM\Support\Identification;

use App\Services\Archie\RM\RMObject;

abstract class ObjectId extends RMObject
{
    public function __construct(private ?array $value = null)
    {
        //
    }

    public function getValue(): ?array
    {
        return $this->value;
    }

    public function setValue(array $value): void
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return $this->value;
    }

    public function equals($o): bool
    {
        if ($this === $o) {
            return true;
        }
        if (is_null($o) || get_class($this) != get_class($o)) {
            return false;
        }

        $objectId = $o;

        return $this->value == $objectId->getValue();
    }

    public function hashCode(): string
    {
        return hash('md5', $this->value); // PHP doesn't have a built-in function equivalent to Java's Objects.hash, so we use MD5 hash instead
    }
}
