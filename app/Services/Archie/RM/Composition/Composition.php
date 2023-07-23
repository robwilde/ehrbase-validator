<?php

namespace App\Services\Archie\RM\Composition;

use com\nedap\archie\rm\datatypes\CodePhrase;
use com\nedap\archie\rm\datavalues\DvCodedText;
use com\nedap\archie\rm\datavalues\DvText;
use com\nedap\archie\rm\generic\PartyProxy;
use com\nedap\archie\rminfo\Invariant;
use com\nedap\archie\rmutil\InvariantUtil;

class Composition extends Locatable
{
    private $language;

    private $territory;

    private $category;

    private $composer;

    private $context;

    private $content;

    public function __construct($archetypeNodeId, DvText $name, $content, CodePhrase $language, $context, PartyProxy $composer, DvCodedText $category, CodePhrase $territory)
    {
        parent::__construct($archetypeNodeId, $name);
        $this->language = $language;
        $this->territory = $territory;
        $this->category = $category;
        $this->composer = $composer;
        $this->context = $context;
        $this->content = $content;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public function setLanguage(CodePhrase $language)
    {
        $this->language = $language;
    }

    public function setLanguageFromString($codePhrase)
    {
        $this->language = new CodePhrase($codePhrase);
    }

    public function getTerritory()
    {
        return $this->territory;
    }

    public function setTerritory(CodePhrase $territory)
    {
        $this->territory = $territory;
    }

    public function setTerritoryFromString($codePhrase)
    {
        $this->territory = new CodePhrase($codePhrase);
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory(DvCodedText $category)
    {
        $this->category = $category;
    }

    public function setCategoryFromString($value, $codePhrase)
    {
        $this->category = new DvCodedText($value, $codePhrase);
    }

    public function getComposer()
    {
        return $this->composer;
    }

    public function setComposer(PartyProxy $composer)
    {
        $this->composer = $composer;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function setContext($context)
    {
        $this->context = $context;
        $this->setThisAsParent($context, 'context');
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
        $this->setThisAsParent($content, 'content');
    }

    public function addContent(ContentItem $item)
    {
        $this->content[] = $item;
        $this->setThisAsParent($item, 'content');
    }

    public function equals($o)
    {
        if ($this === $o) {
            return true;
        }
        if ($o === null || get_class($this) !== get_class($o)) {
            return false;
        }
        if (! parent::equals($o)) {
            return false;
        }
        $that = $o;

        return $this->language == $that->language &&
            $this->territory == $that->territory &&
            $this->category == $that->category &&
            $this->composer == $that->composer &&
            $this->context == $that->context &&
            $this->content == $that->content;
    }

    public function hashCode()
    {
        return Objects::hash(parent::hashCode(), $this->language, $this->territory, $this->category, $this->composer, $this->context, $this->content);
    }

    /**
     * @Invariant("Category_validity")
     */
    public function categoryValid()
    {
        return InvariantUtil::belongsToTerminologyByGroupId($this->category, 'composition category');
    }

    /**
     * @Invariant("Territory_valid")
     */
    public function territoryValid()
    {
        return InvariantUtil::belongsToTerminologyByOpenEHRId($this->territory, 'countries');
    }

    /**
     * @Invariant("Language_valid")
     */
    public function languageValid()
    {
        return InvariantUtil::belongsToTerminologyByOpenEHRId($this->language, 'languages');
    }

    /**
     * @Invariant(value="Content valid", ignored=true)
     */
    public function contentValid()
    {
        return InvariantUtil::nullOrNotEmpty($this->content);
    }

    /**
     * @Invariant("Is_archetype_root")
     */
    public function archetypeRoot()
    {
        return $this->isArchetypeRoot();
    }
}
