<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Ubl21\CreditNote\Aggregate;

use Tiime\EN16931\DataType\ItemTypeCode;
use Tiime\UniversalBusinessLanguage\Ubl21\CreditNote\DataType\Aggregate\CommodityClassification;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class CreditNoteCommodityClassificationTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:CommodityClassification>
    <cbc:ItemClassificationCode listID="STI" listVersionID="0.1">9873242</cbc:ItemClassificationCode>
  </cac:CommodityClassification>
</CreditNote>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:CommodityClassification>
    <cbc:ItemClassificationCode listID="STI">9873242</cbc:ItemClassificationCode>
  </cac:CommodityClassification>
</CreditNote>
XML;

    protected const XML_VALID_NO_LINE = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</CreditNote>
XML;

    protected const XML_VALID_MANY_LINES = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:CommodityClassification>
    <cbc:ItemClassificationCode listID="STI">9873242</cbc:ItemClassificationCode>
  </cac:CommodityClassification>
  <cac:CommodityClassification>
    <cbc:ItemClassificationCode listID="STI">9873242</cbc:ItemClassificationCode>
  </cac:CommodityClassification>
</CreditNote>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObjects     = CommodityClassification::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(1, $ublObjects);
        $ublObject = $ublObjects[0];
        $this->assertInstanceOf(CommodityClassification::class, $ublObject);
        $this->assertEquals('9873242', $ublObject->getItemClassificationCode());
        $this->assertEquals('0.1', $ublObject->getListVersionIdentifier());
        $this->assertInstanceOf(ItemTypeCode::class, $ublObject->getListIdentifier());
        $this->assertEquals(ItemTypeCode::tryFrom('STI'), $ublObject->getListIdentifier());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObjects     = CommodityClassification::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(1, $ublObjects);
        $ublObject = $ublObjects[0];
        $this->assertInstanceOf(CommodityClassification::class, $ublObject);
        $this->assertEquals('9873242', $ublObject->getItemClassificationCode());
        $this->assertNull($ublObject->getListVersionIdentifier());
        $this->assertInstanceOf(ItemTypeCode::class, $ublObject->getListIdentifier());
        $this->assertEquals(ItemTypeCode::tryFrom('STI'), $ublObject->getListIdentifier());
    }

    public function testCanBeCreatedFromNoLine(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_NO_LINE);
        $ublObjects     = CommodityClassification::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(0, $ublObjects);
    }

    public function testCanBeCreatedFromManyLines(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MANY_LINES);
        $ublObjects     = CommodityClassification::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(2, $ublObjects);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObjects      = CommodityClassification::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyCreditNoteRootDocument();

        foreach ($ublObjects as $ublObject) {
            $rootDestination->appendChild($ublObject->toXML($this->document));
        }
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
