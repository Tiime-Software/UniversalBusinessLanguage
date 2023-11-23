<?php

namespace Tiime\UniversalBusinessLanguage\Tests\helpers;

use PHPUnit\Framework\TestCase;

class BaseXMLNodeTestWithHelpers extends TestCase
{
    protected ?\DOMDocument $document = null;

    protected ?\DOMXPath $xpath = null;

    protected function setUp(): void
    {
        unset($this->document, $this->xpath);
    }

    protected function loadXMLDocument(string $xmlSource): \DOMElement
    {
        $this->document                     = new \DOMDocument('1.0', 'UTF-8');
        $this->document->preserveWhiteSpace = false;
        $this->document->formatOutput       = true;

        if (!$this->document->loadXML($xmlSource)) {
            $this->fail('Source is not valid');
        }
        $this->xpath = new \DOMXPath($this->document);
        $this->xpath->registerNamespace('ubl', 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2');

        return $this->document->documentElement;
    }

    protected function formatXMLOutput(): string
    {
        $tmpDocument                     = new \DOMDocument('1.0', 'UTF-8');
        $tmpDocument->preserveWhiteSpace = false;
        $tmpDocument->formatOutput       = true;
        $tmpDocument->loadXML($this->document->saveXml());

        return $tmpDocument->saveXml($tmpDocument->documentElement, \LIBXML_NOEMPTYTAG);
    }

    protected function generateEmptyInvoiceRootDocument(): \DOMElement
    {
        $this->document = new \DOMDocument('1.0', 'UTF-8');

        $universalBusinessLanguage = $this->document->createElement('Invoice');
        $universalBusinessLanguage->setAttribute(
            'xmlns',
            'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2'
        );
        $universalBusinessLanguage->setAttribute(
            'xmlns:cac',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2'
        );
        $universalBusinessLanguage->setAttribute(
            'xmlns:cbc',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2'
        );
        $this->document->appendChild($universalBusinessLanguage);

        $this->xpath = new \DOMXPath($this->document);

        return $this->document->documentElement;
    }

    protected function generateEmptyCreditNoteRootDocument(): \DOMElement
    {
        $this->document = new \DOMDocument('1.0', 'UTF-8');

        $universalBusinessLanguage = $this->document->createElement('CreditNote');
        $universalBusinessLanguage->setAttribute(
            'xmlns',
            'urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2'
        );
        $universalBusinessLanguage->setAttribute(
            'xmlns:cac',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2'
        );
        $universalBusinessLanguage->setAttribute(
            'xmlns:cbc',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2'
        );
        $this->document->appendChild($universalBusinessLanguage);

        $this->xpath = new \DOMXPath($this->document);

        return $this->document->documentElement;
    }
}
