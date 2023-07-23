<?php

namespace App\Services\Archie\Query;

use App\Services\Archie\RMInfo\ModelInfoLookup;
use com\nedap\archie\aom\utils\AOMUtils;
use com\nedap\archie\definitions\AdlCodeDefinitions;
use com\nedap\archie\paths\PathSegment;

class RMPathQuery
{
    private $pathSegments = [];

    private $matchSpecialisedNodes;

    public function __construct($query, $matchSpecialisedNodes = false)
    {
        $this->pathSegments = (new APathQuery($query))->getPathSegments();
        $this->matchSpecialisedNodes = $matchSpecialisedNodes;
    }

    public function find(ModelInfoLookup $lookup, $root)
    {
        $currentObject = $root;
        try {
            foreach ($this->pathSegments as $segment) {
                if ($currentObject === null) {
                    return null;
                }
                $attributeInfo = $lookup->getAttributeInfo(get_class($currentObject), $segment->getNodeName());
                if ($attributeInfo === null) {
                    return null;
                }
                $method = $attributeInfo->getGetMethod();
                $currentObject = $method->invoke($currentObject);
                if ($currentObject === null) {
                    return null;
                }

                $archetypeNodeIdFromObject = $lookup->getArchetypeNodeIdFromRMObject($currentObject);
                if ($currentObject instanceof Collection) {
                    $collection = $currentObject;
                    if (! $segment->hasExpressions()) {
                        $currentObject = $collection;
                    } else {
                        $currentObject = $this->findRMObject($lookup, $segment, $collection);
                    }
                } elseif ($archetypeNodeIdFromObject !== null) {
                    if ($segment->hasExpressions()) {
                        if ($segment->hasIdCode()) {
                            if ($archetypeNodeIdFromObject !== $segment->getNodeId()) {
                                return null;
                            }
                        } elseif ($segment->hasNumberIndex()) {
                            $number = $segment->getIndex();
                            if ($number !== 1) {
                                return null;
                            }
                        } elseif ($segment->hasArchetypeRef()) {
                            if ($archetypeNodeIdFromObject !== $segment->getNodeId()) {
                                throw new \InvalidArgumentException('cannot handle RM-queries with node names or archetype references yet');
                            }
                        }
                    }
                } elseif ($segment->hasNumberIndex()) {
                    $number = $segment->getIndex();
                    if ($number !== 1) {
                        return null;
                    }
                }
            }

            return $currentObject;
        } catch (\ReflectionException $e) {
            throw new \RuntimeException($e);
        } catch (\Exception $e) {
            throw new \RuntimeException($e);
        }
    }

    public function findList(ModelInfoLookup $lookup, $root)
    {
        $currentObjects = [new RMObjectWithPath($root, '/')];
        try {
            foreach ($this->pathSegments as $segment) {
                if (empty($currentObjects)) {
                    return [];
                }
                $newCurrentObjects = [];

                foreach ($currentObjects as $currentObject) {
                    $currentRMObject = $currentObject->getObject();
                    $attributeInfo = $lookup->getAttributeInfo(get_class($currentRMObject), $segment->getNodeName());
                    if ($attributeInfo === null) {
                        continue;
                    }
                    $method = $attributeInfo->getGetMethod();
                    $currentRMObject = $method->invoke($currentRMObject);
                    $pathSeparator = '/';
                    if (substr($currentObject->getPath(), -1) === '/') {
                        $pathSeparator = '';
                    }
                    $newPath = $currentObject->getPath().$pathSeparator.$segment->getNodeName();

                    if ($currentRMObject === null) {
                        continue;
                    }
                    $archetypeNodeIdFromObject = $lookup->getArchetypeNodeIdFromRMObject($currentObject);
                    if ($currentRMObject instanceof Collection) {
                        $collection = $currentRMObject;
                        if (! $segment->hasExpressions()) {
                            $this->addAllFromCollection($lookup, $newCurrentObjects, $collection, $newPath);
                        } else {
                            $newCurrentObjects = array_merge($newCurrentObjects, $this->findRMObjectsWithPathCollection($lookup, $segment, $collection, $newPath));
                        }
                    } elseif ($archetypeNodeIdFromObject !== null) {
                        if ($segment->hasExpressions()) {
                            if ($segment->hasIdCode()) {
                                if ($archetypeNodeIdFromObject !== $segment->getNodeId()) {
                                    continue;
                                }
                            } elseif ($segment->hasNumberIndex()) {
                                $number = $segment->getIndex();
                                if ($number !== 1) {
                                    continue;
                                }
                            } elseif ($segment->hasArchetypeRef()) {
                                if ($archetypeNodeIdFromObject !== $segment->getNodeId()) {
                                    continue;
                                }
                            }
                            $newCurrentObjects[] = $this->createRMObjectWithPath($lookup, $currentRMObject, $newPath);
                        }
                    } elseif ($segment->hasNumberIndex()) {
                        $number = $segment->getIndex();
                        if ($number !== 1) {
                            continue;
                        }
                    } else {
                        $newCurrentObjects[] = $this->createRMObjectWithPath($lookup, $currentRMObject, $newPath);
                    }
                }
                $currentObjects = $newCurrentObjects;
            }

            return $currentObjects;
        } catch (\ReflectionException|\Exception $e) {
            throw new \RuntimeException($e);
        }
    }

    private function createRMObjectWithPath(ModelInfoLookup $lookup, $currentObject, $newPath)
    {
        $archetypeNodeId = $lookup->getArchetypeNodeIdFromRMObject($currentObject);
        $pathConstraint = $this->buildPathConstraint(null, $archetypeNodeId);

        return new RMObjectWithPath($currentObject, $newPath.$pathConstraint);
    }

    private function addAllFromCollection(ModelInfoLookup $lookup, &$newCurrentObjects, $toAdd, $basePath)
    {
        $index = 1;
        foreach ($toAdd as $object) {
            $constraint = $this->buildPathConstraint($index, $lookup->getArchetypeNodeIdFromRMObject($object));
            $newCurrentObjects[] = new RMObjectWithPath($object, $basePath.$constraint);
            $index++;
        }
    }

    private function buildPathConstraint($index, $archetypeNodeId)
    {
        if ($index === null && ! $this->archetypeNodeIdPresent($archetypeNodeId)) {
            return '';
        }
        if ($this->archetypeNodeIdPresent($archetypeNodeId) && $index === null) {
            return '['.$archetypeNodeId.']';
        }
        $constraint = '[';
        $first = true;
        if ($this->archetypeNodeIdPresent($archetypeNodeId)) {
            $constraint .= $archetypeNodeId;
            $first = false;
        }
        if ($index !== null) {
            if (! $first) {
                $constraint .= ', ';
            }
            $constraint .= (string) $index;
        }

        $constraint .= ']';

        return $constraint;
    }

    private function archetypeNodeIdPresent($archetypeNodeId)
    {
        return $archetypeNodeId !== null && $archetypeNodeId !== AdlCodeDefinitions::PRIMITIVE_NODE_ID;
    }

    private function findRMObjectsWithPathCollection(ModelInfoLookup $lookup, PathSegment $segment, $collection, $path)
    {
        if ($segment->hasNumberIndex()) {
            $number = $segment->getIndex();
            $i = 1;
            foreach ($collection as $object) {
                if ($number === $i) {
                    return [new RMObjectWithPath($object, $path.$this->buildPathConstraint($i, $lookup->getArchetypeNodeIdFromRMObject($object)))];
                }
                $i++;
            }
        }
        $result = [];
        $i = 1;
        foreach ($collection as $object) {
            $archetypeNodeId = $lookup->getArchetypeNodeIdFromRMObject($object);

            if ($segment->hasIdCode()) {
                if ($this->matchSpecialisedNodes) {
                    if (AOMUtils::codesConformant($archetypeNodeId, $segment->getNodeId())) {
                        $result[] = new RMObjectWithPath($object, $path.$this->buildPathConstraint($i, $archetypeNodeId));
                    }
                } else {
                    if ($segment->getNodeId() === $archetypeNodeId) {
                        $result[] = new RMObjectWithPath($object, $path.$this->buildPathConstraint($i, $archetypeNodeId));
                    }
                }
            } elseif ($segment->hasArchetypeRef()) {
                if ($segment->getNodeId() === $archetypeNodeId) {
                    $result[] = new RMObjectWithPath($object, $path.$this->buildPathConstraint($i, $archetypeNodeId));
                }
            } else {
                if ($this->equalsName($lookup->getNameFromRMObject($object), $segment->getNodeId())) {
                    $result[] = new RMObjectWithPath($object, $path.$this->buildPathConstraint($i, $archetypeNodeId));
                }
            }
            $i++;
        }

        return $result;
    }

    private function equalsName($name, $nameFromQuery)
    {
        if ($name === null) {
            return false;
        }
        $name = preg_replace("/( |\t|\n|\r)+/", '', $name);
        $nameFromQuery = preg_replace("/( |\t|\n|\r)+/", '', $nameFromQuery);

        return strcasecmp($name, $nameFromQuery) === 0;
    }

    public function getPathSegments()
    {
        return $this->pathSegments;
    }
}
