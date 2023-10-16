<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Aggregate;

use Tiime\UniversalBusinessLanguage\DataType\Aggregate\AdditionalItemProperty;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\BuyersItemIdentification;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\ClassifiedTaxCategory;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\CommodityClassification;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\Item;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\OriginCountry;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\SellersItemIdentification;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\StandardItemIdentification;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class ItemTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Item>
    <cbc:Description>Long description of the item on the invoice line</cbc:Description>
    <cbc:Name>Item name</cbc:Name>
    <cac:BuyersItemIdentification>
      <cbc:ID>123455</cbc:ID>
    </cac:BuyersItemIdentification>
    <cac:SellersItemIdentification>
      <cbc:ID>9873242</cbc:ID>
    </cac:SellersItemIdentification>
    <cac:StandardItemIdentification>
      <cbc:ID schemeID="0160">109876700</cbc:ID>
    </cac:StandardItemIdentification>
    <cac:OriginCountry>
      <cbc:IdentificationCode>CN</cbc:IdentificationCode>
    </cac:OriginCountry>
    <cac:CommodityClassification>
      <cbc:ItemClassificationCode listID="STI" listVersionID="0.1">9873242</cbc:ItemClassificationCode>
    </cac:CommodityClassification>
    <cac:ClassifiedTaxCategory>
      <cbc:ID>S</cbc:ID>
      <cbc:Percent>25.00</cbc:Percent>
      <cac:TaxScheme>
        <cbc:ID>VAT</cbc:ID>
      </cac:TaxScheme>
    </cac:ClassifiedTaxCategory>
    <cac:AdditionalItemProperty>
      <cbc:Name>Color</cbc:Name>
      <cbc:Value>Black</cbc:Value>
    </cac:AdditionalItemProperty>
  </cac:Item>
</Invoice>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Item>
    <cbc:Name>Item name</cbc:Name>
    <cac:ClassifiedTaxCategory>
      <cbc:ID>S</cbc:ID>
      <cac:TaxScheme>
        <cbc:ID>VAT</cbc:ID>
      </cac:TaxScheme>
    </cac:ClassifiedTaxCategory>
  </cac:Item>
</Invoice>
XML;

    protected const XML_INVALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_MANY_LINES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:Item>
    <cbc:Name>Item name 1</cbc:Name>
    <cac:ClassifiedTaxCategory>
      <cbc:ID>5</cbc:ID>
      <cac:TaxScheme>
        <cbc:ID>VAT</cbc:ID>
      </cac:TaxScheme>
    </cac:ClassifiedTaxCategory>
  </cac:Item>
  <cac:Item>
    <cbc:Name>Item name 2</cbc:Name>
    <cac:ClassifiedTaxCategory>
      <cbc:ID>5</cbc:ID>
      <cac:TaxScheme>
        <cbc:ID>VAT</cbc:ID>
      </cac:TaxScheme>
    </cac:ClassifiedTaxCategory>
  </cac:Item>
</Invoice>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject      = Item::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(Item::class, $ublObject);
        $this->assertEquals('Long description of the item on the invoice line', $ublObject->getDescription());
        $this->assertEquals('Item name', $ublObject->getName());
        $this->assertInstanceOf(BuyersItemIdentification::class, $ublObject->getBuyersItemIdentification());
        $this->assertInstanceOf(SellersItemIdentification::class, $ublObject->getSellersItemIdentification());
        $this->assertInstanceOf(StandardItemIdentification::class, $ublObject->getStandardItemIdentification());
        $this->assertInstanceOf(OriginCountry::class, $ublObject->getOriginCountry());
        $this->assertIsArray($ublObject->getCommodityClassifications());
        $this->assertCount(1, $ublObject->getCommodityClassifications());

        foreach ($ublObject->getCommodityClassifications() as $commodityClassification) {
            $this->assertInstanceOf(CommodityClassification::class, $commodityClassification);
        }
        $this->assertInstanceOf(ClassifiedTaxCategory::class, $ublObject->getClassifiedTaxCategory());
        $this->assertIsArray($ublObject->getAdditionalProperties());
        $this->assertCount(1, $ublObject->getAdditionalProperties());

        foreach ($ublObject->getAdditionalProperties() as $additionalProperty) {
            $this->assertInstanceOf(AdditionalItemProperty::class, $additionalProperty);
        }
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject      = Item::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(Item::class, $ublObject);
        $this->assertNull($ublObject->getDescription());
        $this->assertEquals('Item name', $ublObject->getName());
        $this->assertNull($ublObject->getBuyersItemIdentification());
        $this->assertNull($ublObject->getSellersItemIdentification());
        $this->assertNull($ublObject->getStandardItemIdentification());
        $this->assertNull($ublObject->getOriginCountry());
        $this->assertIsArray($ublObject->getCommodityClassifications());
        $this->assertCount(0, $ublObject->getCommodityClassifications());
        $this->assertInstanceOf(ClassifiedTaxCategory::class, $ublObject->getClassifiedTaxCategory());
        $this->assertIsArray($ublObject->getAdditionalProperties());
        $this->assertCount(0, $ublObject->getAdditionalProperties());
    }

    public function testCannotBeCreatedFromNoLine(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NO_LINE);
        Item::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_LINES);
        Item::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject       = Item::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertEquals(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
