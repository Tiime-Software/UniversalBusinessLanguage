<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Aggregate;

use Tiime\EN16931\DataType\Identifier\PaymentAccountIdentifier;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\FinancialInstitutionBranch;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\PayeeFinancialAccount;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class PayeeFinancialAccountTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PayeeFinancialAccount>
    <cbc:ID>NO99991122222</cbc:ID>
    <cbc:Name>Payment Account</cbc:Name>
    <cac:FinancialInstitutionBranch>
      <cbc:ID>9999</cbc:ID>
    </cac:FinancialInstitutionBranch>
  </cac:PayeeFinancialAccount>
</Invoice>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
    <cac:PayeeFinancialAccount>
      <cbc:ID>NO99991122222</cbc:ID>
    </cac:PayeeFinancialAccount>
</Invoice>
XML;


    protected const XML_VALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_MULTIPLE_CONTENTS = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
    <cac:PayeeFinancialAccount>
      <cbc:ID>NO99991122222</cbc:ID>
      <cbc:ID>NO99991122223</cbc:ID>
    </cac:PayeeFinancialAccount>
</Invoice>
XML;
    protected const XML_INVALID_MULTIPLE_LINES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
    <cac:PayeeFinancialAccount>
      <cbc:ID>NO99991122222</cbc:ID>
    </cac:PayeeFinancialAccount>
    <cac:PayeeFinancialAccount>
      <cbc:ID>NO99991122223</cbc:ID>
    </cac:PayeeFinancialAccount>
</Invoice>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject = PayeeFinancialAccount::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(PayeeFinancialAccount::class, $ublObject);
        $this->assertInstanceOf(PaymentAccountIdentifier::class, $ublObject->getPaymentAccountIdentifier());
        $this->assertEquals("Payment Account", $ublObject->getPaymentAccountName());
        $this->assertInstanceOf(FinancialInstitutionBranch::class, $ublObject->getFinancialInstitutionBranch());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject = PayeeFinancialAccount::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(PayeeFinancialAccount::class, $ublObject);
        $this->assertInstanceOf(PaymentAccountIdentifier::class, $ublObject->getPaymentAccountIdentifier());
        $this->assertNull($ublObject->getPaymentAccountName());
        $this->assertNull($ublObject->getFinancialInstitutionBranch());

    }

    public function testCanBeCreatedFromNoLine(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_NO_LINE);
        $ublObject = PayeeFinancialAccount::fromXML($this->xpath, $currentElement);
        $this->assertNull($ublObject);
    }

    public function testCannotBeCreatedFromMultipleContents(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MULTIPLE_CONTENTS);
        PayeeFinancialAccount::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromMultipleLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MULTIPLE_LINES);
        PayeeFinancialAccount::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject = PayeeFinancialAccount::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertEquals(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}