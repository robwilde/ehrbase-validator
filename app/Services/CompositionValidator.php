<?php

namespace App\Services;

use App\Services\Archie\RM\Composition\Composition;
use App\Services\Terminology\ExternalTerminologyValidation;
use Ehrbase\OpenEhr\Sdk\Validation\WebTemplate\ValidationWalker;
use Ehrbase\OpenEhr\Sdk\WebTemplate\Model\WebTemplate;
use Ehrbase\OpenEhr\Sdk\WebTemplate\Parser\OPTParser;
use Nedap\Archie\RMObjectValidator\RMObjectValidationMessage;
use Nedap\Archie\RMObjectValidator\RMObjectValidator;
use OpenEhr\Schemas\V1\OPERATIONALTEMPLATE;

/**
 * Validator that checks a composition against constraints define in an Operational Template or a
 * Web Template.
 * This class is NOT thread-safe!
 */
class CompositionValidator
{
    private RMObjectValidator $rmObjectValidator;

    private ?ExternalTerminologyValidation $externalTerminologyValidation;

    public function __construct(ExternalTerminologyValidation $externalTerminologyValidation = null)
    {
        $this->rmObjectValidator = new RMObjectValidator(new ArchieRMInfoLookup(), function ($archetypeId) {
            return null;
        });
        $this->externalTerminologyValidation = $externalTerminologyValidation;
    }

    /**
     * Validates the composition using an Operational Template.
     *
     * @param  Composition  $composition the composition to validate
     * @param  OPERATIONALTEMPLATE  $template the operational template used to validate the composition
     * @return array the list of constraint violations
     */
    public function withOperationalTemplate(Composition $composition, OPERATIONALTEMPLATE $template)
    {
        return $this->validate($composition, (new OPTParser($template))->parse());
    }

    /**
     * Validates the composition using a Web Template.
     *
     * @param  Composition  $composition the composition to validate
     * @param  WebTemplate  $template the web template used to validate the composition
     * @return array the list of constraint violations
     */
    public function byWebTemplate(Composition $composition, WebTemplate $template)
    {
        $messages = $this->rmObjectValidator->validate($composition);
        if (empty($messages)) {
            $result = [];
            (new ValidationWalker($this->externalTerminologyValidation))->walk($composition, $result, $template->getTree(), $template->getTemplateId());

            return $result;
        } else {
            return array_map(function (RMObjectValidationMessage $validationMessage) {
                return new ConstraintViolation($validationMessage->getPath(), $validationMessage->getMessage());
            }, $messages);
        }
    }

    /**
     * Enable or disable invariant checks in an archie library.
     *
     * @param  bool  $validateInvariants the boolean value
     */
    public function setRunInvariantChecks(bool $validateInvariants): void
    {
        $this->rmObjectValidator->setRunInvariantChecks($validateInvariants);
    }

    /**
     * Sets the ExternalTerminologyValidation used to validate external terminology.
     *
     * @param  ExternalTerminologyValidation  $externalTerminologyValidation the external terminology validator
     */
    public function setExternalTerminologyValidation(ExternalTerminologyValidation $externalTerminologyValidation): void
    {
        $this->externalTerminologyValidation = $externalTerminologyValidation;
    }

    public function getRmObjectValidator(): RMObjectValidator
    {
        return $this->rmObjectValidator;
    }
}
