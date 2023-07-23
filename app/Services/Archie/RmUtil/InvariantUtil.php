<?php

namespace App\Services\Archie\RmUtil;

class InvariantUtil
{
    const ENGLISH = 'en';

    public static function nullOrNotEmpty($collection): bool
    {
        if ($collection != null) {
            return ! empty($collection);
        }

        return true;
    }

    public static function nullOrNotEmptyString($string): bool
    {
        if ($string != null) {
            return ! empty($string);
        }

        return true;
    }

    public static function belongsToTerminologyByOpenEHRId($value, $openEHRId): bool
    {
        if ($value != null && $value->getCodeString() != null) {
            $termCode = OpenEHRTerminologyAccess::getInstance()->getTermByOpenEhrId($openEHRId, $value->getCodeString(), self::ENGLISH);

            return $termCode != null &&
                ($value->getTerminologyId() == null || strtolower($value->getTerminologyId()->getValue()) == strtolower($termCode->getTerminologyId()));
        }

        return true;
    }

    public static function belongsToTerminologyByGroupId($value, $groupId): bool
    {
        if ($value != null && $value->getCodeString() != null) {
            $termCode = OpenEHRTerminologyAccess::getInstance()->getTermByOpenEHRGroup($groupId, self::ENGLISH, $value->getCodeString());

            return $termCode != null &&
                ($value->getTerminologyId() == null || strtolower($value->getTerminologyId()->getValue()) == strtolower($termCode->getTerminologyId()));
        }

        return true;
    }

    public static function belongsToTerminologyByOpenEHRIdDvCodedText($value, $openEHRId): bool
    {
        if ($value != null) {
            return self::belongsToTerminologyByOpenEHRId($value->getDefiningCode(), $openEHRId);
        }

        return true;

    }

    public static function belongsToTerminologyByGroupIdDvCodedText($value, $groupId): bool
    {
        if ($value != null) {
            return self::belongsToTerminologyByGroupId($value->getDefiningCode(), $groupId);
        }

        return true;
    }

    public static function objectRefTypeEquals($refs, $type): bool
    {
        if ($refs == null) {
            return true;
        }
        foreach ($refs as $ref) {
            if (! self::objectRefTypeEqualsSingle($ref, $type)) {
                return false;
            }
        }

        return true;
    }

    public static function objectRefTypeEqualsSingle($ref, $type): bool
    {
        return $ref == null || $ref->getType() == null || $ref->getType() == $type;

    }
}
