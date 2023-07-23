<?php

namespace App\Services\WebTemplate;

use com\nedap\archie\rm\datavalues\quantity\DvOrdinal;

class DvOrdinalValidator implements ConstraintValidator
{
    /**
     * {@inheritDoc}
     */
    public function getAssociatedClass()
    {
        return 'DvOrdinal';
    }

    /**
     * {@inheritDoc}
     */
    public function validate(DvOrdinal $dvOrdinal, WebTemplateNode $node)
    {
        if (! WebTemplateValidationUtils::hasInputs($node)) {
            return [];
        }

        $symbol = $dvOrdinal->getSymbol();
        $result = (new DvCodedTextValidator())->validate($symbol, $node);

        if (empty($result)) {
            $input = WebTemplateValidationUtils::getInputWithType($node, 'CODED_TEXT');
            foreach ($input->getList() as $inputValue) {
                if ($inputValue->getValue() == $symbol->getDefiningCode()->getCodeString()) {
                    if ($dvOrdinal->getValue() != $inputValue->getOrdinal()) {
                        array_push($result, new ConstraintViolation(
                            $node->getAqlPath(),
                            sprintf(
                                'The value %s must be %s',
                                $dvOrdinal->getValue(),
                                $inputValue->getOrdinal())));
                    }
                    break;
                }
            }
        }

        return $result;
    }
}
