<?php

use \PHPUnit\Framework\TestCase;
use \Tiime\UniversalBusinessLanguage\DataType\Basic\DueDate;

class DueDateTest extends TestCase
{
    protected const XML_NODE = "ubl:Invoice";

    protected const XML_ROOT = <<<XMLCONTENT
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XMLCONTENT;

    protected const XML_REFERENCE = <<<XMLCONTENT
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
<cbc:DueDate>2023-01-02</cbc:DueDate></Invoice>
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
        $this->loadXMLDocument(self::XML_VALID_DATE);
        $currentElement = $this->document->documentElement;
        $ublObject = DueDate::fromXML($this->xpath, $currentElement);
        $this->assertEquals($ublObject->getDateTimeString(), new \DateTime('2023-01-02'));
    }

    public function testCanBeCreatedFromOmitted(): void
    {
        $this->loadXMLDocument(self::XML_OMITTED_DATE);
        $currentElement = $this->document->documentElement;
        $ublObject = DueDate::fromXML($this->xpath, $currentElement);
        $this->assertNull($ublObject);
    }

    public function testCannotBeCreatedFromInvalid(): void
    {
        $this->expectException(\Exception::class);
        $this->loadXMLDocument(self::XML_INVALID_DATE);
        $currentElement = $this->document->documentElement;
        DueDate::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromEmpty(): void
    {
        $this->expectException(\Exception::class);
        $this->loadXMLDocument(self::XML_EMPTY_DATE);
        $currentElement = $this->document->documentElement;
        DueDate::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $this->loadXMLDocument(self::XML_VALID_DATE);
        $currentElement = $this->document->documentElement;
        $ublObject = DueDate::fromXML($this->xpath, $currentElement);
        $this->loadXMLDocument(self::XML_ROOT);
        $rootDestination = $this->document->documentElement;
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput =  $this->document->saveXml($this->document->documentElement, LIBXML_NOEMPTYTAG);
        $this->assertEquals(self::XML_REFERENCE, $generatedOutput);
    }

    protected function loadXMLDocument($xmlSource): void
    {
        $this->document = new \DOMDocument('1.0', 'UTF-8');
        $this->document->preserveWhiteSpace = false;
        $this->document->formatOutput = true;
        if(!$this->document->loadXML($xmlSource)) {
            $this->fail('Source is not valid');
        }
        $this->xpath = new \DOMXPath($this->document);
    }
}