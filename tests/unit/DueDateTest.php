<?php

namespace TiimePDP\UniversalBusinessLanguage\Tests\unit;

use Tiime\UniversalBusinessLanguage\DataType\Basic\DueDate;
use TiimePDP\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class DueDateTest extends BaseXMLNodeTestWithHelpers
{
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
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertEquals(self::XML_REFERENCE, $generatedOutput);
    }
}