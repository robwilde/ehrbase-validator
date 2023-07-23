<?php

namespace App\Services\Archie\RM\DataValues\Quantity;

//PHP doesn't support generics like Java, so the translation won't be exact. However, here's a rough equivalent:

use App\Services\Archie\RM\DataValues\DataValue;
use com\nedap\archie\rmutil\InvariantUtil;

class DvOrdered extends DataValue
{
    private $normalStatus;

    private $normalRange;

    private $otherReferenceRanges = [];

    public function __construct($otherReferenceRanges = null, $normalRange = null)
    {
        $this->normalRange = $normalRange;
        $this->otherReferenceRanges = $otherReferenceRanges;
    }

    public function getNormalRange()
    {
        return $this->normalRange;
    }

    public function setNormalRange($normalRange): void
    {
        $this->normalRange = $normalRange;
    }

    public function getOtherReferenceRanges()
    {
        return $this->otherReferenceRanges;
    }

    public function setOtherReferenceRanges($otherReferenceRanges): void
    {
        $this->otherReferenceRanges = $otherReferenceRanges;
    }

    public function addOtherReferenceRange($range): void
    {
        $this->otherReferenceRanges[] = $range;
    }

    public function getNormalStatus()
    {
        return $this->normalStatus;
    }

    public function setNormalStatus($normalStatus)
    {
        $this->normalStatus = $normalStatus;
    }

    public function equals($o): bool
    {
        if ($this == $o) {
            return true;
        }
        if ($o == null || get_class($this) != get_class($o)) {
            return false;
        }
        $dvOrdered = $o;

        return $this->normalStatus == $dvOrdered->normalStatus &&
            $this->normalRange == $dvOrdered->normalRange &&
            $this->otherReferenceRanges == $dvOrdered->otherReferenceRanges;
    }

    public function isSimple(): bool
    {
        return $this->normalRange == null && (empty($this->otherReferenceRanges));
    }

    public function hashCode()
    {
        return hash('sha256', $this->normalStatus.$this->normalRange.implode($this->otherReferenceRanges));
    }

    public function otherReferenceRangesValid()
    {
        return InvariantUtil::nullOrNotEmpty($this->otherReferenceRanges);
    }

    public function simpleValiditiy()
    {
        return true;
    }

    public function normalStatusValidity()
    {
        return InvariantUtil::belongsToTerminologyByOpenEHRId($this->normalStatus, 'normal statuses');
    }

    public function normalRangeAndStatusConsistency()
    {
        if ($this->normalStatus != null && $this->normalRange != null) {
            return $this->normalStatus->getCodeString() == 'N' ^ ! $this->normalRange->has($this);
        }

        return true;
    }
}
// Please note that PHP doesn't have a built-in equivalent for Java's `Objects.equals()` and `Objects.hash()`, so I've used simple comparison and a hash function instead. Also, PHP doesn't have a built-in equivalent for Java's `@Nullable` and `@Invariant` annotations.
