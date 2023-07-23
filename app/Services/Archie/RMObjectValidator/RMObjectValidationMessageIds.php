<?php

namespace App\Services\Archie\RMObjectValidator;

interface MessageCode
{
    public function getCode();

    public function getMessageTemplate();
}

class RMObjectValidationMessageIds implements MessageCode
{
    const RM_VALIDATION_MESSAGE_TO_STRING = 'Message at %s (%s):  %s';

    const rm_INCORRECT_TYPE = 'Object should be type {0}, but was {1}';

    const rm_TUPLE_CONSTRAINT = 'Multiple values for Tuple constraint {0}: {1}';

    const rm_TUPLE_MISMATCH = 'Object does not match tuple: {0}';

    const rm_PRIMITIVE_CONSTRAINT = 'Multiple values for Primitive Object constraint {0}: {1}';

    const rm_INVALID_FOR_CONSTRAINT = 'The value {0} must be {1}';

    const rm_INVALID_FOR_CONSTRAINT_MULTIPLE = 'The value {0} must be one of:';

    const rm_CARDINALITY_MISMATCH = 'Attribute does not match cardinality {0}';

    const rm_EXISTENCE_MISMATCH = 'Attribute {0} of class {1} does not match existence {2}';

    const rm_OCCURRENCE_MISMATCH = 'Attribute has {0} occurrences, but must be {1}';

    const rm_ARCHETYPE_ID_SLOT_MISMATCH = 'The archetype id {0} does not match the possible archetype ids.';

    const rm_SLOT_WITHOUT_ARCHETYPE_ID = 'An archetype slot was used in the archetype, but no archetype id was present in the data.';

    const rm_ARCHETYPE_NOT_FOUND = 'The Archetype with id {0} cannot be found';

    private $messageTemplate;

    public function __construct($messageTemplate)
    {
        $this->messageTemplate = $messageTemplate;
    }

    public function getCode(): string
    {
        $code = get_class($this);
        if (str_starts_with($code, 'rm_')) {
            return substr($code, 3);
        }

        return $code;
    }

    public function getMessageTemplate()
    {
        return $this->messageTemplate;
    }
}
