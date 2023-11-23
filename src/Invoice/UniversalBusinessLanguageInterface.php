<?php

namespace Tiime\UniversalBusinessLanguage\Invoice;

interface UniversalBusinessLanguageInterface
{
    public function toXML(): \DOMDocument;

    public static function fromXML(\DOMDocument $document): self;
}
