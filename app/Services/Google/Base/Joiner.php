<?php

namespace App\Services\Google\Base;

class Joiner
{
    private string $delimiter;

    private bool $skipNulls = false;

    private function __construct($delimiter)
    {
        $this->delimiter = $delimiter;
    }

    public static function on($delimiter): static
    {
        return new static($delimiter);
    }

    public function skipNulls(): static
    {
        $this->skipNulls = true;

        return $this;
    }

    public function join(array $array): string
    {
        if ($this->skipNulls) {
            $array = array_filter($array, function ($value) {
                return $value !== null;
            });
        }

        return implode($this->delimiter, $array);
    }
}
