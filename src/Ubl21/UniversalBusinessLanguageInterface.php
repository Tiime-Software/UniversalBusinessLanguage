<?php

namespace Tiime\UniversalBusinessLanguage\Ubl21;

interface UniversalBusinessLanguageInterface
{
    public function toXML(): \DOMDocument;

    public static function fromXML(\DOMDocument $document): self;
}
