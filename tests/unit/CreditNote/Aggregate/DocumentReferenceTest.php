<?php

use Tiime\EN16931\DataType\Identifier\ObjectIdentifier;
use Tiime\EN16931\DataType\ObjectSchemeCode;
use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Aggregate\DocumentReference;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class DocumentReferenceTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:DocumentReference>
    <cbc:ID schemeID="ABZ">AB12345</cbc:ID>
    <cbc:DocumentTypeCode>130</cbc:DocumentTypeCode>
  </cac:DocumentReference>
</Invoice>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:DocumentReference>
    <cbc:ID>AB12345</cbc:ID>
    <cbc:DocumentTypeCode>130</cbc:DocumentTypeCode>
  </cac:DocumentReference>
</Invoice>
XML;

    protected const XML_VALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_MANY_LINES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:DocumentReference>
    <cbc:ID>AB12345</cbc:ID>
    <cbc:DocumentTypeCode>130</cbc:DocumentTypeCode>
  </cac:DocumentReference>
  <cac:DocumentReference>
    <cbc:ID>AB12345</cbc:ID>
    <cbc:DocumentTypeCode>130</cbc:DocumentTypeCode>
  </cac:DocumentReference>
</Invoice>
XML;

    protected const XML_INVALID_MANY_CONTENTS = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:DocumentReference>
    <cbc:ID>AB12345</cbc:ID>
    <cbc:ID>AB12345</cbc:ID>
    <cbc:DocumentTypeCode>130</cbc:DocumentTypeCode>
  </cac:DocumentReference>
</Invoice>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject      = DocumentReference::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(DocumentReference::class, $ublObject);
        $this->assertInstanceOf(ObjectIdentifier::class, $ublObject->getIdentifier());
        $this->assertEquals('AB12345', $ublObject->getIdentifier()->value);
        $this->assertEquals(ObjectSchemeCode::tryFrom('ABZ'), $ublObject->getIdentifier()->scheme);
        $this->assertEquals('130', $ublObject->getDocumentTypeCode());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject      = DocumentReference::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(DocumentReference::class, $ublObject);
        $this->assertInstanceOf(ObjectIdentifier::class, $ublObject->getIdentifier());
        $this->assertEquals('AB12345', $ublObject->getIdentifier()->value);
        $this->assertNull($ublObject->getIdentifier()->scheme);
        $this->assertEquals('130', $ublObject->getDocumentTypeCode());
    }

    public function testCanBeCreatedFromNoLine(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_NO_LINE);
        $ublObject      = DocumentReference::fromXML($this->xpath, $currentElement);
        $this->assertNull($ublObject);
    }

    public function testCannotBeCreatedFromManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_LINES);
        DocumentReference::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromManyContents(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_CONTENTS);
        DocumentReference::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject       = DocumentReference::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
