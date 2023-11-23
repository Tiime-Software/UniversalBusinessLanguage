<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\CreditNote\Aggregate;

use Tiime\EN16931\DataType\Identifier\MandateReferenceIdentifier;
use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Aggregate\PayerFinancialAccount;
use Tiime\UniversalBusinessLanguage\CreditNote\DataType\Aggregate\PaymentMandate;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class CreditNotePaymentMandateTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PaymentMandate>
    <cbc:ID>123456</cbc:ID>
    <cac:PayerFinancialAccount>
      <cbc:ID>12345676543</cbc:ID>
    </cac:PayerFinancialAccount>
  </cac:PaymentMandate>
</CreditNote>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PaymentMandate>
  </cac:PaymentMandate>
</CreditNote>
XML;

    protected const XML_VALID_NO_LINE = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</CreditNote>
XML;

    protected const XML_INVALID_MANY_CONTENTS = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PaymentMandate>
    <cbc:ID>123456</cbc:ID>
    <cbc:ID>123456</cbc:ID>
  </cac:PaymentMandate>
</CreditNote>
XML;

    protected const XML_INVALID_MANY_LINES = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PaymentMandate>
    <cbc:ID>123456</cbc:ID>
  </cac:PaymentMandate>
  <cac:PaymentMandate>
    <cbc:ID>123456</cbc:ID>
  </cac:PaymentMandate>
</CreditNote>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject      = PaymentMandate::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(PaymentMandate::class, $ublObject);
        $this->assertInstanceOf(MandateReferenceIdentifier::class, $ublObject->getIdentifier());
        $this->assertInstanceOf(PayerFinancialAccount::class, $ublObject->getPayerFinancialAccount());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject      = PaymentMandate::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(PaymentMandate::class, $ublObject);
        $this->assertNull($ublObject->getIdentifier());
        $this->assertNull($ublObject->getPayerFinancialAccount());
    }

    public function testCanBeCreatedFromNoLine(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_NO_LINE);
        $ublObject      = PaymentMandate::fromXML($this->xpath, $currentElement);
        $this->assertNull($ublObject);
    }

    public function testCannotBeCreatedFromManyContents(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_CONTENTS);
        PaymentMandate::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_LINES);
        PaymentMandate::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject       = PaymentMandate::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyCreditNoteRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
