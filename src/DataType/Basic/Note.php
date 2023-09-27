<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\DataType\Basic;

use Tiime\EN16931\DataType\InvoiceNoteCode;

/**
 * BG-1.
 */
class Note
{
    protected const XML_NODE = 'cbc:Note';

    /**
     * BT-22.
     */
    private string $content;

    /**
     * BT-21.
     */
    private ?InvoiceNoteCode $subjectCode;

    private ?string $languageIdentifier;

    private ?string $languageLocaleIdentifier;

    public function __construct(string $content)
    {
        $this->content                  = $content;
        $this->subjectCode              = null;
        $this->languageIdentifier       = null;
        $this->languageLocaleIdentifier = null;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getSubjectCode(): ?InvoiceNoteCode
    {
        return $this->subjectCode;
    }

    public function setSubjectCode(?InvoiceNoteCode $subjectCode): static
    {
        $this->subjectCode = $subjectCode;

        return $this;
    }

    public function getLanguageIdentifier(): ?string
    {
        return $this->languageIdentifier;
    }

    public function setLanguageIdentifier(?string $languageIdentifier): static
    {
        $this->languageIdentifier = $languageIdentifier;

        return $this;
    }

    public function getLanguageLocaleIdentifier(): ?string
    {
        return $this->languageLocaleIdentifier;
    }

    public function setLanguageLocaleIdentifier(?string $languageLocaleIdentifier): static
    {
        $this->languageLocaleIdentifier = $languageLocaleIdentifier;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $content = '';

        if ($this->subjectCode instanceof InvoiceNoteCode) {
            $content .= '#' . $this->subjectCode->value . '#';
        }
        $content .= $this->content;

        $currentNode = $document->createElement(self::XML_NODE, $content);

        if ($this->languageIdentifier) {
            $currentNode->setAttribute('languageID', $this->languageIdentifier);
        }

        if ($this->languageLocaleIdentifier) {
            $currentNode->setAttribute('languageLocaleID', $this->languageLocaleIdentifier);
        }

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $noteElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if (0 === $noteElements->count()) {
            return null;
        }

        if (1 < $noteElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $noteElement */
        $noteElement = $noteElements->item(0);
        $content     = (string) $noteElement->nodeValue;

        $note = new self($content);

        $languageIdentifier = $noteElement->hasAttribute('languageID') ?
            $noteElement->getAttribute('languageID') : null;

        if (\is_string($languageIdentifier)) {
            $note->setLanguageIdentifier($languageIdentifier);
        }

        $languageLocaleIdentifier = $noteElement->hasAttribute('languageLocaleID') ?
            $noteElement->getAttribute('languageLocaleID') : null;

        if (\is_string($languageLocaleIdentifier)) {
            $note->setLanguageLocaleIdentifier($languageLocaleIdentifier);
        }

        // @todo si nécessaire : rechercher (regex) le code entre ## si présent et l'affecter à subjectcode

        return $note;
    }
}
