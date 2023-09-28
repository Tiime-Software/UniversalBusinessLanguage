<?php

use \PHPUnit\Framework\TestCase;
use \Tiime\UniversalBusinessLanguage\DataType\Basic\DueDate;

class DueDateTest extends TestCase
{
    protected const XML_ROOT = <<<XMLCONTENT
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XMLCONTENT;

    protected const XML_REFERENCE = <<<XMLCONTENT
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:DueDate>2023-01-02</cbc:DueDate>
</Invoice>
XMLCONTENT;

    protected const XML_VALID_DATE = <<<XMLCONTENT
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:DueDate>2023-01-02</cbc:DueDate>
</Invoice>
XMLCONTENT;

    protected const XML_INVALID_DATE = <<<XMLCONTENT
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:DueDate>201</cbc:DueDate>
</Invoice>
XMLCONTENT;

    protected const XML_EMPTY_DATE = <<<XMLCONTENT
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:DueDate></cbc:DueDate>
</Invoice>
XMLCONTENT;

    protected const XML_OMITTED_DATE = <<<XMLCONTENT
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XMLCONTENT;

    protected ?\DOMDocument $document = null;

    protected ?\DOMXPath $xpath = null;

    public function setUp(): void
    {
        unset($this->document);
        unset($this->xpath);
    }

    public function testCanBeCreatedFromValid(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_DATE);
        $ublObject = DueDate::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(DueDate::class, $ublObject);
        $this->assertEquals($ublObject->getDateTimeString(), new \DateTime('2023-01-02'));
    }

    public function testCanBeCreatedFromOmitted(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_OMITTED_DATE);
        $ublObject = DueDate::fromXML($this->xpath, $currentElement);
        $this->assertNull($ublObject);
    }

    public function testCannotBeCreatedFromInvalid(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_DATE);
        DueDate::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromEmpty(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_EMPTY_DATE);
        DueDate::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_DATE);
        $ublObject = DueDate::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->loadXMLDocument(self::XML_ROOT);
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertEquals(self::XML_REFERENCE, $generatedOutput);
    }

    protected function loadXMLDocument(string $xmlSource): \DOMElement
    {
        $this->document = new \DOMDocument('1.0', 'UTF-8');
        $this->document->preserveWhiteSpace = false;
        $this->document->formatOutput = true;
        if (!$this->document->loadXML($xmlSource)) {
            $this->fail('Source is not valid');
        }
        $this->xpath = new \DOMXPath($this->document);

        return $this->document->documentElement;
    }

    protected function formatXMLOutput(): string
    {
        $tmpDocument = new \DOMDocument('1.0', 'UTF-8');
        $tmpDocument->preserveWhiteSpace = false;
        $tmpDocument->formatOutput = true;
        $tmpDocument->loadXML($this->document->saveXml());

        return $tmpDocument->saveXml($tmpDocument->documentElement, LIBXML_NOEMPTYTAG);
    }
}