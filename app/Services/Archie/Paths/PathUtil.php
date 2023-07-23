<?php

namespace App\Services\Archie\Paths;

use App\Services\Archie\Definitions\AdlCodeDefinitions;

class PathUtil
{
    public static function getPath(array $pathSegments): string
    {
        $result = '';

        if (count($pathSegments) == 0) {
            return '/';
        }

        foreach ($pathSegments as $segment) {
            $result .= '/';
            $result .= $segment->getNodeName();
            if ($segment->getNodeId() != null && $segment->getNodeId() != AdlCodeDefinitions::PRIMITIVE_NODE_ID) {
                $result .= '[';
                $result .= $segment->getNodeId();
                if ($segment->hasNumberIndex()) {
                    $result .= ',';
                    $result .= $segment->getIndex();
                }
                $result .= ']';
            } elseif ($segment->hasNumberIndex()) {
                $result .= '[';
                $result .= $segment->getIndex();
                $result .= ']';
            }
        }

        return $result;
    }
}
