<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Basic;

use Tiime\EN16931\DataType\PaymentMeansCode;
use Tiime\UniversalBusinessLanguage\DataType\Basic\PaymentMeansNamedCode;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class PaymentMeansNamedCodeTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:PaymentMeansCode name="Virement">30</cbc:PaymentMeansCode>
</Invoice>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:PaymentMeansCode>30</cbc:PaymentMeansCode>
</Invoice>
XML;

    protected const XML_INVALID_CODE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:PaymentMeansCode>AZ42</cbc:PaymentMeansCode>
</Invoice>
XML;

    protected const XML_INVALID_MULTIPLE_CODES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cbc:PaymentMeansCode name="Virement">30</cbc:PaymentMeansCode>
  <cbc:PaymentMeansCode name="Virement">30</cbc:PaymentMeansCode>
</Invoice>
XML;

    protected const XML_INVALID_NO_CODE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject      = PaymentMeansNamedCode::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(PaymentMeansNamedCode::class, $ublObject);
        $this->assertEquals(PaymentMeansCode::tryFrom(30), $ublObject->getPaymentMeansCode());
        $this->assertEquals('Virement', $ublObject->getName());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject      = PaymentMeansNamedCode::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(PaymentMeansNamedCode::class, $ublObject);
        $this->assertEquals(PaymentMeansCode::tryFrom(30), $ublObject->getPaymentMeansCode());
        $this->assertNull($ublObject->getName());
    }

    public function testCannotBeCreatedFromInvalidCode(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_CODE);
        PaymentMeansNamedCode::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromMultipleCodes(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MULTIPLE_CODES);
        PaymentMeansNamedCode::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromNoCode(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NO_CODE);
        PaymentMeansNamedCode::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject       = PaymentMeansNamedCode::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
