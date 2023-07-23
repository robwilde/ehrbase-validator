<?php

namespace App\Services\Archie\Terminology;

interface TerminologyAccess
{
    /** Get a single term by its external Terminology Id, for example "ISO_639-1", "nl" for the given language*/
    public function getTerm($terminologyId, $code, $language);

    /** Get all terms by its external Terminology Id, for example ISO_639-1*/
    public function getTerms($terminologyId, $language);

    /** Get all terms by its terminology URI, for example http://openehr.org/id/124, for the given language */
    public function getTermByTerminologyURI($uri, $language);

    /** Get all terms by its openEHR id, for example "countries", "GB" for the given language */
    public function getTermByOpenEhrId($terminologyId, $code, $language);

    /** Get all terms by its openEHR id, for example "countries", for the given language */
    public function getTermsByOpenEhrId($terminologyId, $language);

    /** Get a list of all term codes for a given OpenEHR group, given the ENGLISH group id (NO translated group names, but you will get them in the result!) */
    public function getTermsByOpenEHRGroup($groupId, $language);

    /** Get a list a term for a given value in a OpenEHR group, given the ENGLISH group id (NO translated group names, but you will get them in the result!) */
    public function getTermByOpenEHRGroup($groupId, $language, $code);
}
