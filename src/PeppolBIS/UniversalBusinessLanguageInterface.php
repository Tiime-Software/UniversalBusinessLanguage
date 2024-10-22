<?php

namespace Tiime\UniversalBusinessLanguage\PeppolBIS;

interface UniversalBusinessLanguageInterface
{
    public function toXML(): \DOMDocument;

    public static function fromXML(\DOMDocument $document): self;
}
