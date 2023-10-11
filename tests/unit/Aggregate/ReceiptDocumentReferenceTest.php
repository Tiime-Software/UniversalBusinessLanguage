<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Aggregate;

use Tiime\EN16931\DataType\Identifier\LegalRegistrationIdentifier;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\ReceiptDocumentReference;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class ReceiptDocumentReferenceTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:ReceiptDocumentReference>
    <cbc:ID>RECEIV-ADV002</cbc:ID>
  </cac:ReceiptDocumentReference>
</Invoice>
XML;

    protected const XML_VALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_TOO_MANY_ID = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:ReceiptDocumentReference>
    <cbc:ID>RECEIV-ADV002</cbc:ID>
    <cbc:ID>RECEIV-ADV002</cbc:ID>
  </cac:ReceiptDocumentReference>
</Invoice>
XML;
    protected const XML_INVALID_TOO_MANY_LINES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:ReceiptDocumentReference>
    <cbc:ID>RECEIV-ADV002</cbc:ID>
  </cac:ReceiptDocumentReference>
  <cac:ReceiptDocumentReference>
    <cbc:ID>RECEIV-ADV002</cbc:ID>
  </cac:ReceiptDocumentReference>
</Invoice>
XML;

    public function testCanBeCreatedFromContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject = ReceiptDocumentReference::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(ReceiptDocumentReference::class, $ublObject);
        $this->assertEquals("RECEIV-ADV002", $ublObject->getIdentifier());
    }

    public function testCannotBeCreatedFromNoLine(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_NO_LINE);
        $ublDocument = ReceiptDocumentReference::fromXML($this->xpath, $currentElement);
        $this->assertNull($ublDocument);
    }

    public function testCannotBeCreatedFromTooManyId(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_TOO_MANY_ID);
        ReceiptDocumentReference::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromTooManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_TOO_MANY_LINES);
        ReceiptDocumentReference::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject = ReceiptDocumentReference::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertEquals(self::XML_VALID_CONTENT, $generatedOutput);
    }
}