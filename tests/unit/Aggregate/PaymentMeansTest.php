<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Aggregate;

use Tiime\UniversalBusinessLanguage\DataType\Aggregate\CardAccount;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\PayeeFinancialAccount;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\PaymentMandate;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\PaymentMeans;
use Tiime\UniversalBusinessLanguage\DataType\Basic\PaymentMeansNamedCode;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class PaymentMeansTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PaymentMeans>
    <cbc:PaymentMeansCode name="Credit transfer">30</cbc:PaymentMeansCode>
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
</Invoice>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PaymentMeans>
    <cbc:PaymentMeansCode>30</cbc:PaymentMeansCode>
  </cac:PaymentMeans>
</Invoice>
XML;


    protected const XML_VALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_MULTIPLE_CONTENTS = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PaymentMeans>
    <cbc:PaymentMeansCode>30</cbc:PaymentMeansCode>
    <cbc:PaymentMeansCode>30</cbc:PaymentMeansCode>
  </cac:PaymentMeans>
</Invoice>
XML;
    protected const XML_VALID_MULTIPLE_LINES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PaymentMeans>
    <cbc:PaymentMeansCode>30</cbc:PaymentMeansCode>
  </cac:PaymentMeans>
  <cac:PaymentMeans>
    <cbc:PaymentMeansCode>30</cbc:PaymentMeansCode>
  </cac:PaymentMeans>
</Invoice>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObjects = PaymentMeans::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(1, $ublObjects);
        $ublObject = $ublObjects[0];
        $this->assertInstanceOf(PaymentMeans::class, $ublObject);
        $this->assertInstanceOf(PaymentMeansNamedCode::class, $ublObject->getPaymentMeansCode());
        $this->assertInstanceOf(CardAccount::class, $ublObject->getCardAccount());
        $this->assertInstanceOf(PayeeFinancialAccount::class, $ublObject->getPayeeFinancialAccount());
        $this->assertInstanceOf(PaymentMandate::class, $ublObject->getPaymentMandate());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObjects = PaymentMeans::fromXML($this->xpath, $currentElement);
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
        $ublObjects = PaymentMeans::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(0, $ublObjects);
    }

    public function testCannotBeCreatedFromMultipleContents(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MULTIPLE_CONTENTS);
        PaymentMeans::fromXML($this->xpath, $currentElement);
    }

    public function testCanBeCreatedFromMultipleLines(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MULTIPLE_LINES);
        $ublObjects = PaymentMeans::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(2, $ublObjects);
        foreach($ublObjects as $ublObject) {
            $this->assertInstanceOf(PaymentMeans::class, $ublObject);
        }
    }

    public function testGenerateXml(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObjects = PaymentMeans::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        foreach($ublObjects as $ublObject) {
            $rootDestination->appendChild($ublObject->toXML($this->document));
        }
        $generatedOutput = $this->formatXMLOutput();
        $this->assertEquals(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}