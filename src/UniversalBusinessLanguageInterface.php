<?php

namespace Tiime\UniversalBusinessLanguage;

interface UniversalBusinessLanguageInterface
{
    public function toXML(): \DOMDocument;

    public static function fromXML(\DOMDocument $document): self;
}
