<?php

namespace App\Services\Archie\Paths;

use App\Services\Google\Base\Joiner;

/**
 * Segment of an apathy-query
 * Created by pieter.bos on 19/10/15.
 */
class PathSegment
{
    private const archetypeRefPattern = '/(.*::)?.*-.*-.*\\..*\\.v.*/';

    private const nodeIdPattern = '/id(\\.?\\d)+|at(\\.?\\d)+/';

    private $archetypeRef = null;

    //    public function __construct()
    //    {
    //    }
    //
    //    public function __construct($nodeName, $index)
    //    {
    //        $this->__construct($nodeName, null, $index);
    //    }
    //
    //    public function __construct($nodeName, $nodeId)
    //    {
    //        $this->__construct($nodeName, $nodeId, null);
    //    }
    //
    //    public function __construct($nodeName)
    //    {
    //        $this->__construct($nodeName, null, null);
    //    }

    public function __construct(private ?string $nodeName, private ?int $nodeId, private ?int $index)
    {
        //
    }

    public function getNodeName(): ?string
    {
        return $this->nodeName;
    }

    public function setNodeName($nodeName): void
    {
        $this->nodeName = $nodeName;
    }

    public function getNodeId(): ?int
    {
        return $this->nodeId;
    }

    public function setNodeId($nodeId): void
    {
        $this->nodeId = $nodeId;
    }

    public function getIndex(): ?int
    {
        return $this->index;
    }

    public function setIndex($index): void
    {
        $this->index = $index;
    }

    public function getArchetypeRef()
    {
        return $this->archetypeRef;
    }

    public function setArchetypeRef($archetypeRef): void
    {
        $this->archetypeRef = $archetypeRef;
    }

    public function hasIdCode(): bool
    {
        return $this->nodeId != null && preg_match(self::nodeIdPattern, $this->nodeId);
    }

    public function hasNumberIndex(): bool
    {
        return $this->index != null;
    }

    public function hasArchetypeRef(): bool
    {
        return $this->nodeId != null && preg_match(self::archetypeRefPattern, $this->nodeId);
    }

    public function __toString()
    {
        if ($this->hasExpressions()) {
            $joinedArray = Joiner::on(',')->skipNulls()->join([$this->nodeId, $this->index]);

            return '/'.$this->nodeName."[{$joinedArray}]";
        } else {
            return '/'.$this->nodeName;
        }
    }

    public function hasExpressions(): bool
    {
        return $this->nodeId != null || $this->index != null;
    }
}
