<?php

namespace App\Services\Terminology;

interface ExternalTerminologyValidation
{
    public function supports(TerminologyParam $param);

    // Replace this with appropriate error handling mechanism as per your use case
    public function validate(TerminologyParam $param): bool;

    public function expand(TerminologyParam $param): array;
}
