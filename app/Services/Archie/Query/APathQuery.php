<?php

namespace App\Services\Archie\Query;

use App\Services\Archie\Paths\PathSegment;

class APathQuery
{
    private array $pathSegments = [];

    public function __construct(string $query)
    {
        if (! startsWith($query, '/') && ! str_contains($query, '/') && ! str_contains($query, '[')) {
            $this->pathSegments[] = new PathSegment($query, null, null);
        } elseif ($query !== '/') {
            // For now, simply splitting the path by '/' as PHP doesn't have a library like ANTLR
            $parts = explode('/', $query);
            foreach ($parts as $part) {
                if (! empty($part)) {
                    $this->pathSegments[] = new PathSegment($part, null, null);
                }
            }
        }
    }

    public function getPathSegments(): array
    {
        return $this->pathSegments;
    }

    public function __toString()
    {
        if (count($this->pathSegments) == 0) {
            return '/';
        }

        return implode('/', $this->pathSegments);
    }
}
