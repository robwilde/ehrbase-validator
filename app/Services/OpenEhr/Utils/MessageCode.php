<?php

namespace App\Services\OpenEhr\Utils;

interface MessageCode
{
    /**
     * Get the code that uniquely identifies this within the given context
     */
    public function getCode(): string;

    /**
     * Get the message template, in English
     */
    public function getMessageTemplate(): string;

    /**
     * Get the message translated to the current locale.
     * This is just a stub. Actual implementation would need a localization system like gettext or a similar solution
     */
    public function getMessage(array $args = []): string;

    /**
     * Get the message translated to the specified locale.
     * This is just a stub. Actual implementation would need a localization system like gettext or a similar solution
     */
    public function getMessageForLocale(string $locale, array $args = []): string;
}
