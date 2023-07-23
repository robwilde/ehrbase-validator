<?php

namespace App\Services\Archie\RM\Support\Identification;

class TerminologyId extends ObjectId
{
    public function __construct($args = [])
    {
        parent::__construct($args);
        if (isset($args['terminologyId']) && isset($args['terminologyVersion'])) {
            if (empty($args['terminologyVersion'])) {
                parent::setValue($args['terminologyId']);
            } else {
                parent::setValue((array) $args['terminologyId']);
            }
        } elseif (isset($args['terminologyId'])) {
            $this->__construct(['terminologyId' => $args['terminologyId'], 'terminologyVersion' => null]);
        }
    }
}
