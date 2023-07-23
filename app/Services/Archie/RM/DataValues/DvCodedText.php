<?php

namespace App\Services\Archie\RM\DataValues;

use App\Services\Archie\RM\DataTypes\CodePhrase;

class DvCodedText extends DvText
{
    private $definingCode;

    public function __construct(
        string $value = null,
        CodePhrase $definingCode = null,
        CodePhrase $language = null,
        CodePhrase $encoding = null
    ) {
        parent::__construct($value, $language, $encoding);
        $this->definingCode = $definingCode instanceof CodePhrase ? $definingCode : new CodePhrase($definingCode);
    }

    public function getDefiningCode(): CodePhrase
    {
        return $this->definingCode;
    }

    public function setDefiningCode(CodePhrase $definingCode): void
    {
        $this->definingCode = $definingCode;
    }

    public function equals($o): bool
    {
        if ($this === $o) {
            return true;
        }
        if (! ($o instanceof self)) {
            return false;
        }
        if (! parent::equals($o)) {
            return false;
        }

        return $this->definingCode == $o->getDefiningCode();
    }

    public function hashCode(): int
    {
        return crc32(parent::hashCode().spl_object_hash($this->definingCode));
    }

    public function __toString(): string
    {
        return sprintf('DvCodedText{defining_code=%s, value=%s}', $this->definingCode, $this->getValue());
    }
}
