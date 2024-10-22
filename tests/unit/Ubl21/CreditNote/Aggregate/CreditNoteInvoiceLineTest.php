<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Ubl21\CreditNote\Aggregate;

use Tiime\EN16931\DataType\Identifier\InvoiceLineIdentifier;
use Tiime\UniversalBusinessLanguage\Ubl21\CreditNote\DataType\Aggregate\CreditNoteLine;
use Tiime\UniversalBusinessLanguage\Ubl21\CreditNote\DataType\Aggregate\DocumentReference;
use Tiime\UniversalBusinessLanguage\Ubl21\CreditNote\DataType\Aggregate\InvoiceLineAllowance;
use Tiime\UniversalBusinessLanguage\Ubl21\CreditNote\DataType\Aggregate\InvoiceLineCharge;
use Tiime\UniversalBusinessLanguage\Ubl21\CreditNote\DataType\Aggregate\InvoiceLineInvoicePeriod;
use Tiime\UniversalBusinessLanguage\Ubl21\CreditNote\DataType\Aggregate\Item;
use Tiime\UniversalBusinessLanguage\Ubl21\CreditNote\DataType\Aggregate\OrderLineReference;
use Tiime\UniversalBusinessLanguage\Ubl21\CreditNote\DataType\Aggregate\Price;
use Tiime\UniversalBusinessLanguage\Ubl21\CreditNote\DataType\Basic\CreditedQuantity;
use Tiime\UniversalBusinessLanguage\Ubl21\CreditNote\DataType\Basic\LineExtensionAmount;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class CreditNoteInvoiceLineTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:CreditNoteLine>
    <cbc:ID>15</cbc:ID>
    <cbc:Note>New article number 12345</cbc:Note>
    <cbc:CreditedQuantity unitCode="C62">100.0000</cbc:CreditedQuantity>
    <cbc:LineExtensionAmount currencyID="EUR">2145.00</cbc:LineExtensionAmount>
    <cbc:AccountingCost>1287:65464</cbc:AccountingCost>
    <cac:InvoicePeriod>
      <cbc:StartDate>2017-10-05</cbc:StartDate>
      <cbc:EndDate>2017-10-15</cbc:EndDate>
    </cac:InvoicePeriod>
    <cac:OrderLineReference>
      <cbc:LineID>3</cbc:LineID>
    </cac:OrderLineReference>
    <cac:DocumentReference>
      <cbc:ID schemeID="ABZ">AB12345</cbc:ID>
      <cbc:DocumentTypeCode>130</cbc:DocumentTypeCode>
    </cac:DocumentReference>
    <cac:AllowanceCharge>
      <cbc:ChargeIndicator>false</cbc:ChargeIndicator>
      <cbc:AllowanceChargeReasonCode>95</cbc:AllowanceChargeReasonCode>
      <cbc:AllowanceChargeReason>Discount</cbc:AllowanceChargeReason>
      <cbc:MultiplierFactorNumeric>20.00</cbc:MultiplierFactorNumeric>
      <cbc:Amount currencyID="EUR">200.00</cbc:Amount>
      <cbc:BaseAmount currencyID="EUR">1000.00</cbc:BaseAmount>
    </cac:AllowanceCharge>
    <cac:AllowanceCharge>
      <cbc:ChargeIndicator>true</cbc:ChargeIndicator>
      <cbc:AllowanceChargeReasonCode>AA</cbc:AllowanceChargeReasonCode>
      <cbc:AllowanceChargeReason>Google Ads</cbc:AllowanceChargeReason>
      <cbc:MultiplierFactorNumeric>20.00</cbc:MultiplierFactorNumeric>
      <cbc:Amount currencyID="EUR">200.00</cbc:Amount>
      <cbc:BaseAmount currencyID="EUR">1000.00</cbc:BaseAmount>
    </cac:AllowanceCharge>
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
    <cac:Price>
      <cbc:PriceAmount currencyID="EUR">23.45</cbc:PriceAmount>
      <cbc:BaseQuantity unitCode="C62">1.0000</cbc:BaseQuantity>
      <cac:AllowanceCharge>
        <cbc:ChargeIndicator>false</cbc:ChargeIndicator>
        <cbc:Amount currencyID="EUR">100.00</cbc:Amount>
        <cbc:BaseAmount currencyID="EUR">123.45</cbc:BaseAmount>
      </cac:AllowanceCharge>
    </cac:Price>
  </cac:CreditNoteLine>
</CreditNote>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:CreditNoteLine>
    <cbc:ID>15</cbc:ID>
    <cbc:CreditedQuantity unitCode="C62">100</cbc:CreditedQuantity>
    <cbc:LineExtensionAmount currencyID="EUR">2145.00</cbc:LineExtensionAmount>
    <cac:Item>
      <cbc:Name>Item name</cbc:Name>
      <cac:ClassifiedTaxCategory>
        <cbc:ID>S</cbc:ID>
        <cac:TaxScheme>
          <cbc:ID>VAT</cbc:ID>
        </cac:TaxScheme>
      </cac:ClassifiedTaxCategory>
    </cac:Item>
    <cac:Price>
      <cbc:PriceAmount currencyID="EUR">23.45</cbc:PriceAmount>
    </cac:Price>
  </cac:CreditNoteLine>
</CreditNote>
XML;

    protected const XML_INVALID_NO_LINE = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</CreditNote>
XML;

    protected const XML_INVALID_MANY_CONTENTS = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:CreditNoteLine>
    <cbc:ID>15</cbc:ID>
    <cbc:ID>15</cbc:ID>
    <cbc:CreditedQuantity unitCode="C62">100</cbc:CreditedQuantity>
    <cbc:LineExtensionAmount currencyID="EUR">2145.00</cbc:LineExtensionAmount>
    <cac:Item>
      <cbc:Name>Item name</cbc:Name>
      <cac:ClassifiedTaxCategory>
        <cbc:ID>S</cbc:ID>
        <cac:TaxScheme>
          <cbc:ID>VAT</cbc:ID>
        </cac:TaxScheme>
      </cac:ClassifiedTaxCategory>
    </cac:Item>
    <cac:Price>
      <cbc:PriceAmount currencyID="EUR">23.45</cbc:PriceAmount>
    </cac:Price>
  </cac:CreditNoteLine>
</CreditNote>
XML;

    protected const XML_VALID_MANY_LINES = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:CreditNoteLine>
    <cbc:ID>15</cbc:ID>
    <cbc:CreditedQuantity unitCode="C62">100</cbc:CreditedQuantity>
    <cbc:LineExtensionAmount currencyID="EUR">2145.00</cbc:LineExtensionAmount>
    <cac:Item>
      <cbc:Name>Item name</cbc:Name>
      <cac:ClassifiedTaxCategory>
        <cbc:ID>S</cbc:ID>
        <cac:TaxScheme>
          <cbc:ID>VAT</cbc:ID>
        </cac:TaxScheme>
      </cac:ClassifiedTaxCategory>
    </cac:Item>
    <cac:Price>
      <cbc:PriceAmount currencyID="EUR">23.45</cbc:PriceAmount>
    </cac:Price>
  </cac:CreditNoteLine>
  <cac:CreditNoteLine>
    <cbc:ID>15</cbc:ID>
    <cbc:CreditedQuantity unitCode="C62">100</cbc:CreditedQuantity>
    <cbc:LineExtensionAmount currencyID="EUR">2145.00</cbc:LineExtensionAmount>
    <cac:Item>
      <cbc:Name>Item name</cbc:Name>
      <cac:ClassifiedTaxCategory>
        <cbc:ID>S</cbc:ID>
        <cac:TaxScheme>
          <cbc:ID>VAT</cbc:ID>
        </cac:TaxScheme>
      </cac:ClassifiedTaxCategory>
    </cac:Item>
    <cac:Price>
      <cbc:PriceAmount currencyID="EUR">23.45</cbc:PriceAmount>
    </cac:Price>
  </cac:CreditNoteLine>
</CreditNote>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObjects     = CreditNoteLine::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(1, $ublObjects);
        $ublObject = $ublObjects[0];
        $this->assertInstanceOf(CreditNoteLine::class, $ublObject);
        $this->assertInstanceOf(InvoiceLineIdentifier::class, $ublObject->getInvoiceLineIdentifier());
        $this->assertEquals('New article number 12345', $ublObject->getNote());
        $this->assertInstanceOf(CreditedQuantity::class, $ublObject->getCreditedQuantity());
        $this->assertInstanceOf(LineExtensionAmount::class, $ublObject->getLineExtensionAmount());
        $this->assertEquals('1287:65464', $ublObject->getAccountingCost());
        $this->assertInstanceOf(InvoiceLineInvoicePeriod::class, $ublObject->getInvoicePeriod());
        $this->assertInstanceOf(OrderLineReference::class, $ublObject->getOrderLineReference());
        $this->assertInstanceOf(DocumentReference::class, $ublObject->getDocumentReference());
        $this->assertIsArray($ublObject->getAllowances());
        $this->assertCount(1, $ublObject->getAllowances());

        foreach ($ublObject->getAllowances() as $allowance) {
            $this->assertInstanceOf(InvoiceLineAllowance::class, $allowance);
        }
        $this->assertIsArray($ublObject->getCharges());
        $this->assertCount(1, $ublObject->getCharges());

        foreach ($ublObject->getCharges() as $charge) {
            $this->assertInstanceOf(InvoiceLineCharge::class, $charge);
        }
        $this->assertInstanceOf(Item::class, $ublObject->getItem());
        $this->assertInstanceOf(Price::class, $ublObject->getPrice());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObjects     = CreditNoteLine::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(1, $ublObjects);
        $ublObject = $ublObjects[0];
        $this->assertInstanceOf(CreditNoteLine::class, $ublObject);
        $this->assertInstanceOf(InvoiceLineIdentifier::class, $ublObject->getInvoiceLineIdentifier());
        $this->assertNull($ublObject->getNote());
        $this->assertInstanceOf(CreditedQuantity::class, $ublObject->getCreditedQuantity());
        $this->assertInstanceOf(LineExtensionAmount::class, $ublObject->getLineExtensionAmount());
        $this->assertNull($ublObject->getInvoicePeriod());
        $this->assertNull($ublObject->getOrderLineReference());
        $this->assertNull($ublObject->getDocumentReference());
        $this->assertIsArray($ublObject->getAllowances());
        $this->assertCount(0, $ublObject->getAllowances());
        $this->assertIsArray($ublObject->getCharges());
        $this->assertCount(0, $ublObject->getCharges());
        $this->assertInstanceOf(Item::class, $ublObject->getItem());
        $this->assertInstanceOf(Price::class, $ublObject->getPrice());
    }

    public function testCannotBeCreatedFromNoLine(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NO_LINE);
        CreditNoteLine::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromManyContents(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_CONTENTS);
        CreditNoteLine::fromXML($this->xpath, $currentElement);
    }

    public function testCanBeCreatedFromManyLines(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MANY_LINES);
        $ublObjects     = CreditNoteLine::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(2, $ublObjects);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObjects      = CreditNoteLine::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyCreditNoteRootDocument();

        foreach ($ublObjects as $ublObject) {
            $rootDestination->appendChild($ublObject->toXML($this->document));
        }
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
