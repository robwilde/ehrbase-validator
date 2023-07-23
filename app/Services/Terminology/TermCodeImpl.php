<?php

namespace App\Services\Terminology;

class TermCodeImpl implements TermCode
{
    private $terminologyId;

    private $language;

    private $codeString;

    private $description;

    private $groupName;

    private array $groupIds = []; //TODO: still one group name. Change to multiple as well?

    public function __construct($terminologyId, $language, $codeString, $description, $groupName = null, $groupId = null)
    {
        $this->terminologyId = $terminologyId;
        $this->language = $language;
        $this->description = $description === null ? $codeString : $description;
        $this->codeString = $codeString;
        $this->groupName = $groupName;
        if ($groupId !== null) {
            $this->groupIds[] = $groupId;
        }
    }

    protected function addGroupId($groupId): void
    {
        $this->groupIds[] = $groupId;
    }

    public function getTerminologyId()
    {
        return $this->terminologyId;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getCodeString()
    {
        return $this->codeString;
    }

    public function getGroupName()
    {
        return $this->groupName;
    }

    public function getGroupIds(): array
    {
        return $this->groupIds;
    }
}
