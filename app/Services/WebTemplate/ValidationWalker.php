<?php

namespace App\Services\WebTemplate;

use App\Services\Archie\RM\RMObject;
use com\nedap\archie\rm\datavalues\DvCodedText;
use org\ehrbase\openehr\sdk\serialisation\walker\Context;
use org\ehrbase\openehr\sdk\serialisation\walker\FromCompositionWalker;
use org\ehrbase\openehr\sdk\util\reflection\ReflectionHelper;
use org\ehrbase\openehr\sdk\validation\terminology\ExternalTerminologyValidation;
use org\ehrbase\openehr\sdk\webtemplate\model\WebTemplateNode;
use Psr\Log\LoggerInterface;

class ValidationWalker extends FromCompositionWalker
{
    private $logger;

    private static $VALIDATORS;

    private $defaultValidator;

    public function __construct(ExternalTerminologyValidation $externalTerminologyValidation, LoggerInterface $logger)
    {
        $this->logger = $logger;

        if (empty(self::$VALIDATORS)) {
            self::$VALIDATORS = ReflectionHelper::buildMap(ConstraintValidator::class);
        }

        $this->defaultValidator = new DefaultValidator();

        if ($externalTerminologyValidation !== null) {
            self::$VALIDATORS[DvCodedText::class] = new DvCodedTextValidator($externalTerminologyValidation);
        }
    }

    protected function preHandle(Context $context)
    {
        $node = $context->getNodeDeque()->element();
        $rmObject = $context->getRmObjectDeque()->element();
        $result = $context->getObjectDeque()->element();

        $this->logger->trace("PreHandle: {$node}, rmObject={$rmObject}");

        $validator = $this->getValidator($rmObject);
        $result->addAll($validator->validate($rmObject, $node));
    }

    protected function extract(Context $context, WebTemplateNode $child, bool $isChoice, ?int $i)
    {
        return $context->getObjectDeque()->peek();
    }

    protected function postHandle(Context $context)
    {
        // No-op
    }

    private function getValidator(RMObject $object)
    {
        return self::$VALIDATORS[$object->getClass()] ?? $this->defaultValidator;
    }
}
