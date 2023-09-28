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

    public function __construct(string $content)
    {
        $this->content     = $content;
        $this->subjectCode = null;
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

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $content = '';

        if ($this->subjectCode instanceof InvoiceNoteCode) {
            $content .= '#' . $this->subjectCode->value . '#';
        }
        $content .= $this->content;

        $currentNode = $document->createElement(self::XML_NODE, $content);

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

        $content = (string) $noteElements->item(0)->nodeValue;

        // @todo si nécessaire : rechercher (regex) le code entre ## si présent et l'affecter à subjectcode

        return new self($content);
    }
}
