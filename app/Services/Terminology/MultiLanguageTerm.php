<?php

namespace App\Services\Terminology;

class MultiLanguageTerm
{
    private $terminologyId;

    private $termId;

    private $termCodesByLanguage = [];

    // for json parsing only
    public function __construct($terminologyId = null, $termId = null)
    {
        $this->terminologyId = $terminologyId;
        $this->termId = $termId;
    }

    public function getTerminologyId()
    {
        return $this->terminologyId;
    }

    public function getTermId()
    {
        return $this->termId;
    }

    public function getTermCodesByLanguage(): array
    {
        return $this->termCodesByLanguage;
    }

    public function addCode($code): void
    {
        if (isset($this->termCodesByLanguage[$code->getLanguage()])) {
            // sometimes terms occur twice. They mean the same, but are in two groups.
            // TODO: properly implement groups
            $this->termCodesByLanguage[$code->getLanguage()]->getGroupIds()->add($code->getGroupName());
        } else {
            $this->termCodesByLanguage[$code->getLanguage()] = $code;
        }
    }
}
