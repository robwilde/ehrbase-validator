<?php

namespace App\Services\Terminology;

class TerminologyImpl
{
    private $terminologyId;

    private $issuer;

    private $openEhrId;

    private $termsById = [];

    // for json creation
    public function __construct($issuer = null, $openEhrId = null, $terminologyId = null)
    {
        $this->issuer = $issuer;
        $this->openEhrId = $openEhrId;
        $this->terminologyId = $terminologyId;
    }

    public function getTerminologyId()
    {
        return $this->terminologyId;
    }

    public function getIssuer()
    {
        return $this->issuer;
    }

    public function getOpenEhrId()
    {
        return $this->openEhrId;
    }

    public function getTermsById(): array
    {
        return $this->termsById;
    }

    public function getTermCode($code, $language)
    {
        if (isset($this->termsById[$code])) {
            $multiLanguageTerm = $this->termsById[$code];

            return $multiLanguageTerm->getTermCodesByLanguage()[$language] ?? null;
        }

        return null;
    }

    public function getMultiLanguageTerm($code)
    {
        return $this->termsById[$code] ?? null;
    }

    public function getAllTermsForLanguage($language): array
    {
        $result = [];
        foreach ($this->termsById as $multiLanguageTerm) {
            $termCode = $multiLanguageTerm->getTermCodesByLanguage()[$language] ?? null;
            if ($termCode) {
                $result[] = $termCode;
            }
        }

        return $result;
    }

    public function getOrCreateTermSet($id)
    {
        if (! isset($this->termsById[$id])) {
            $multiLanguageTerm = new MultiLanguageTerm($this->terminologyId, $id);
            $this->termsById[$id] = $multiLanguageTerm;
        }

        return $this->termsById[$id];
    }
}
