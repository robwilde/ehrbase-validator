<?php

namespace App\Services\Archie\RMObjectValidator;

class RMObjectValidationMessage
{
    private $archetypePath;

    private $path;

    private $humanReadableArchetypePath;

    private $message;

    private $archetypeId;

    private $type;

    public function __construct($constraint = null, $actualPath = null, $message = null, $type = null)
    {
        // If parameters are not null, this is the equivalent of RMObjectValidationMessage(ArchetypeConstraint, String, String, RMObjectValidationMessageType) constructor
        if (isset($constraint, $actualPath, $message)) {
            $this->path = $actualPath;
            $this->archetypeId = $this->getArchetypeId($constraint);
            $this->archetypePath = isset($constraint) ? $constraint->getPath() : null;
            $this->humanReadableArchetypePath = isset($constraint) ? $constraint->getLogicalPath() : null;
            $this->message = $message;
            $this->type = isset($type) ? $type : RMObjectValidationMessageType::DEFAULT;
        }
    }

    // This method is an equivalent of RMObjectValidationMessage(String, String, String, String, String, RMObjectValidationMessageType) constructor
    public function setAttributes($path, $archetypeId, $archetypePath, $humanPath, $message, $type): void
    {
        $this->path = $path;
        $this->archetypeId = $archetypeId;
        $this->archetypePath = $archetypePath;
        $this->humanReadableArchetypePath = $humanPath;
        $this->message = $message;
        $this->type = $type;
    }

    public function setExceptionAttributes($e): void
    {
        $this->path = $e->getPath();
        $this->humanReadableArchetypePath = $e->getHumanPath();
        $this->message = $e->getMessage();
    }

    public function archetypeId()
    {
        return $this->archetypeId;
    }

    public function getArchetypePath()
    {
        return $this->archetypePath;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type): void
    {
        $this->type = $type;
    }

    public function getHumanReadableArchetypePath()
    {
        return $this->humanReadableArchetypePath;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function __toString()
    {
        $pathToUse = is_null($this->humanReadableArchetypePath) ? $this->path : $this->humanReadableArchetypePath;

        return sprintf(RMObjectValidationMessageIds::RM_VALIDATION_MESSAGE_TO_STRING, $pathToUse, $this->path, $this->message);
    }

    private function getArchetypeId($constraint)
    {
        if (! isset($constraint)) {
            return null;
        }
        $archetype = $constraint->getArchetype();

        return $archetype?->archetypeId()->getFullId();
    }

    // Please note: in PHP we don't have the equals() method equivalent, it's recommended to use comparison operators
    // Also hashCode() method is not available in PHP
}
