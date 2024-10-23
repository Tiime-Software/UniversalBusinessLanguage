<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Ubl21\CreditNote\Aggregate;

use Tiime\EN16931\Codelist\ReferenceQualifierCodeUNTDID1153 as ReferenceQualifierCode;
use Tiime\EN16931\DataType\Identifier\ObjectIdentifier;
use Tiime\UniversalBusinessLanguage\Ubl21\CreditNote\DataType\Aggregate\AdditionalDocumentReference;
use Tiime\UniversalBusinessLanguage\Ubl21\CreditNote\DataType\Aggregate\Attachment;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class CreditNoteAdditionalDocumentReferenceTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:AdditionalDocumentReference>
    <cbc:ID schemeID="AUN">AB23456</cbc:ID>
    <cbc:DocumentTypeCode>130</cbc:DocumentTypeCode>
    <cbc:DocumentDescription>Time list</cbc:DocumentDescription>
    <cac:Attachment>
      <cbc:EmbeddedDocumentBinaryObject mimeCode="text/csv" filename="Hours-spent.csv">aHR0cHM6Ly90ZXN0LXZlZmEuZGlmaS5uby9wZXBwb2xiaXMvcG9hY2MvYmlsbGluZy8zLjAvYmlzLw==</cbc:EmbeddedDocumentBinaryObject>
      <cac:ExternalReference>
        <cbc:URI>http://www.example.com/index.html</cbc:URI>
      </cac:ExternalReference>
    </cac:Attachment>
  </cac:AdditionalDocumentReference>
</CreditNote>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:AdditionalDocumentReference>
    <cbc:ID>AB23456</cbc:ID>
  </cac:AdditionalDocumentReference>
</CreditNote>
XML;

    protected const XML_VALID_NO_LINE = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</CreditNote>
XML;

    protected const XML_INVALID_MANY_CONTENTS = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:AdditionalDocumentReference>
    <cbc:ID>AB23456</cbc:ID>
    <cbc:ID>BC34567</cbc:ID>
  </cac:AdditionalDocumentReference>
</CreditNote>
XML;

    protected const XML_VALID_MANY_LINES = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:AdditionalDocumentReference>
    <cbc:ID>AB23456</cbc:ID>
  </cac:AdditionalDocumentReference>
  <cac:AdditionalDocumentReference>
    <cbc:ID>BC34567</cbc:ID>
  </cac:AdditionalDocumentReference>
</CreditNote>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObjects     = AdditionalDocumentReference::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(1, $ublObjects);
        $ublObject = $ublObjects[0];
        $this->assertInstanceOf(AdditionalDocumentReference::class, $ublObject);
        $this->assertInstanceOf(ObjectIdentifier::class, $ublObject->getIdentifier());
        $this->assertEquals('AB23456', $ublObject->getIdentifier()->value);
        $this->assertEquals(ReferenceQualifierCode::tryFrom('AUN'), $ublObject->getIdentifier()->scheme);
        $this->assertEquals('130', $ublObject->getDocumentTypeCode());
        $this->assertEquals('Time list', $ublObject->getDocumentDescription());
        $this->assertInstanceOf(Attachment::class, $ublObject->getAttachment());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObjects     = AdditionalDocumentReference::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(1, $ublObjects);
        $ublObject = $ublObjects[0];
        $this->assertInstanceOf(AdditionalDocumentReference::class, $ublObject);
        $this->assertInstanceOf(ObjectIdentifier::class, $ublObject->getIdentifier());
        $this->assertNull($ublObject->getDocumentTypeCode());
        $this->assertNull($ublObject->getDocumentDescription());
        $this->assertNull($ublObject->getAttachment());
    }

    public function testCanBeCreatedFromNoLine(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_NO_LINE);
        $ublObjects     = AdditionalDocumentReference::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(0, $ublObjects);
    }

    public function testCannotBeCreatedFromManyContents(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_CONTENTS);
        AdditionalDocumentReference::fromXML($this->xpath, $currentElement);
    }

    public function testCanBeCreatedFromManyLines(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MANY_LINES);
        $ublObjects     = AdditionalDocumentReference::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(2, $ublObjects);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObjects      = AdditionalDocumentReference::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyCreditNoteRootDocument();

        foreach ($ublObjects as $ublObject) {
            $rootDestination->appendChild($ublObject->toXML($this->document));
        }
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
