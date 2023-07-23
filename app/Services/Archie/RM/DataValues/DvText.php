<?php

namespace App\Services\Archie\RM\DataValues;

use App\Services\Archie\RM\DataTypes\CodePhrase;

class DvText extends DataValue
{
    protected string $value;

    private $hyperlink;

    private $formatting;

    private $mappings;

    private $language;

    private $encoding;

    public function __construct(
        string $value = null,
        CodePhrase $language = null,
        CodePhrase $encoding = null
    ) {
        $this->value = $value;
        $this->language = $language;
        $this->encoding = $encoding;
        $this->mappings = [];
    }

    public function getMappings(): array
    {
        return $this->mappings;
    }

    public function setMappings(array $mappings): void
    {
        $this->mappings = $mappings;
    }

    public function addMapping(TermMapping $mapping): void
    {
        $this->mappings[] = $mapping;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function getHyperlink(): DvURI
    {
        return $this->hyperlink;
    }

    public function setHyperlink(DvURI $hyperlink): void
    {
        $this->hyperlink = $hyperlink;
    }

    public function getFormatting(): ?string
    {
        return $this->formatting;
    }

    public function setFormatting(?string $formatting): void
    {
        $this->formatting = $formatting;
    }

    public function getLanguage(): CodePhrase
    {
        return $this->language;
    }

    public function setLanguage(CodePhrase $language): void
    {
        $this->language = $language;
    }

    public function getEncoding(): CodePhrase
    {
        return $this->encoding;
    }

    public function setEncoding(CodePhrase $encoding): void
    {
        $this->encoding = $encoding;
    }

    public function equals($o): bool
    {
        if ($this === $o) {
            return true;
        }
        if (! ($o instanceof self)) {
            return false;
        }

        return
            $this->value === $o->getValue() &&
            $this->hyperlink == $o->getHyperlink() &&
            $this->formatting === $o->getFormatting() &&
            $this->mappings == $o->getMappings() &&
            $this->language === $o->getLanguage() &&
            $this->encoding === $o->getEncoding();
    }

    public function hashCode(): int
    {
        return crc32(
            spl_object_hash((object) $this->value).
            spl_object_hash($this->hyperlink).
            spl_object_hash($this->formatting).
            spl_object_hash((object) $this->mappings).
            spl_object_hash($this->language).
            spl_object_hash($this->encoding)
        );
    }
}
