<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Aggregate;

use Tiime\UniversalBusinessLanguage\DataType\Aggregate\DespatchDocumentReference;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class DespatchDocumentReferenceTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:DespatchDocumentReference>
    <cbc:ID>desp98</cbc:ID>
  </cac:DespatchDocumentReference>
</Invoice>
XML;

    protected const XML_VALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_MANY_CONTENTS = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:DespatchDocumentReference>
    <cbc:ID>desp98</cbc:ID>
    <cbc:ID>desp99</cbc:ID>
  </cac:DespatchDocumentReference>
</Invoice>
XML;
    protected const XML_INVALID_MANY_LINES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:DespatchDocumentReference>
    <cbc:ID>desp98</cbc:ID>
  </cac:DespatchDocumentReference>
  <cac:DespatchDocumentReference>
    <cbc:ID>desp99</cbc:ID>
  </cac:DespatchDocumentReference>
</Invoice>
XML;

    public function testCanBeCreatedFromContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject      = DespatchDocumentReference::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(DespatchDocumentReference::class, $ublObject);
        $this->assertEquals('desp98', $ublObject->getIdentifier());
    }

    public function testCanBeCreatedFromNoLine(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_NO_LINE);
        $ublDocument    = DespatchDocumentReference::fromXML($this->xpath, $currentElement);
        $this->assertNull($ublDocument);
    }

    public function testCannotBeCreatedFromManyContents(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_CONTENTS);
        DespatchDocumentReference::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_LINES);
        DespatchDocumentReference::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject       = DespatchDocumentReference::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertEquals(self::XML_VALID_CONTENT, $generatedOutput);
    }
}
