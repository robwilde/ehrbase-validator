<?php

namespace App\Services\Archie\RM\Archetyped;

use App\Services\Archie\Paths\PathSegment;
use App\Services\Archie\Paths\PathUtil;
use App\Services\Archie\Query\RMPathQuery;
use App\Services\Archie\RM\RMObject;
//use com\nedap\archie\query\RMObjectWithPath;
use com\nedap\archie\rminfo\ArchieRMInfoLookup;
use com\nedap\archie\rminfo\PropertyType;
use com\nedap\archie\rminfo\RMProperty;
use com\nedap\archie\rminfo\RMPropertyIgnore;

class Pathable extends RMObject
{
    //TODO: implement according to spec: pathExists(path), pathUnique(path), pathOfItem(pathable)

    /**
     * @var Pathable|null
     *
     * @JsonIgnore
     *
     * @XmlTransient
     *
     * @RMProperty(value="parent", computed=PropertyType.COMPUTED)
     */
    private $parent;

    /**
     * @var string|null
     *
     * @JsonIgnore
     *
     * @XmlTransient
     */
    private $parentAttributeName;

    public function __construct(Pathable $parent = null, string $parentAttributeName = null)
    {
        $this->parent = $parent;
        $this->parentAttributeName = $parentAttributeName;
    }

    public function itemAtPath(string $s)
    {
        return (new RMPathQuery($s))->find(ArchieRMInfoLookup::getInstance(), $this);
    }

    public function itemAtPathMatchSpecialisedNodes(string $s)
    {
        return (new RMPathQuery($s, true))->find(ArchieRMInfoLookup::getInstance(), $this);
    }

    public function itemsAtPath(string $s): array
    {
        $objects = (new RMPathQuery($s))->findList(ArchieRMInfoLookup::getInstance(), $this);
        $result = [];
        foreach ($objects as $object) {
            $result[] = $object->getObject();
        }

        return $result;
    }

    public function itemsAtPathMatchSpecialisedNodes(string $s): array
    {
        $objects = (new RMPathQuery($s, true))->findList(ArchieRMInfoLookup::getInstance(), $this);
        $result = [];
        foreach ($objects as $object) {
            $result[] = $object->getObject();
        }

        return $result;
    }

    /**
     * @JsonIgnore
     */
    public function getParent(): ?Pathable
    {
        return $this->parent;
    }

    private function setParent(?Pathable $parent): void
    {
        $this->parent = $parent;
    }

    private function setParentAttributeName(?string $parentAttributeName): void
    {
        $this->parentAttributeName = $parentAttributeName;
    }

    /**
     * Utility method to set this object as the parent of the given child,
     * if the child is not null
     */
    protected function setThisAsParent(?Pathable $child, ?string $attributeName): void
    {
        if ($child !== null) {
            $child->setParent($this);
            $child->setParentAttributeName($attributeName);
        }
    }

    /**
     * Utility method to set this object as the parent of the given child,
     * if the child is not null
     */
    protected function setThisAsParentForCollection(?array $children, ?string $attributeName): void
    {
        if ($children !== null) {
            foreach ($children as $child) {
                $this->setThisAsParent($child, $attributeName);
            }
        }
    }

    protected function getParentAttributeName(): ?string
    {
        return $this->parentAttributeName;
    }

    /**
     * @return PathSegment[]
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
        $segments[] = new PathSegment($this->getParentAttributeName(), null, null);

        return $segments;
    }

    /**
     * Path from the toplevel-RM object. Not sure if this should be here, because the EHR and Folder objects are also in
     * the RM. But for now, it works because the most toplevel element is a Composition
     *
     * @RMPropertyIgnore
     */
    final public function getPath(): string
    {
        return PathUtil::getPath($this->getPathSegments());
    }
}
