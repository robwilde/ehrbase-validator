<?php

namespace App\Services\Terminology;

interface TermCode
{
    // get the id of the terminology of this code
    public function getTerminologyId();

    // get the language of the description of this code
    public function getLanguage();

    // get the code string of this code, the unique machine-readable code that is independent of the language
    public function getCodeString();

    // get the language specific description of this code
    public function getDescription();

    // Get the language specific OpenEHR Group name. Often null
    public function getGroupName();

    // get the id of the openEHR group, which is independent of the language as its English group name. often null
    public function getGroupIds();
}
