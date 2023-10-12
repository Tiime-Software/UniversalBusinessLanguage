<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Aggregate;

use Tiime\EN16931\DataType\Identifier\MandateReferenceIdentifier;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\PayerFinancialAccount;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\PaymentMandate;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class PaymentMandateTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PaymentMandate>
    <cbc:ID>123456</cbc:ID>
    <cac:PayerFinancialAccount>
      <cbc:ID>12345676543</cbc:ID>
    </cac:PayerFinancialAccount>
  </cac:PaymentMandate>
</Invoice>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PaymentMandate>
  </cac:PaymentMandate>
</Invoice>
XML;

    protected const XML_VALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_MANY_CONTENTS = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PaymentMandate>
    <cbc:ID>123456</cbc:ID>
    <cbc:ID>123456</cbc:ID>
  </cac:PaymentMandate>
</Invoice>
XML;

    protected const XML_INVALID_MANY_LINES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PaymentMandate>
    <cbc:ID>123456</cbc:ID>
  </cac:PaymentMandate>
  <cac:PaymentMandate>
    <cbc:ID>123456</cbc:ID>
  </cac:PaymentMandate>
</Invoice>
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
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertEquals(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
