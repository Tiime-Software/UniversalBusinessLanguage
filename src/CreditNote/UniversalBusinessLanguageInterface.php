<?php

namespace Tiime\UniversalBusinessLanguage\CreditNote;

interface UniversalBusinessLanguageInterface
{
    public function toXML(): \DOMDocument;

    public static function fromXML(\DOMDocument $document): self;
}
