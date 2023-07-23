<?php

namespace App\Services\Archie\RM\Archetyped;

use com\nedap\archie\rm\support\identification\UIDBasedId;
use com\nedap\archie\rminfo\Invariant;
use com\nedap\archie\rminfo\RMPropertyIgnore;
use com\nedap\archie\rmutil\InvariantUtil;

/**
 * Class Locatable
 *
 * @XmlAccessorType(XmlAccessType.FIELD)
 *
 * @XmlType(name = "LOCATABLE", propOrder = {
 *     "name",
 *     "uid",
 *     "links",
 *     "archetypeDetails",
 *     "feederAudit"
 * })
 */
abstract class Locatable extends Pathable
{
    /**
     * @XmlElement
     *
     * @var DvText
     */
    private $name;

    /**
     * @XmlAttribute(name = "archetype_node_id")
     *
     * @var string
     */
    private $archetypeNodeId;

    /**
     * @var UIDBasedId|null
     */
    private $uid;

    /**
     * @XmlElement(name = "archetype_details")
     *
     * @var Archetyped|null
     */
    private $archetypeDetails;

    /**
     * @XmlElement(name = "feeder_audit")
     *
     * @var FeederAudit|null
     */
    private $feederAudit;

    /**
     * @var array
     */
    private $links = [];

    //    public function __construct()
    //    {
    //    }
    //
    //    public function __construct(string $archetypeNodeId, DvText $name)
    //    {
    //        $this->name = $name;
    //        $this->archetypeNodeId = $archetypeNodeId;
    //    }

    public function __construct(?UIDBasedId $uid, string $archetypeNodeId, DvText $name, ?Archetyped $archetypeDetails, ?FeederAudit $feederAudit, ?array $links, ?Pathable $parent, ?string $parentAttributeName)
    {
        parent::__construct($parent, $parentAttributeName);
        $this->name = $name;
        $this->archetypeNodeId = $archetypeNodeId;
        $this->uid = $uid;
        $this->archetypeDetails = $archetypeDetails;
        $this->feederAudit = $feederAudit;
        $this->links = $links;
    }

    public function getName(): DvText
    {
        return $this->name;
    }

    public function setName(DvText $name): void
    {
        $this->name = $name;
    }

    /**
     * convenience method
     */
    public function setNameAsString(string $name): void
    {
        $this->name = new DvText($name);
    }

    public function getArchetypeNodeId(): string
    {
        return $this->archetypeNodeId;
    }

    public function setArchetypeNodeId(string $archetypeNodeId): void
    {
        $this->archetypeNodeId = $archetypeNodeId;
    }

    public function getUid(): ?UIDBasedId
    {
        return $this->uid;
    }

    public function setUid(?UIDBasedId $uid): void
    {
        $this->uid = $uid;
    }

    public function getArchetypeDetails(): ?Archetyped
    {
        return $this->archetypeDetails;
    }

    public function setArchetypeDetails(?Archetyped $archetypeDetails): void
    {
        $this->archetypeDetails = $archetypeDetails;
    }

    public function getLinks(): array
    {
        return $this->links;
    }

    public function setLinks(array $links): void
    {
        $this->links = $links;
    }

    public function addLink(Link $link): void
    {
        $this->links[] = $link;
    }

    /**
     * @JsonIgnore
     *
     * @RMPropertyIgnore
     */
    public function getPathSegments(): array
    {
        $parent = $this->getParent();
        if ($parent === null) {
            return [];
        }

        $segments = $parent->getPathSegments();
        $segments[] = new PathSegment($this->getParentAttributeName(), $this->archetypeNodeId);

        return $segments;
    }

    /**
     * @JsonIgnore
     *
     * @RMPropertyIgnore
     *
     * @XmlTransient
     */
    public function getNameAsString(): ?string
    {
        return $this->name === null ? null : $this->name->getValue();
    }

    public function getFeederAudit(): ?FeederAudit
    {
        return $this->feederAudit;
    }

    public function setFeederAudit(?FeederAudit $feederAudit): void
    {
        $this->feederAudit = $feederAudit;
    }

    /**
     * @JsonIgnore
     *
     * @RMPropertyIgnore
     */
    public function isArchetypeRoot(): bool
    {
        return $this->archetypeDetails !== null;
    }

    public function equals($o): bool
    {
        if ($this === $o) {
            return true;
        }
        if ($o === null || get_class($this) !== get_class($o)) {
            return false;
        }

        $locatable = $o;

        return Objects.equals($this->name, $locatable->name) &&
            Objects.equals($this->archetypeNodeId, $locatable->archetypeNodeId) &&
            Objects.equals($this->uid, $locatable->uid) &&
            Objects.equals($this->archetypeDetails, $locatable->archetypeDetails) &&
            Objects.equals($this->feederAudit, $locatable->feederAudit) &&
            Objects.equals($this->links, $locatable->links);
    }

    public function hashCode(): int
    {
        return Objects::hash($this->name, $this->archetypeNodeId, $this->uid, $this->archetypeDetails, $this->feederAudit, $this->links);
    }

    /**
     * @Invariant(value="Links_valid", ignored = true)
     */
    public function linksValid(): bool
    {
        return InvariantUtil::nullOrNotEmpty($this->links);
    }

    /**
     * @Invariant(value="Archetyped_valid", ignored = true)
     */
    public function archetypedValid(): bool
    {
        return $this->isArchetypeRoot() ^ $this->archetypeDetails !== null; //this is not a data validation, again, and pretty much useless
    }

    /**
     * @Invariant("Archetype_node_id_valid")
     */
    public function archetypeNodeIdValid(): bool
    {
        return InvariantUtil::nullOrNotEmpty($this->archetypeNodeId);
    }
}
