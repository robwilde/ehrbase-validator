<?php

namespace App\Services\Archie\RMInfo;

//There is no direct equivalent of Java annotations in PHP. However, you can use DocBlocks (comments) to achieve similar functionality. Here's how you might translate the given Java code to PHP:

/**
 * This annotation defines a method as being an invariant, which means it will be checked during validation
 * automatically. The method must be of a boolean return type, and not have any parameters
 *
 * @Annotation
 *
 * @Target("METHOD")
 */
class Invariant
{
    /** @var string the name of the Invariant */
    public string $value;

    /** @var bool whether the Invariant is ignored */
    public bool $ignored = false;
}

//Please note that this PHP code will not have the same functionality as the Java code.
// In PHP, annotations are not part of the language itself and are only comments that can be parsed by certain libraries.
// The `@Annotation` and `@Target` tags are used by the Doctrine Annotations library.
