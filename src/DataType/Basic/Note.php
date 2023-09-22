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

    private ?string $languageID;

    private ?string $languageLocaleID;

    public function __construct(string $content)
    {
        $this->content          = $content;
        $this->subjectCode      = null;
        $this->languageID       = null;
        $this->languageLocaleID = null;
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

    public function getLanguageID(): ?string
    {
        return $this->languageID;
    }

    public function setLanguageID(?string $languageID): static
    {
        $this->languageID = $languageID;

        return $this;
    }

    public function getLanguageLocaleID(): ?string
    {
        return $this->languageLocaleID;
    }

    public function setLanguageLocaleID(?string $languageLocaleID): static
    {
        $this->languageLocaleID = $languageLocaleID;

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

        if ($this->languageID) {
            $currentNode->setAttribute('languageID', $this->languageID);
        }

        if ($this->languageLocaleID) {
            $currentNode->setAttribute('languageLocaleID', $this->languageLocaleID);
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

        /** @var \DOMElement $noteItem */
        $noteItem = $noteElements->item(0);
        $content  = (string) $noteItem->nodeValue;

        $note = new self($content);

        $languageID = $noteItem->getAttribute('languageID');
        $note->setLanguageID($languageID);

        $languageLocaleID = $noteItem->getAttribute('languageLocaleID');
        $note->setLanguageLocaleID($languageLocaleID);

        // @todo si nécessaire : rechercher (regex) le code entre ## si présent et l'affecter à subjectcode

        return $note;
    }
}
