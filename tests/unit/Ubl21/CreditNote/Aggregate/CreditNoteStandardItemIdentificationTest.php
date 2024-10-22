<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Ubl21\CreditNote\Aggregate;

use Tiime\EN16931\DataType\Identifier\StandardItemIdentifier;
use Tiime\UniversalBusinessLanguage\Ubl21\CreditNote\DataType\Aggregate\StandardItemIdentification;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class CreditNoteStandardItemIdentificationTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:StandardItemIdentification>
    <cbc:ID schemeID="0088">59878541259876</cbc:ID>
  </cac:StandardItemIdentification>
</CreditNote>
XML;

    protected const XML_VALID_NO_LINE = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</CreditNote>
XML;

    protected const XML_INVALID_MANY_LINES = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:StandardItemIdentification>
    <cbc:ID schemeID="0088">59878541259876</cbc:ID>
  </cac:StandardItemIdentification>
  <cac:StandardItemIdentification>
    <cbc:ID schemeID="0088">59878541259876</cbc:ID>
  </cac:StandardItemIdentification>
</CreditNote>
XML;

    public function testCanBeCreatedFromContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject      = StandardItemIdentification::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(StandardItemIdentification::class, $ublObject);
        $this->assertInstanceOf(StandardItemIdentifier::class, $ublObject->getStandardItemIdentifier());
    }

    public function testCanBeCreatedFromNoLine(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_NO_LINE);
        $ublObject      = StandardItemIdentification::fromXML($this->xpath, $currentElement);
        $this->assertNull($ublObject);
    }

    public function testCannotBeCreatedFromManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_LINES);
        StandardItemIdentification::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject       = StandardItemIdentification::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyCreditNoteRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_CONTENT, $generatedOutput);
    }
}
