<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\Ubl21\CreditNote\DataType\Basic;

use Tiime\EN16931\Codelist\TextSubjectCodeUNTDID4451;

/**
 * BG-1..
 */
class Note
{
    protected const XML_NODE = 'cbc:Note';

    private ?TextSubjectCodeUNTDID4451 $subjectCode;
    private string $content;

    public function __construct(?TextSubjectCodeUNTDID4451 $subjectCode, string $content)
    {
        $this->subjectCode = $subjectCode;
        $this->content     = $content;
    }

    public function getSubjectCode(): TextSubjectCodeUNTDID4451
    {
        return $this->subjectCode;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $note = ($this->subjectCode ? "##{$this->subjectCode->value}##" : '') . $this->content;

        return $document->createElement(self::XML_NODE, $note);
    }

    /**
     * @return array<int, Note>
     */
    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): array
    {
        $noteElements = $xpath->query(\sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $noteElements->count()) {
            return [];
        }

        $notes = [];

        $pattern = '/##(.*?)##(.*)/';

        /** @var \DOMElement $noteElement */
        foreach ($noteElements as $noteElement) {
            preg_match($pattern, $noteElement->nodeValue, $matches);

            $subjectCode = $matches[1] ? TextSubjectCodeUNTDID4451::tryFrom($matches[1]) : null;
            $content     = $matches[2] ?? $noteElement->nodeValue;

            $note = new self($subjectCode, $content);

            $notes[] = $note;
        }

        return $notes;
    }
}
