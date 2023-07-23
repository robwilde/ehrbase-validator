<?php

namespace App\Services\Terminology;

use Closure;
use com\nedap\archie\rm\datatypes\CodePhrase;

class TerminologyParam
{
    const PATTERN = "/(?<api>\/\/[^\/]*)\/(?<type>CodeSystem|ValueSet)(?:\/)?(?<op>\$expand|\$validate-code)?(?<param>\?.*)?/";

    private $serviceApi;

    private $operation;

    private $useValueSet = true;

    private $useCodeSystem = false;

    private $parameter;

    private $codePhrase = null;

    private function __construct($serviceApi = null)
    {
        $this->serviceApi = $serviceApi;
    }

    public static function ofFhir($url)
    {
        if (is_null($url)) {
            return new TerminologyParam();
        }

        if (! preg_match(self::PATTERN, $url, $matches)) {
            return new TerminologyParam();
        }

        $api = $matches['api'];
        $type = $matches['type'];
        $op = $matches['op'];
        $param = $matches['param'];

        if (is_null($api)) {
            throw new \RuntimeException('Missing service-api');
        }

        $tp = new TerminologyParam($api);
        if (strtolower($type) === 'codesystem') {
            $tp->useCodeSystem();
        }
        if (strtolower($type) === 'valueset') {
            $tp->useValueSet();
        }
        $tp->setOperation($op);
        $tp->setParameter($param !== null ? substr($param, 1) : null);

        return $tp;
    }

    public static function ofServiceApi($api): TerminologyParam
    {
        return new TerminologyParam($api);
    }

    public function useValueSet(): void
    {
        $this->useValueSet = true;
        $this->useCodeSystem = false;
    }

    public function useCodeSystem(): void
    {
        $this->useCodeSystem = true;
        $this->useValueSet = false;
    }

    public function getServiceApi()
    {
        return $this->serviceApi;
    }

    public function setOperation($op): void
    {
        $this->operation = $op;
    }

    public function getOperation()
    {
        return $this->operation;
    }

    public function getCodePhrase()
    {
        return $this->codePhrase;
    }

    public function setCodePhrase(CodePhrase $cp): void
    {
        $this->codePhrase = $cp;
    }

    public function renderCodePhrase(Closure $renderer)
    {
        return $renderer($this->codePhrase);
    }

    public function isUseValueSet()
    {
        return $this->useValueSet;
    }

    public function isUseCodeSystem()
    {
        return $this->useCodeSystem;
    }

    public function setParameter($param)
    {
        $this->parameter = $param;
    }

    public function getParameter()
    {
        return $this->parameter;
    }

    public function extractFromParameter(Closure $extractor)
    {
        return $extractor($this->parameter);
    }

    public function equals($o)
    {
        if ($this === $o) {
            return true;
        }
        if ($o === null || get_class($this) != get_class($o)) {
            return false;
        }

        $that = $o;

        return $this->serviceApi === $that->serviceApi &&
            $this->operation === $that->operation &&
            $this->useValueSet === $that->useValueSet &&
            $this->useCodeSystem === $that->useCodeSystem &&
            $this->parameter === $that->parameter &&
            (($this->codePhrase == null && $that->codePhrase == null) ||
                ($this->codePhrase != null && $that->codePhrase != null && $this->codePhrase->equals($that->codePhrase)));
    }

    public function hashCode()
    {
        // simple hash code implementation
        return spl_object_hash($this);
    }
}
