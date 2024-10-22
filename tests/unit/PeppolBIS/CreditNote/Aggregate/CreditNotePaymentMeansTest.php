<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\PeppolBIS\CreditNote\Aggregate;

use Tiime\UniversalBusinessLanguage\PeppolBIS\CreditNote\DataType\Aggregate\CardAccount;
use Tiime\UniversalBusinessLanguage\PeppolBIS\CreditNote\DataType\Aggregate\PayeeFinancialAccount;
use Tiime\UniversalBusinessLanguage\PeppolBIS\CreditNote\DataType\Aggregate\PaymentMandate;
use Tiime\UniversalBusinessLanguage\PeppolBIS\CreditNote\DataType\Aggregate\PaymentMeans;
use Tiime\UniversalBusinessLanguage\PeppolBIS\CreditNote\DataType\Basic\PaymentDueDate;
use Tiime\UniversalBusinessLanguage\PeppolBIS\CreditNote\DataType\Basic\PaymentMeansNamedCode;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class CreditNotePaymentMeansTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PaymentMeans>
    <cbc:PaymentMeansCode name="Credit transfer">30</cbc:PaymentMeansCode>
    <cbc:PaymentDueDate>2017-11-01</cbc:PaymentDueDate>
    <cbc:PaymentID>632948234234234</cbc:PaymentID>
    <cac:CardAccount>
      <cbc:PrimaryAccountNumberID>1234</cbc:PrimaryAccountNumberID>
      <cbc:NetworkID>NA</cbc:NetworkID>
      <cbc:HolderName>John Doe</cbc:HolderName>
    </cac:CardAccount>
    <cac:PayeeFinancialAccount>
      <cbc:ID>NO99991122222</cbc:ID>
      <cbc:Name>Payment Account</cbc:Name>
      <cac:FinancialInstitutionBranch>
        <cbc:ID>9999</cbc:ID>
      </cac:FinancialInstitutionBranch>
    </cac:PayeeFinancialAccount>
    <cac:PaymentMandate>
      <cbc:ID>123456</cbc:ID>
      <cac:PayerFinancialAccount>
        <cbc:ID>12345676543</cbc:ID>
      </cac:PayerFinancialAccount>
    </cac:PaymentMandate>
  </cac:PaymentMeans>
</CreditNote>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PaymentMeans>
    <cbc:PaymentMeansCode>30</cbc:PaymentMeansCode>
  </cac:PaymentMeans>
</CreditNote>
XML;

    protected const XML_VALID_NO_LINE = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</CreditNote>
XML;

    protected const XML_INVALID_MANY_CONTENTS = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PaymentMeans>
    <cbc:PaymentMeansCode>30</cbc:PaymentMeansCode>
    <cbc:PaymentMeansCode>30</cbc:PaymentMeansCode>
  </cac:PaymentMeans>
</CreditNote>
XML;
    protected const XML_VALID_MANY_LINES = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PaymentMeans>
    <cbc:PaymentMeansCode>30</cbc:PaymentMeansCode>
  </cac:PaymentMeans>
  <cac:PaymentMeans>
    <cbc:PaymentMeansCode>30</cbc:PaymentMeansCode>
  </cac:PaymentMeans>
</CreditNote>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObjects     = PaymentMeans::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(1, $ublObjects);
        $ublObject = $ublObjects[0];
        $this->assertInstanceOf(PaymentMeans::class, $ublObject);
        $this->assertInstanceOf(PaymentDueDate::class, $ublObject->getPaymentDueDate());
        $this->assertInstanceOf(PaymentMeansNamedCode::class, $ublObject->getPaymentMeansCode());
        $this->assertInstanceOf(CardAccount::class, $ublObject->getCardAccount());
        $this->assertInstanceOf(PayeeFinancialAccount::class, $ublObject->getPayeeFinancialAccount());
        $this->assertInstanceOf(PaymentMandate::class, $ublObject->getPaymentMandate());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObjects     = PaymentMeans::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(1, $ublObjects);
        $ublObject = $ublObjects[0];
        $this->assertInstanceOf(PaymentMeans::class, $ublObject);
        $this->assertInstanceOf(PaymentMeansNamedCode::class, $ublObject->getPaymentMeansCode());
        $this->assertNull($ublObject->getCardAccount());
        $this->assertNull($ublObject->getPayeeFinancialAccount());
        $this->assertNull($ublObject->getPaymentMandate());
    }

    public function testCanBeCreatedFromNoLine(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_NO_LINE);
        $ublObjects     = PaymentMeans::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(0, $ublObjects);
    }

    public function testCannotBeCreatedFromManyContents(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_CONTENTS);
        PaymentMeans::fromXML($this->xpath, $currentElement);
    }

    public function testCanBeCreatedFromManyLines(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MANY_LINES);
        $ublObjects     = PaymentMeans::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(2, $ublObjects);

        foreach ($ublObjects as $ublObject) {
            $this->assertInstanceOf(PaymentMeans::class, $ublObject);
        }
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObjects      = PaymentMeans::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyCreditNoteRootDocument();

        foreach ($ublObjects as $ublObject) {
            $rootDestination->appendChild($ublObject->toXML($this->document));
        }
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
