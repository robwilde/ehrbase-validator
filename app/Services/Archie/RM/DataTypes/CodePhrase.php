<?php

namespace App\Services\Archie\RM\DataTypes;

use App\Services\Archie\RM\RMObject;
use App\Services\Archie\RM\Support\Identification\TerminologyId;
use com\nedap\archie\rmutil\InvariantUtil;

class CodePhrase extends RMObject
{
    private TerminologyId $terminologyId;

    private string $codeString;

    private $preferredTerm;

    public function __construct(TerminologyId $terminologyId = null, $codeString = null, $preferredTerm = null)
    {
        if (is_string($terminologyId)) {
            $this->parsePhrase($terminologyId);
        } else {
            $this->terminologyId = $terminologyId;
            $this->codeString = $codeString;
            $this->preferredTerm = $preferredTerm;
        }
    }

    private function parsePhrase($phrase): void
    {
        $pattern = '/\\[?(?<terminologyId>.+)(\\((?<terminologyVersion>.+)\\))?::(?<codeString>[^\\]]+)\\]?/';
        preg_match($pattern, $phrase, $matches);
        if ($matches) {
            $this->terminologyId = new TerminologyId($matches['terminologyId'], $matches['terminologyVersion'] ?? null);
            $this->codeString = $matches['codeString'];
        } else {
            $this->terminologyId = new TerminologyId();
            $this->terminologyId->setValue('UNKNOWN');
            $this->codeString = $phrase;
        }
    }

    public function getTerminologyId()
    {
        return $this->terminologyId;
    }

    public function setTerminologyId(TerminologyId $terminologyId)
    {
        $this->terminologyId = $terminologyId;
    }

    public function getCodeString()
    {
        return $this->codeString;
    }

    public function setCodeString($codeString)
    {
        $this->codeString = $codeString;
    }

    public function getPreferredTerm()
    {
        return $this->preferredTerm;
    }

    public function setPreferredTerm($preferredTerm)
    {
        $this->preferredTerm = $preferredTerm;
    }

    public function __toString()
    {
        return $this->terminologyId.'::'.$this->codeString;
    }

    public function equals($other): bool
    {
        if ($this == $other) {
            return true;
        }
        if ($other == null || get_class($this) != get_class($other)) {
            return false;
        }

        return $this->terminologyId == $other->terminologyId &&
            $this->codeString == $other->codeString &&
            $this->preferredTerm == $other->preferredTerm;
    }

    public function hashCode()
    {
        // Implement your own hashing logic here
    }

    public function codeStringValid()
    {
        return InvariantUtil::nullOrNotEmpty($this->codeString);
    }
}
